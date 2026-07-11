<?php

namespace App\Services\Pte;

use App\Models\Module\Module;
use App\Models\Pte\PteAnswer;
use App\Models\Pte\PteAttempt;
use App\Models\Pte\PteModule;
use App\Repositories\Interfaces\Pte\PteAnswerRepositoryInterface;
use App\Repositories\Interfaces\Pte\PteAttemptRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PteExamService
{
    public function __construct(
        private readonly PteAttemptRepositoryInterface $attempts,
        private readonly PteAnswerRepositoryInterface  $answers,
    ) {}

    /**
     * Return (or create) the in-progress attempt for the given user + module.
     */
    public function startOrResumeAttempt(int $userId, int $moduleId): PteAttempt
    {
        $existing = $this->attempts->currentInProgress($userId, $moduleId);
        if ($existing) return $existing;

        return $this->attempts->create([
            'user_id'    => $userId,
            'module_id'  => $moduleId,
            'status'     => PteAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
        ]);
    }

    /**
     * Load the full exam structure for a Module:
     *   [ { pte_module, section, questions[] }, ... ]
     * ordered by section display_order, then by question display_order.
     */
    public function loadStructure(Module $module): Collection
    {
        return PteModule::with([
                'section',
                'moduleQuestions' => fn ($q) => $q
                    ->where('is_active', true)
                    ->with('question.subType')
                    ->orderBy('display_order'),
            ])
            ->where('module_id', $module->id)
            ->get()
            ->sortBy(fn ($pm) => $pm->section?->display_order ?? 999)
            ->values();
    }

    /**
     * Load the user's prior answers for this attempt, keyed by mapping id.
     * @return array<int, PteAnswer>
     */
    public function answersByMapping(int $attemptId): array
    {
        return $this->answers->byAttempt($attemptId)
            ->keyBy('pte_module_wise_question_id')
            ->all();
    }

    public function saveAnswer(int $attemptId, int $mappingId, int $questionId, array $payload): PteAnswer
    {
        $clean = collect($payload)->only([
            'response_text',
            'response_audio_url',
            'selected_option_ids',
            'response_blanks',
            'response_segments_order',
            'response_highlight_word_ids',
        ])->all();

        return $this->answers->upsertForMapping($attemptId, $mappingId, $questionId, $clean);
    }

    public function submit(int $attemptId): PteAttempt
    {
        $attempt = $this->attempts->find($attemptId);
        $attempt->update([
            'status'   => PteAttempt::STATUS_COMPLETED,
            'ended_at' => now(),
        ]);
        return $attempt->fresh();
    }
}
