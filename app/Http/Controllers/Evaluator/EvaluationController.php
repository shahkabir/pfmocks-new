<?php

namespace App\Http\Controllers\Evaluator;

use App\Http\Controllers\Controller;
use App\Models\Answer\Answer;
use App\Models\Evaluation\Evaluation;
use App\Services\EvaluationService;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function __construct(private EvaluationService $service) {}

    /** Evaluator's "my evaluations" list. */
    public function index()
    {
        $evaluations = Evaluation::with([
                'attempt.user:id,name,email',
                'attempt.module:id,name,module_type',
                'attempt.module.exam:id,name',
            ])
            ->where('assigned_to', auth()->id())
            ->orderByDesc('id')
            ->get();

        return view('evaluator.evaluations.index', compact('evaluations'));
    }

    /** View one evaluation — student answers + feedback form. */
    public function show(int $id)
    {
        $evaluation = Evaluation::with([
                'attempt.user:id,name,email',
                'attempt.module:id,name,module_type',
                'attempt.module.exam:id,name',
                'attempt.answers.question.options',
            ])
            ->where('id', $id)
            ->where('assigned_to', auth()->id())
            ->firstOrFail();
        // dd($evaluation->toArray());

        // Mark in_progress on first open
        $this->service->markInProgress($evaluation);

        $module  = $evaluation->attempt->module;
        $student = $evaluation->attempt->user;

        // SOP modules use a dedicated view with résumé / SOP file uploads
        if (in_array($module->module_type, \App\Constants\ModuleConstants::SOP_TYPES, true)) {
            $submission = \App\Models\Sop\SopSubmission::where('user_id', $student->id)
                ->where('module_id', $module->id)
                ->latest('id')
                ->first();

            return view('evaluator.evaluations.sop', [
                'evaluation' => $evaluation->fresh(),
                'submission' => $submission,
                'student'    => $student,
                'module'     => $module,
            ]);
        }

        // IELTS writing/speaking — original flow
        $answers = Answer::with(['question:id,question_header,passage,passage_instruction,type'])
            ->where('exam_attempt_id', $evaluation->exam_attempt_id)
            ->where('user_id', $student->id)
            ->get();

        // dd($evaluation->toArray(), $answers->toArray());

        return view('evaluator.evaluations.show', [
            'evaluation' => $evaluation->fresh(),
            'answers'    => $answers,
            'student'    => $student,
            'module'     => $module,
        ]);
    }

    /** Persist feedback + scores + optional voice recording (or final SOP file). */
    public function update(Request $request, int $id)
    {
        // Validate band fields softly (any valid IELTS half-step 0..9)
        $bandRule = ['nullable', 'numeric', 'min:0', 'max:9'];

        $data = $request->validate([
            'feedback_text'      => 'nullable|string|max:8000',
            'feedback_audio'     => 'nullable|file|mimes:webm,mp3,wav,ogg|max:20480',
            'final_sop'          => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'task_achievement'   => $bandRule,
            'coherence_cohesion' => $bandRule,
            'lexical_resource'   => $bandRule,
            'grammatical_range'  => $bandRule,
            'fluency_coherence'  => $bandRule,
            'pronunciation'      => $bandRule,
            'overall'            => $bandRule,
        ]);

        $evaluation = Evaluation::where('id', $id)
            ->where('assigned_to', auth()->id())
            ->firstOrFail();

        $this->service->submitFeedback(
            evaluationId: $evaluation->id,
            data:         $data,
            audio:        $request->file('feedback_audio'),
            finalSop:     $request->file('final_sop'),
        );

        return redirect()
            ->route('evaluator.evaluations.index')
            ->with('success', 'Evaluation submitted. The student has been notified by email.');
    }
}
