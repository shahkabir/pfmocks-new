<?php

namespace App\Http\Controllers\Duolingo;

use App\Http\Controllers\Controller;
use App\Models\Duolingo\DuolingoAttempt;
use App\Models\Duolingo\DuolingoModule;
use App\Models\Exam\UserExam;
use App\Models\Module\Module;
use App\Services\Duolingo\DuolingoExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DuolingoExamController extends Controller
{
    public function __construct(private readonly DuolingoExamService $exam) {}

    /** GET /duolingo/exam/{module}/start — bootstrap (or resume) an attempt. */
    public function start(Module $module)
    {
        abort_unless($module->module_type === DuolingoModule::MODULE_TYPE, 404);

        $hasAccess = $module->type === 'free'
            || UserExam::where('user_id', auth()->id())
                ->where('module_id', $module->id)
                ->whereIn('status', ['free', 'purchased'])
                ->exists();
        abort_unless($hasAccess, 403, 'You do not have access to this exam.');

        $attempt = $this->exam->startOrResumeAttempt(auth()->id(), $module->id);
        return redirect()->route('duolingo.exam.play', $attempt->id);
    }

    /** GET /duolingo/exam/attempt/{attempt} — main play view. */
    public function play(DuolingoAttempt $attempt)
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        if ($attempt->status === DuolingoAttempt::STATUS_COMPLETED) {
            return redirect()->route('duolingo.exam.result', $attempt->id);
        }

        $module    = $attempt->module;
        $structure = $this->exam->loadStructure($module);
        $answers   = $this->exam->answersByMapping($attempt->id);

        return view('exams.duolingo.start', compact('attempt', 'module', 'structure', 'answers'));
    }

    /**
     * POST /duolingo/exam/attempt/{attempt}/answer — AJAX upsert one answer.
     *
     * TODO: per-response_type handlers (fill_in_blanks, word_classification,
     * mcq_single, highlighting, dictation, writing, interactive_writing, essay,
     * speaking, interactive_speaking) — not implemented yet.
     */
    public function saveAnswer(Request $request, DuolingoAttempt $attempt): JsonResponse
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        return response()->json(['ok' => false, 'message' => 'Answer saving is not implemented yet.'], 501);
    }

    /** POST /duolingo/exam/attempt/{attempt}/submit — close the attempt and grade it. */
    public function submit(DuolingoAttempt $attempt)
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        if ($attempt->status === DuolingoAttempt::STATUS_IN_PROGRESS) {
            $this->exam->submit($attempt->id);
        }

        return redirect()->route('duolingo.exam.result', $attempt->id)
            ->with('success', 'Duolingo mock test submitted.');
    }

    /** GET /duolingo/exam/attempt/{attempt}/result — raw performance + estimated scores. */
    public function result(DuolingoAttempt $attempt)
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        $module = $attempt->module;
        return view('exams.duolingo.result', compact('attempt', 'module'));
    }
}
