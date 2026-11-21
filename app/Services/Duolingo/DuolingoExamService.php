<?php

namespace App\Services\Duolingo;

use App\Models\Duolingo\DuolingoAnswer;
use App\Models\Duolingo\DuolingoAttempt;
use App\Models\Duolingo\DuolingoModule;
use App\Models\Duolingo\DuolingoModuleWiseQuestion;
use App\Models\Module\Module;
use App\Repositories\Interfaces\Duolingo\DuolingoAnswerRepositoryInterface;
use App\Repositories\Interfaces\Duolingo\DuolingoAttemptRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DuolingoExamService
{
    public function __construct(
        private readonly DuolingoAttemptRepositoryInterface $attempts,
        private readonly DuolingoAnswerRepositoryInterface  $answers,
        private readonly DuolingoAnswerScorer               $scorer,
        private readonly DuolingoEvaluationService          $evaluations,
    ) {}

    /** Return (or create) the in-progress attempt for the given user + module. */
    public function startOrResumeAttempt(int $userId, int $moduleId): DuolingoAttempt
    {
        $existing = $this->attempts->currentInProgress($userId, $moduleId);
        if ($existing) return $existing;

        return $this->attempts->create([
            'user_id'          => $userId,
            'module_id'        => $moduleId,
            'status'           => DuolingoAttempt::STATUS_IN_PROGRESS,
            'started_at'       => now(),
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Full exam structure for a Module, ordered by section then question display_order:
     *   [ DuolingoModule{ section, moduleQuestions[ question{ type, group, options, blanks, turns } ] } ]
     */
    public function loadStructure(Module $module): Collection
    {
        return DuolingoModule::with([
                'section',
                'moduleQuestions' => fn ($q) => $q
                    ->where('is_active', true)
                    ->with(['question.type', 'question.group', 'question.options', 'question.blanks', 'question.turns'])
                    ->orderBy('display_order'),
            ])
            ->where('module_id', $module->id)
            ->get()
            ->sortBy(fn ($dm) => $dm->section?->display_order ?? 999)
            ->values();
    }

    /**
     * Prior answers for this attempt, keyed by mapping id.
     * @return array<int, DuolingoAnswer>
     */
    public function answersByMapping(int $attemptId): array
    {
        return $this->answers->byAttempt($attemptId)
            ->keyBy('duolingo_module_wise_question_id')
            ->all();
    }

    /** An active question of the attempt's own module, or null. */
    public function findMapping(DuolingoAttempt $attempt, int $mappingId): ?DuolingoModuleWiseQuestion
    {
        return DuolingoModuleWiseQuestion::where('id', $mappingId)
            ->where('is_active', true)
            ->whereHas('duolingoModule', fn ($q) => $q->where('module_id', $attempt->module_id))
            ->with(['question.type', 'question.options', 'question.blanks', 'question.highlights', 'question.group'])
            ->first();
    }

    /**
     * Stores a response and marks it straight away when it is objective.
     * $payload holds only the columns for this question's response type.
     */
    public function saveAnswer(DuolingoAttempt $attempt, DuolingoModuleWiseQuestion $mapping, array $payload): DuolingoAnswer
    {
        return DB::transaction(function () use ($attempt, $mapping, $payload) {
            $answer = $this->answers->upsertForMapping(
                $attempt->id,
                $mapping->id,
                (int) $mapping->duolingo_question_id,
                $payload,
            );

            $answer->update($this->scorer->score($mapping->question, $answer));
            $attempt->update(['last_activity_at' => now()]);

            return $answer->fresh();
        });
    }

    /** Remembers where the student is, so a refresh or a later visit resumes there. */
    public function saveState(DuolingoAttempt $attempt, array $state): void
    {
        $attempt->update(['state' => $state, 'last_activity_at' => now()]);
    }

    /** Closes the attempt and sends writing and speaking to an evaluator. */
    public function submit(DuolingoAttempt $attempt): DuolingoAttempt
    {
        return DB::transaction(function () use ($attempt) {
            $attempt->update([
                'status'           => DuolingoAttempt::STATUS_COMPLETED,
                'ended_at'         => now(),
                'last_activity_at' => now(),
            ]);

            return $this->evaluations->open($attempt->fresh());
        });
    }
}
