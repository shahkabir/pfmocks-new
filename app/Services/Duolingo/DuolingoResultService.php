<?php

namespace App\Services\Duolingo;

use App\Models\Duolingo\DuolingoAnswer;
use App\Models\Duolingo\DuolingoAttempt;
use App\Models\Duolingo\DuolingoModuleWiseQuestion;
use Illuminate\Support\Collection;

/** Builds the per-question breakdown of an attempt and publishes its final scores. */
class DuolingoResultService
{
    public function __construct(private readonly DuolingoGradingService $grading) {}

    /**
     * Every active question in the attempt's module, in test order, with the student's answer.
     *
     * @return Collection<int, array{mapping: DuolingoModuleWiseQuestion, answer: ?DuolingoAnswer, skill: string, earned: float, max: float}>
     */
    public function rows(DuolingoAttempt $attempt): Collection
    {
        $answers = $attempt->answers()->get()->keyBy('duolingo_module_wise_question_id');
        $skills  = config('duolingo.grading.skills');

        return DuolingoModuleWiseQuestion::query()
            ->select('duolingo_module_wise_question.*')
            ->join('duolingo_module', 'duolingo_module.id', '=', 'duolingo_module_wise_question.duolingo_module_id')
            ->join('duolingo_sections', 'duolingo_sections.id', '=', 'duolingo_module.duolingo_section_id')
            ->where('duolingo_module.module_id', $attempt->module_id)
            ->where('duolingo_module_wise_question.is_active', true)
            ->orderBy('duolingo_sections.display_order')
            ->orderBy('duolingo_module_wise_question.display_order')
            ->with(['duolingoModule.section', 'question.type', 'question.options', 'question.blanks',
                    'question.highlights', 'question.group', 'question.turns'])
            ->get()
            ->map(function (DuolingoModuleWiseQuestion $mapping) use ($answers, $skills) {
                $answer = $answers->get($mapping->id);

                return [
                    'mapping' => $mapping,
                    'answer'  => $answer,
                    'skill'   => $skills[$mapping->duolingoModule->section->tag] ?? 'unknown',
                    'earned'  => (float) ($answer?->raw_score ?? 0),
                    'max'     => (float) $mapping->question->marks,
                ];
            });
    }

    /** Answers to writing and speaking questions that an evaluator still has to score. */
    public function unevaluatedRubricAnswers(DuolingoAttempt $attempt): Collection
    {
        return $this->rows($attempt)
            ->filter(fn ($row) => $row['answer'] && !$row['mapping']->question->type->isObjective()
                && !$row['answer']->isEvaluated())
            ->values();
    }

    /** Calculates the full report and makes it visible to the student. */
    public function publish(DuolingoAttempt $attempt): DuolingoAttempt
    {
        $report = $this->grading->report($this->rows($attempt));

        $attempt->update([
            'result_status'       => DuolingoAttempt::RESULT_PUBLISHED,
            'published_at'        => now(),
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
