<?php

namespace App\Http\Controllers\Duolingo;

use App\Http\Controllers\Controller;
use App\Models\Duolingo\DuolingoEvaluation;
use App\Services\Duolingo\DuolingoEvaluationService;
use App\Services\Duolingo\DuolingoGradingService;
use App\Services\Duolingo\DuolingoResultService;
use Illuminate\Http\Request;

/** Evaluator review of Duolingo writing and speaking responses. */
class DuolingoEvaluationController extends Controller
{
    public function __construct(
        private readonly DuolingoEvaluationService $evaluations,
        private readonly DuolingoResultService     $results,
        private readonly DuolingoGradingService    $grading,
    ) {}

    /** Evaluators see their own and unassigned reviews; admins see all. */
    public function index()
    {
        $user = auth()->user();

        $evaluations = DuolingoEvaluation::with(['attempt.user:id,name,email', 'attempt.module:id,name', 'evaluator:id,name'])
            ->when($user->role !== 'admin', fn ($q) => $q->where(fn ($w) => $w
                ->where('assigned_to', $user->id)->orWhereNull('assigned_to')))
            ->orderByRaw("FIELD(status, 'in_progress', 'pending', 'completed')")
            ->orderBy('id')
            ->get();

        return view('evaluator.duolingo.index', compact('evaluations'));
    }

    public function show(DuolingoEvaluation $evaluation)
    {
        abort_unless($this->evaluations->canOpen($evaluation, auth()->user()), 403);

        if (!$evaluation->isCompleted()) {
            $evaluation = $this->evaluations->start($evaluation, auth()->user());
        }

        $attempt = $evaluation->attempt()->with(['user:id,name,email', 'module:id,name'])->first();
        $rows    = $this->results->rows($attempt);

        $rubricRows    = $rows->filter(fn ($r) => !$r['mapping']->question->type->isObjective())->values();
        $objectiveRows = $rows->filter(fn ($r) => $r['mapping']->question->type->isObjective());

        return view('evaluator.duolingo.show', [
            'evaluation'    => $evaluation,
            'attempt'       => $attempt,
            'rubricRows'    => $rubricRows,
            'objective'     => [
                'earned' => $objectiveRows->sum('earned'),
                'max'    => $objectiveRows->sum('max'),
                'count'  => $objectiveRows->count(),
            ],
            'criteria'      => config('duolingo.grading.rubric'),
            'criterionMax'  => (float) config('duolingo.grading.rubric_criterion_max'),
            'grading'       => $this->grading,
        ]);
    }

    /** Saves rubric scores; with action=publish, also publishes the final result. */
    public function update(Request $request, DuolingoEvaluation $evaluation)
    {
        abort_unless($this->evaluations->canOpen($evaluation, auth()->user()), 403);
        abort_if($evaluation->isCompleted(), 422, 'This result has already been published.');

        $max  = (float) config('duolingo.grading.rubric_criterion_max');
        $data = $request->validate([
            'scores'        => 'nullable|array',
            'scores.*'      => 'array',
            'scores.*.*'    => "nullable|numeric|min:0|max:{$max}",
            'feedback'      => 'nullable|array',
            'feedback.*'    => 'nullable|string|max:5000',
            'feedback_text' => 'nullable|string|max:8000',
            'action'        => 'required|in:save,publish',
        ]);

        $this->evaluations->saveScores($evaluation, $data['scores'] ?? [], $data['feedback'] ?? [], auth()->id());
        $evaluation->update(['feedback_text' => $data['feedback_text'] ?? null]);

        if ($data['action'] === 'save') {
            return back()->with('success', 'Scores saved.');
        }

        try {
            $this->evaluations->publish($evaluation->fresh(), $data['feedback_text'] ?? null);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('evaluator.duolingo.index')
            ->with('success', 'Result published. The student has been notified by email.');
    }
}
