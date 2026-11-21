<?php

namespace App\Services\Duolingo;

use App\Models\Duolingo\DuolingoAnswer;
use App\Models\Duolingo\DuolingoQuestion;

/**
 * Objective marking (duolingo.md §6 A): correct = 1, incorrect = 0, partial = 0.5,
 * scaled by the question's marks. Writing and speaking (§6 B) are left for an evaluator.
 */
class DuolingoAnswerScorer
{
    /** Dictation word accuracy needed for full and for partial credit. */
    private const DICTATION_FULL    = 1.0;
    private const DICTATION_PARTIAL = 0.7;

    /** Word-overlap F1 needed for a highlight to count as correct or partially correct. */
    private const HIGHLIGHT_FULL    = 0.9;
    private const HIGHLIGHT_PARTIAL = 0.5;

    public function __construct(private readonly DuolingoGradingService $grading) {}

    /**
     * @return array{raw_score: ?float, max_score: float, is_correct: ?bool, score_detail: ?array, evaluated_at: mixed}
     */
    public function score(DuolingoQuestion $question, DuolingoAnswer $answer): array
    {
        $marks = (float) $question->marks;

        if (!$question->type->isObjective()) {
            // Rubric item: an evaluator scores it later. A new response clears any earlier score.
            return [
                'raw_score'     => null,
                'max_score'     => $marks,
                'is_correct'    => null,
                'score_detail'  => null,
                'rubric_scores' => null,
                'evaluated_at'  => null,
                'evaluated_by'  => null,
            ];
        }

        [$result, $detail, $raw] = match ($question->type->response_type) {
            'fill_in_blanks'      => $this->blanks($question, $answer),
            'word_classification' => $this->wordClassification($question, $answer),
            'mcq_single'          => $this->singleChoice($question, $answer),
            'highlighting'        => $this->highlight($question, $answer),
            'dictation'           => $this->dictation($question, $answer),
            default               => [DuolingoGradingService::RESULT_INCORRECT, null, null],
        };

        return [
            'raw_score'    => $raw ?? $this->grading->objectiveMark($result, $marks),
            'max_score'    => $marks,
            'is_correct'   => $result === DuolingoGradingService::RESULT_CORRECT,
            'score_detail' => ($detail ?? []) + ['result' => $result],
            'evaluated_at' => now(),
        ];
    }

    /* ── Per response type ──────────────────────────────────────────── */

    /** Each blank is worth one mark, so the raw score is the number of correct blanks. */
    private function blanks(DuolingoQuestion $question, DuolingoAnswer $answer): array
    {
        $given   = $answer->response_blanks ?? [];
        $correct = [];

        foreach ($question->blanks as $blank) {
            $typed    = $this->normalize((string) ($given[(string) $blank->blank_order] ?? ''));
            $accepted = array_map(
                fn ($v) => $this->normalize((string) $v),
                array_merge([$blank->correct_answer], $blank->accepted_variants ?? []),
            );
            $prefix = $this->normalize((string) $blank->visible_prefix);

            // A student who retypes the whole word ("practice" instead of "ice") is also right.
            $ok = $typed !== '' && (
                in_array($typed, $accepted, true)
                || ($prefix !== '' && in_array($typed, array_map(fn ($a) => $prefix . $a, $accepted), true))
            );
            $correct[$blank->blank_order] = $ok;
        }

        $count = count(array_filter($correct));
        $total = count($correct);
        $result = match (true) {
            $total > 0 && $count === $total => DuolingoGradingService::RESULT_CORRECT,
            $count > 0                      => DuolingoGradingService::RESULT_PARTIAL,
            default                         => DuolingoGradingService::RESULT_INCORRECT,
        };

        $perBlank = $total > 0 ? (float) $question->marks / $total : 0.0;

        return [$result, ['blanks' => $correct, 'correct' => $count, 'total' => $total], round($count * $perBlank, 2)];
    }

    private function wordClassification(DuolingoQuestion $question, DuolingoAnswer $answer): array
    {
        $ok = strtolower(trim((string) $answer->response_text)) === strtolower(trim((string) $question->correct_ans));

        return [$ok ? DuolingoGradingService::RESULT_CORRECT : DuolingoGradingService::RESULT_INCORRECT, null, null];
    }

    private function singleChoice(DuolingoQuestion $question, DuolingoAnswer $answer): array
    {
        $selected = (int) (($answer->selected_option_ids ?? [])[0] ?? 0);
        $ok       = (bool) $question->options->firstWhere('id', $selected)?->is_correct;

        return [$ok ? DuolingoGradingService::RESULT_CORRECT : DuolingoGradingService::RESULT_INCORRECT, null, null];
    }

    /**
     * Compared by words rather than character offsets, so small differences in where a
     * selection starts or ends do not matter. The selection must come from the passage.
     */
    private function highlight(DuolingoQuestion $question, DuolingoAnswer $answer): array
    {
        $selected = $this->normalize((string) (($answer->response_highlight ?? [])['text'] ?? ''));
        $passage  = $this->normalize((string) $question->group?->passage_text);

        if ($selected === '' || !str_contains($passage, $selected)) {
            return [DuolingoGradingService::RESULT_INCORRECT, ['overlap' => 0.0], null];
        }

        $best = 0.0;
        foreach ($question->highlights as $key) {
            $best = max($best, $this->wordF1($selected, $this->normalize($key->answer_text)));
        }

        $result = match (true) {
            $best >= self::HIGHLIGHT_FULL    => DuolingoGradingService::RESULT_CORRECT,
            $best >= self::HIGHLIGHT_PARTIAL => DuolingoGradingService::RESULT_PARTIAL,
            default                          => DuolingoGradingService::RESULT_INCORRECT,
        };

        return [$result, ['overlap' => round($best, 2)], null];
    }

    /** Word accuracy against the transcript or an accepted variant, ignoring case and punctuation. */
    private function dictation(DuolingoQuestion $question, DuolingoAnswer $answer): array
    {
        $typed = $this->words((string) $answer->response_text);
        $best  = 0.0;

        foreach (array_merge([$question->correct_ans], $question->accepted_variants ?? []) as $expected) {
            $target = $this->words((string) $expected);
            if (!$target) continue;
            $distance = $this->wordDistance($typed, $target);
            $best = max($best, max(0.0, 1 - $distance / count($target)));
        }

        $result = match (true) {
            $best >= self::DICTATION_FULL    => DuolingoGradingService::RESULT_CORRECT,
            $best >= self::DICTATION_PARTIAL => DuolingoGradingService::RESULT_PARTIAL,
            default                          => DuolingoGradingService::RESULT_INCORRECT,
        };

        return [$result, ['accuracy' => round($best, 2)], null];
    }

    /* ── Text helpers ───────────────────────────────────────────────── */

    /** Lower case, straight quotes, punctuation removed (apostrophes kept), single spaces. */
    private function normalize(string $text): string
    {
        $text = mb_strtolower(str_replace(['’', '‘'], "'", $text));
        $text = preg_replace("/[^\p{L}\p{N}\s']+/u", ' ', $text);

        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    /** @return array<int, string> */
    private function words(string $text): array
    {
        $normalized = $this->normalize($text);

        return $normalized === '' ? [] : explode(' ', $normalized);
    }

    /** Word-level edit distance (insertions, deletions and substitutions). */
    private function wordDistance(array $a, array $b): int
    {
        $prev = range(0, count($b));
        foreach ($a as $i => $wa) {
            $curr = [$i + 1];
            foreach ($b as $j => $wb) {
                $curr[$j + 1] = min($prev[$j + 1] + 1, $curr[$j] + 1, $prev[$j] + ($wa === $wb ? 0 : 1));
            }
            $prev = $curr;
        }

        return $prev[count($b)];
    }

    /** F1 of the two word multisets. */
    private function wordF1(string $selected, string $expected): float
    {
        $s = array_count_values($this->words($selected));
        $e = array_count_values($this->words($expected));
        if (!$s || !$e) return 0.0;

        $common = 0;
        foreach ($s as $word => $n) {
            $common += min($n, $e[$word] ?? 0);
        }
        if ($common === 0) return 0.0;

        $precision = $common / array_sum($s);
        $recall    = $common / array_sum($e);

        return 2 * $precision * $recall / ($precision + $recall);
    }
}
