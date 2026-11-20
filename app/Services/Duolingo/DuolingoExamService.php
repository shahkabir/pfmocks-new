<?php

namespace App\Services\Duolingo;

use App\Models\Duolingo\DuolingoAnswer;
use App\Models\Duolingo\DuolingoAttempt;
use App\Models\Duolingo\DuolingoModule;
use App\Models\Module\Module;
use App\Repositories\Interfaces\Duolingo\DuolingoAnswerRepositoryInterface;
use App\Repositories\Interfaces\Duolingo\DuolingoAttemptRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DuolingoExamService
{
    public function __construct(
        private readonly DuolingoAttemptRepositoryInterface $attempts,
        private readonly DuolingoAnswerRepositoryInterface  $answers,
        private readonly DuolingoGradingService             $grading,
    ) {}

    /** Return (or create) the in-progress attempt for the given user + module. */
    public function startOrResumeAttempt(int $userId, int $moduleId): DuolingoAttempt
    {
        $existing = $this->attempts->currentInProgress($userId, $moduleId);
        if ($existing) return $existing;

        return $this->attempts->create([
            'user_id'    => $userId,
            'module_id'  => $moduleId,
            'status'     => DuolingoAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
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

    /** Close the attempt and store the grading report (raw + estimated). */
    public function submit(int $attemptId): DuolingoAttempt
    {
        /** @var DuolingoAttempt $attempt */
        $attempt = $this->attempts->find($attemptId);

        $answers = $attempt->answers()->with('question.type.section')->get();
        $report  = $this->grading->report($answers);

        $attempt->update([
            'status'              => DuolingoAttempt::STATUS_COMPLETED,
            'ended_at'            => now(),
            'raw_scores'          => $report['raw'],
            'reading_score'       => $report['individual']['reading']   ?? null,
            'writing_score'       => $report['individual']['writing']   ?? null,
            'listening_score'     => $report['individual']['listening'] ?? null,
            'speaking_score'      => $report['individual']['speaking']  ?? null,
            'literacy_score'      => $report['integrated']['literacy']      ?? null,
            'comprehension_score' => $report['integrated']['comprehension'] ?? null,
            'conversation_score'  => $report['integrated']['conversation']  ?? null,
            'production_score'    => $report['integrated']['production']    ?? null,
            'overall_score'       => $report['overall'],
        ]);

        return $attempt->fresh();
    }
}
