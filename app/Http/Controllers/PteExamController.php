<?php

namespace App\Http\Controllers;

use App\Models\Module\Module;
use App\Models\Pte\PteAttempt;
use App\Models\Pte\PteModuleWiseQuestion;
use App\Services\Pte\PteExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PteExamController extends Controller
{
    public function __construct(private readonly PteExamService $exam) {}

    /** GET /pte/exam/{module}/start — bootstraps an attempt, then renders play view. */
    public function start(Module $module)
    {
        $user = auth()->user();
        $attempt = $this->exam->startOrResumeAttempt($user->id, $module->id);
        return redirect()->route('pte.exam.play', $attempt->id);
    }

    /** GET /pte/exam/attempt/{attempt} — main play view. */
    public function play(PteAttempt $attempt)
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        $module    = $attempt->module;
        $structure = $this->exam->loadStructure($module);
        $answers   = $this->exam->answersByMapping($attempt->id);

        return view('exams.pte.start', compact('attempt', 'module', 'structure', 'answers'));
    }

    /** POST /pte/exam/attempt/{attempt}/answer — AJAX upsert one answer. */
    public function saveAnswer(Request $request, PteAttempt $attempt): JsonResponse
    {
        abort_unless($attempt->user_id === auth()->id(), 403);
        if ($attempt->status !== PteAttempt::STATUS_IN_PROGRESS) {
            return response()->json(['ok' => false, 'message' => 'Attempt already submitted.'], 422);
        }

        $data = $request->validate([
            'pte_module_wise_question_id'   => 'required|exists:pte_module_wise_question,id',
            'response_text'                 => 'nullable|string',
            'selected_option_ids'           => 'nullable|array',
            'selected_option_ids.*'         => 'integer',
            'response_blanks'               => 'nullable|array',
            'response_segments_order'       => 'nullable|array',
            'response_segments_order.*'     => 'integer',
            'response_highlight_word_ids'   => 'nullable|array',
            'response_highlight_word_ids.*' => 'integer',
        ]);

        $mapping = PteModuleWiseQuestion::findOrFail($data['pte_module_wise_question_id']);

        // Optional audio upload (for audio_record sub-types)
        if ($request->hasFile('audio_blob')) {
            // Loose validation: MediaRecorder blobs often sniff as octet-stream and fail mimes:
            $request->validate(['audio_blob' => 'file|max:51200']);
            $file = $request->file('audio_blob');
            $dir  = "data/audio/pte/{$attempt->id}";
            $name = uniqid('pte_ans_') . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($dir), $name);
            $data['response_audio_url'] = "{$dir}/{$name}";
        }

        $answer = $this->exam->saveAnswer(
            attemptId:  $attempt->id,
            mappingId:  $mapping->id,
            questionId: (int) $mapping->pte_question_granular_id,
            payload:    $data,
        );

        return response()->json(['ok' => true, 'answer_id' => $answer->id, 'audio_url' => $answer->response_audio_url]);
    }

    /** POST /pte/exam/attempt/{attempt}/intro — save the 25s self-introduction recording. */
    public function saveIntro(Request $request, PteAttempt $attempt): JsonResponse
    {
        abort_unless($attempt->user_id === auth()->id(), 403);
        if ($attempt->status !== PteAttempt::STATUS_IN_PROGRESS) {
            return response()->json(['ok' => false, 'message' => 'Attempt already submitted.'], 422);
        }

        // NOTE: strict mimes:webm validation rejects many MediaRecorder blobs whose
        // headers sniff as application/octet-stream — that's why saving silently failed.
        $request->validate(['audio_blob' => 'required|file|max:51200']);

        // Replace any previous intro recording
        if ($attempt->intro_audio_url && file_exists(public_path($attempt->intro_audio_url))) {
            @unlink(public_path($attempt->intro_audio_url));
        }

        $file = $request->file('audio_blob');

        // User-wise folder derived from the email, e.g. data/audio/pte/intro/skabir-cse-gmail-com
        $email    = (string) ($attempt->user?->email ?? '');
        $userSlug = Str::slug(str_replace(['@', '.'], '-', $email)) ?: 'user-' . $attempt->user_id;
        $dir      = "data/audio/pte/intro/{$userSlug}";

        $ext  = strtolower($file->getClientOriginalExtension() ?: 'webm');
        $name = "intro_attempt{$attempt->id}_" . time() . ".{$ext}";
        $file->move(public_path($dir), $name);
        $relative = "{$dir}/{$name}";

        // Store as mp3 when ffmpeg is available on the server; otherwise keep the original container
        if ($mp3 = $this->convertToMp3(public_path($relative))) {
            @unlink(public_path($relative));
            $relative = "{$dir}/" . basename($mp3);
        }

        $attempt->update(['intro_audio_url' => $relative]);

        return response()->json(['ok' => true, 'intro_audio_url' => asset($relative)]);
    }

    /** Transcode an audio file to mp3 via ffmpeg when installed. Returns the mp3 path or null. */
    private function convertToMp3(string $absPath): ?string
    {
        if (!function_exists('exec')) {
            return null;
        }

        @exec('ffmpeg -version 2>&1', $probe, $probeCode);
        if ($probeCode !== 0) {
            return null; // ffmpeg not on PATH — keep the uploaded format
        }

        $mp3 = preg_replace('/\.[A-Za-z0-9]+$/', '.mp3', $absPath);
        if ($mp3 === $absPath) {
            $mp3 = $absPath . '.mp3';
        }

        @exec('ffmpeg -y -i ' . escapeshellarg($absPath) . ' -vn -ab 128k ' . escapeshellarg($mp3) . ' 2>&1', $out, $code);

        return ($code === 0 && file_exists($mp3)) ? $mp3 : null;
    }

    /** POST /pte/exam/attempt/{attempt}/submit — close the attempt. */
    public function submit(PteAttempt $attempt)
    {
        abort_unless($attempt->user_id === auth()->id(), 403);
        $this->exam->submit($attempt->id);
        return redirect()->route('dashboard.student')->with('success', 'PTE exam submitted.');
    }
}
