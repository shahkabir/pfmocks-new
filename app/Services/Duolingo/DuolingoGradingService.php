<?php

namespace App\Services\Duolingo;

use App\Models\Duolingo\DuolingoAnswer;
use App\Models\Duolingo\DuolingoQuestionType;

/**
 * Duolingo mock grading model (duolingo.md §5–§6).
 *
 *  - Objective items: correct = 1, incorrect = 0, partial = 0.5 (× question marks).
 *  - Writing / speaking: weighted rubric (relevance 25, grammar 25, vocabulary 20,
 *    coherence 20, fluency 10), each criterion marked 0..rubric_criterion_max.
 *  - Interactive Reading / Listening: subtasks scored independently, then summed.
 *  - Raw performance (earned / max per skill) is kept SEPARATE from the
 *    estimated proficiency (10–160, step 5).
 *  - Integrated subscores = average of two individual subscores.
 *  - Overall = average of the four individual subscores, rounded to nearest 5.
 *
 * This is an internal preparation model, not Duolingo's proprietary IRT scoring.
 */
class DuolingoGradingService
{
    public const RESULT_CORRECT   = 'correct';
    public const RESULT_PARTIAL   = 'partial';
    public const RESULT_INCORRECT = 'incorrect';

    /* ── §6 A · Objective items ─────────────────────────────────────── */

    /** Marks earned for an objective item. $result is correct / partial / incorrect. */
    public function objectiveMark(string $result, float $marks = 1.0): float
    {
        $factor = (float) config("duolingo.grading.objective.{$result}", 0);
        return round($factor * $marks, 2);
    }

    /* ── §6 B · Rubric items (writing / speaking) ───────────────────── */

    /**
     * Weighted rubric result as a fraction 0..1.
     *
     * @param string               $skill  'writing' | 'speaking'
     * @param array<string, float> $scores criterion => 0..rubric_criterion_max
     */
    public function rubricFraction(string $skill, array $scores): float
    {
        $weights = config("duolingo.grading.rubric.{$skill}", []);
        $max     = (float) config('duolingo.grading.rubric_criterion_max', 5);
        $total   = array_sum($weights);

        if ($total <= 0 || $max <= 0) {
            return 0.0;
        }

        $weighted = 0.0;
        foreach ($weights as $criterion => $weight) {
            $value     = max(0.0, min($max, (float) ($scores[$criterion] ?? 0)));
            $weighted += ($value / $max) * $weight;
        }

        return round($weighted / $total, 4);
    }

    /** Marks earned for a rubric item. */
    public function rubricMark(string $skill, array $scores, float $marks = 1.0): float
    {
        return round($this->rubricFraction($skill, $scores) * $marks, 2);
    }

    /** Rubric skill to use for a question type ('writing' for typed, 'speaking' for recorded). */
    public function rubricSkillFor(DuolingoQuestionType $type): string
    {
        return in_array($type->response_type, ['speaking', 'interactive_speaking'], true)
            ? 'speaking'
            : 'writing';
    }

    /* ── §6 C · Interactive tasks ───────────────────────────────────── */

    /**
     * Aggregate independently scored subtasks of one Interactive Reading / Listening activity.
     *
     * @param  iterable<DuolingoAnswer> $answers
     * @return array{earned: float, max: float, percent: float|null}
     */
    public function aggregate(iterable $answers): array
    {
        $earned = 0.0;
        $max    = 0.0;
        foreach ($answers as $a) {
            if ($a->max_score === null) continue; // not evaluated yet
            $earned += (float) $a->raw_score;
            $max    += (float) $a->max_score;
        }

        return [
            'earned'  => round($earned, 2),
            'max'     => round($max, 2),
            'percent' => $max > 0 ? round($earned / $max * 100, 2) : null,
        ];
    }

    /* ── Raw performance per skill ──────────────────────────────────── */

    /**
     * Raw earned / max per individual skill, over every question in the test.
     * Unanswered questions count as 0 out of their marks.
     *
     * @param  iterable<array{skill: string, earned: float, max: float}> $rows
     * @return array<string, array{earned: float, max: float, percent: float|null}>
     */
    public function rawBySkill(iterable $rows): array
    {
        $out = [];
        foreach (array_values(config('duolingo.grading.skills')) as $skill) {
            $out[$skill] = ['earned' => 0.0, 'max' => 0.0];
        }

        foreach ($rows as $row) {
            if (!isset($out[$row['skill']])) continue;
            $out[$row['skill']]['earned'] += (float) $row['earned'];
            $out[$row['skill']]['max']    += (float) $row['max'];
        }

        foreach ($out as $skill => $r) {
            $out[$skill] = [
                'earned'  => round($r['earned'], 2),
                'max'     => round($r['max'], 2),
                'percent' => $r['max'] > 0 ? round($r['earned'] / $r['max'] * 100, 2) : null,
            ];
        }

        return $out;
    }

    /* ── §5 · Estimated proficiency (10–160) ────────────────────────── */

    /**
     * Estimated individual subscore from a raw percentage.
     * Linear placeholder mapping onto the reporting scale; replace with a
     * calibrated / difficulty-weighted model when one is available.
     */
    public function estimateSubscore(?float $percent): ?int
    {
        if ($percent === null) return null;

        $min = (int) config('duolingo.grading.scale.min', 10);
        $max = (int) config('duolingo.grading.scale.max', 160);

        $p = max(0.0, min(100.0, $percent));
        return $this->roundToScale($min + ($max - $min) * $p / 100);
    }

    /** Round to the nearest reporting increment (5) and clamp to 10–160. */
    public function roundToScale(float $value): int
    {
        $inc = (int) config('duolingo.grading.scale.increment', 5);
        $min = (int) config('duolingo.grading.scale.min', 10);
        $max = (int) config('duolingo.grading.scale.max', 160);

        $rounded = (int) (round($value / $inc) * $inc);
        return max($min, min($max, $rounded));
    }

    /**
     * Integrated subscores: average of their two individual subscores.
     *
     * @param  array<string, int|null> $individual reading/writing/listening/speaking
     * @return array<string, float|null>
     */
    public function integratedSubscores(array $individual): array
    {
        $out = [];
        foreach (config('duolingo.grading.integrated') as $name => [$a, $b]) {
            $out[$name] = (isset($individual[$a], $individual[$b]))
                ? ($individual[$a] + $individual[$b]) / 2
                : null;
        }
        return $out;
    }

    /**
     * Overall score: average of the four individual subscores, rounded to nearest 5.
     * Returns null unless all four skills have a subscore.
     */
    public function overallScore(array $individual): ?int
    {
        $skills = array_values(config('duolingo.grading.skills'));
        $values = [];
        foreach ($skills as $skill) {
            if (!isset($individual[$skill])) return null;
            $values[] = $individual[$skill];
        }
        return $this->roundToScale(array_sum($values) / count($values));
    }

    /**
     * Full report: raw performance kept separate from the estimated scores.
     *
     * @param  iterable<array{skill: string, earned: float, max: float}> $rows one row per question
     * @return array{raw: array, individual: array, integrated: array, overall: int|null}
     */
    public function report(iterable $rows): array
    {
        $raw = $this->rawBySkill($rows);

        $individual = [];
        foreach ($raw as $skill => $r) {
            $individual[$skill] = $this->estimateSubscore($r['percent']);
        }

        return [
            'raw'        => $raw,
            'individual' => $individual,
            'integrated' => $this->integratedSubscores($individual),
            'overall'    => $this->overallScore($individual),
        ];
    }
}
