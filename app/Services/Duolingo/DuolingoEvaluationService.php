<?php

namespace App\Services\Duolingo;

use App\Mail\Duolingo\DuolingoResultPublishedMail;
use App\Models\Duolingo\DuolingoAnswer;
use App\Models\Duolingo\DuolingoAttempt;
use App\Models\Duolingo\DuolingoEvaluation;
use App\Models\User;
use App\Services\Mail\Mailer;
use Illuminate\Support\Facades\DB;

/**
 * Evaluator review of writing and speaking responses (duolingo.md §6 B).
 * The final score is published only after every response has been scored.
 */
class DuolingoEvaluationService
{
    public function __construct(
        private readonly DuolingoResultService  $results,
        private readonly DuolingoGradingService $grading,
        private readonly Mailer                 $mailer,
    ) {}

    /**
     * Called on submit. With nothing to evaluate the result is published straight away;
     * otherwise the attempt goes to the evaluator with the fewest open reviews.
     */
    public function open(DuolingoAttempt $attempt): DuolingoAttempt
    {
        if ($this->results->unevaluatedRubricAnswers($attempt)->isEmpty()) {
            return $this->results->publish($attempt);
        }

        $evaluatorId = $this->leastLoadedEvaluatorId();

        DuolingoEvaluation::updateOrCreate(
            ['duolingo_attempt_id' => $attempt->id],
            [
                'assigned_to' => $evaluatorId,
                'assigned_at' => $evaluatorId ? now() : null,
                'status'      => DuolingoEvaluation::STATUS_PENDING,
            ],
        );

        $attempt->update(['result_status' => DuolingoAttempt::RESULT_PENDING_EVALUATION]);

        return $attempt->fresh();
    }

    /** Admins may open any review; evaluators their own or an unassigned one. */
    public function canOpen(DuolingoEvaluation $evaluation, User $user): bool
    {
        return $user->role === 'admin'
            || $evaluation->assigned_to === null
            || $evaluation->assigned_to === $user->id;
    }

    /** The first evaluator to open an unassigned review claims it. */
    public function start(DuolingoEvaluation $evaluation, User $user): DuolingoEvaluation
    {
        $changes = [];
        if ($evaluation->assigned_to === null) {
            $changes += ['assigned_to' => $user->id, 'assigned_at' => now()];
        }
        if ($evaluation->status === DuolingoEvaluation::STATUS_PENDING) {
            $changes += ['status' => DuolingoEvaluation::STATUS_IN_PROGRESS, 'started_at' => now()];
        }
        if ($changes) {
            $evaluation->update($changes);
        }

        return $evaluation->fresh();
    }

    /**
     * Saves rubric scores. An answer counts as evaluated once every criterion has a score.
     *
     * @param array<int, array<string, mixed>> $scores   answer id => criterion => 0..max
     * @param array<int, ?string>              $feedback answer id => comment
     * @return int number of answers fully scored by this call
     */
    public function saveScores(DuolingoEvaluation $evaluation, array $scores, array $feedback, int $evaluatorId): int
    {
        return DB::transaction(function () use ($evaluation, $scores, $feedback, $evaluatorId) {
            $answers = DuolingoAnswer::with('question.type')
                ->where('duolingo_attempt_id', $evaluation->duolingo_attempt_id)
                ->whereIn('id', array_keys($scores + $feedback))
                ->get();

            $max   = (float) config('duolingo.grading.rubric_criterion_max');
            $saved = 0;

            foreach ($answers as $answer) {
                $type = $answer->question->type;
                if ($type->isObjective()) continue;

                $skill    = $this->grading->rubricSkillFor($type);
                $criteria = array_keys(config("duolingo.grading.rubric.{$skill}"));

                $given = [];
                foreach ($criteria as $criterion) {
                    $value = $scores[$answer->id][$criterion] ?? null;
                    if ($value !== null && $value !== '' && is_numeric($value)) {
                        $given[$criterion] = max(0.0, min($max, (float) $value));
                    }
                }

                $complete = count($given) === count($criteria);
                $answer->update([
                    'rubric_scores' => $given ?: null,
                    'feedback'      => $feedback[$answer->id] ?? $answer->feedback,
                    'raw_score'     => $complete ? $this->grading->rubricMark($skill, $given, (float) $answer->question->marks) : null,
                    'evaluated_at'  => $complete ? now() : null,
                    'evaluated_by'  => $complete ? $evaluatorId : null,
                ]);
                $saved += $complete ? 1 : 0;
            }

            return $saved;
        });
    }

    /** Publishes the final score once every response is scored, then emails the student. */
    public function publish(DuolingoEvaluation $evaluation, ?string $feedbackText): DuolingoAttempt
    {
        $attempt = $evaluation->attempt;

        $missing = $this->results->unevaluatedRubricAnswers($attempt)->count();
        if ($missing > 0) {
            throw new \DomainException("{$missing} response(s) still need a score for every criterion.");
        }

        $attempt = DB::transaction(function () use ($evaluation, $attempt, $feedbackText) {
            $evaluation->update([
                'status'        => DuolingoEvaluation::STATUS_COMPLETED,
                'completed_at'  => now(),
                'feedback_text' => $feedbackText,
            ]);

            return $this->results->publish($attempt);
        });

        $student = $attempt->user;
        $this->mailer->send($student->email, new DuolingoResultPublishedMail(
            studentName:  $student->name,
            moduleName:   $attempt->module->name,
            overallScore: $attempt->overall_score,
            resultUrl:    route('duolingo.exam.result', $attempt->id),
        ));

        return $attempt;
    }

    private function leastLoadedEvaluatorId(): ?int
    {
        $openReviews = DuolingoEvaluation::selectRaw('count(*)')
            ->whereColumn('assigned_to', 'users.id')
            ->where('status', '!=', DuolingoEvaluation::STATUS_COMPLETED);

        return User::where('role', 'evaluator')
            ->select('users.id')
            ->addSelect(['open_reviews' => $openReviews])
            ->orderBy('open_reviews')
            ->orderBy('users.id')
            ->first()?->id;
    }
}
