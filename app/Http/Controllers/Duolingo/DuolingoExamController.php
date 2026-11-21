<?php

namespace App\Http\Controllers\Duolingo;

use App\Http\Controllers\Controller;
use App\Models\Duolingo\DuolingoAnswer;
use App\Models\Duolingo\DuolingoAttempt;
use App\Models\Duolingo\DuolingoModule;
use App\Models\Duolingo\DuolingoModuleWiseQuestion;
use App\Models\Exam\UserExam;
use App\Models\Module\Module;
use App\Services\Duolingo\DuolingoExamService;
use App\Services\Duolingo\DuolingoResultService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DuolingoExamController extends Controller
{
    private const AUDIO_DIR        = 'data/audio/duolingo/answers';
    private const AUDIO_EXTENSIONS = ['webm', 'ogg', 'mp4', 'm4a', 'wav'];
    private const MAX_TEXT         = 20000;

    public function __construct(
        private readonly DuolingoExamService   $exam,
        private readonly DuolingoResultService $results,
    ) {}

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

    /** GET /duolingo/exam/attempt/{attempt} — main play view, resuming from the saved state. */
    public function play(DuolingoAttempt $attempt)
    {
        $this->authorizeOwner($attempt);

        if (!$attempt->isInProgress()) {
            return redirect()->route('duolingo.exam.result', $attempt->id);
        }

        $module    = $attempt->module;
        $structure = $this->exam->loadStructure($module);
        $answers   = $this->exam->answersByMapping($attempt->id);

        return view('exams.duolingo.start', compact('attempt', 'module', 'structure', 'answers'));
    }

    /**
     * POST /duolingo/exam/attempt/{attempt}/answer — autosave one response.
     *
     * Body: mapping_id, payload (JSON of the response), and for recordings audio_blob (+ turn).
     * The reply never says whether the answer is correct.
     */
    public function saveAnswer(Request $request, DuolingoAttempt $attempt): JsonResponse
    {
        $this->authorizeOwner($attempt);
        if (!$attempt->isInProgress()) {
            return response()->json(['ok' => false, 'message' => 'This test has already been submitted.'], 422);
        }

        $request->validate(['mapping_id' => 'required|integer', 'payload' => 'nullable|string|max:200000']);

        $mapping = $this->exam->findMapping($attempt, (int) $request->input('mapping_id'));
        if (!$mapping) {
            return response()->json(['ok' => false, 'message' => 'This question is not part of your test.'], 422);
        }

        $payload  = json_decode((string) $request->input('payload', '{}'), true) ?: [];
        $existing = DuolingoAnswer::where('duolingo_attempt_id', $attempt->id)
            ->where('duolingo_module_wise_question_id', $mapping->id)
            ->first();

        $columns = match ($mapping->question->type->response_type) {
            'fill_in_blanks'       => $this->blanksAnswer($payload, $mapping),
            'word_classification'  => $this->validated($payload, ['response_text' => ['nullable', Rule::in(['yes', 'no'])]]),
            'mcq_single'           => $this->singleChoiceAnswer($payload, $mapping),
            'highlighting'         => $this->highlightAnswer($payload),
            'dictation', 'writing', 'essay'
                                   => $this->validated($payload, ['response_text' => 'nullable|string|max:' . self::MAX_TEXT]),
            'interactive_writing'  => $this->stagedWritingAnswer($payload, $mapping),
            'speaking'             => $this->recordingAnswer($request, $attempt, $mapping, $existing),
            'interactive_speaking' => $this->turnRecordingAnswer($request, $attempt, $mapping, $existing),
            default                => abort(422, 'Unsupported question type.'),
        };

        $answer = $this->exam->saveAnswer($attempt, $mapping, $columns);

        return response()->json(['ok' => true, 'saved_at' => $answer->updated_at->toIso8601String()]);
    }

    /** POST /duolingo/exam/attempt/{attempt}/state — remember the current position and timers. */
    public function saveState(Request $request, DuolingoAttempt $attempt): JsonResponse
    {
        $this->authorizeOwner($attempt);
        if (!$attempt->isInProgress()) {
            return response()->json(['ok' => false], 422);
        }

        // sendBeacon posts the state as a JSON string field.
        $raw   = $request->input('state');
        $state = is_string($raw) ? (json_decode($raw, true) ?: []) : (array) $raw;

        $data = Validator::make($state, [
            'mapping_id' => 'required|integer',
            'step'       => 'nullable|integer|min:0|max:20',
            'remaining'  => 'nullable|integer|min:0|max:7200',
            'groups'     => 'nullable|array|max:50',
            'groups.*'   => 'integer|min:0|max:7200',
            'plays'      => 'nullable|array|max:30',
            'plays.*'    => 'integer|min:0|max:20',
            'finished'   => 'nullable|boolean',
            'saved_at'   => 'nullable|integer|min:0',
        ])->validate();

        // An older checkpoint arriving late must not overwrite a newer one.
        $previous = (int) ($attempt->state['saved_at'] ?? 0);
        if (isset($data['saved_at']) && $data['saved_at'] < $previous) {
            return response()->json(['ok' => true, 'stale' => true]);
        }

        if (!$this->exam->findMapping($attempt, (int) $data['mapping_id'])) {
            return response()->json(['ok' => false], 422);
        }

        $this->exam->saveState($attempt, $data);

        return response()->json(['ok' => true]);
    }

    /** POST /duolingo/exam/attempt/{attempt}/submit — close the attempt and send it for evaluation. */
    public function submit(DuolingoAttempt $attempt)
    {
        $this->authorizeOwner($attempt);

        if ($attempt->isInProgress()) {
            $attempt = $this->exam->submit($attempt);
        }

        $message = $attempt->isPublished()
            ? 'Test submitted. Your result is ready.'
            : 'Test submitted. Your writing and speaking will be evaluated, and your final score will be published afterwards.';

        return redirect()->route('duolingo.exam.result', $attempt->id)->with('success', $message);
    }

    /** GET /duolingo/exam/attempt/{attempt}/result — published scores, or the evaluation status. */
    public function result(DuolingoAttempt $attempt)
    {
        $this->authorizeOwner($attempt);

        $module = $attempt->module;
        $all    = $this->results->rows($attempt);
        // The breakdown, including correct answers, is shown only once the result is published.
        $rows          = $attempt->isPublished() ? $all : collect();
        $answeredCount = $all->filter(fn ($r) => $r['answer'])->count();
        $questionCount = $all->count();
        $evaluation    = $attempt->evaluation;

        return view('exams.duolingo.result', compact('attempt', 'module', 'rows', 'answeredCount', 'questionCount', 'evaluation'));
    }

    /* ========================================================================
     | Per response type payloads — each returns only the columns it owns.
     |========================================================================*/

    private function validated(array $payload, array $rules): array
    {
        return Validator::make($payload, $rules)->validate();
    }

    /** { response_blanks: { "1": "ice", ... } } keyed by blank order. */
    private function blanksAnswer(array $payload, DuolingoModuleWiseQuestion $mapping): array
    {
        $data   = $this->validated($payload, ['response_blanks' => 'nullable|array|max:50', 'response_blanks.*' => 'nullable|string|max:100']);
        $orders = $mapping->question->blanks->pluck('blank_order')->map(fn ($o) => (string) $o)->all();

        $blanks = collect($data['response_blanks'] ?? [])
            ->only($orders)
            ->map(fn ($v) => trim((string) $v))
            ->filter(fn ($v) => $v !== '')
            ->all();

        return ['response_blanks' => $blanks];
    }

    /** { selected_option_ids: [id] } — the option must belong to this question. */
    private function singleChoiceAnswer(array $payload, DuolingoModuleWiseQuestion $mapping): array
    {
        $ids  = $mapping->question->options->pluck('id')->all();
        $data = $this->validated($payload, [
            'selected_option_ids'   => 'nullable|array|max:1',
            'selected_option_ids.*' => ['integer', Rule::in($ids)],
        ]);

        return ['selected_option_ids' => array_values($data['selected_option_ids'] ?? [])];
    }

    /** { response_highlight: { start, end, text } } or null to clear. */
    private function highlightAnswer(array $payload): array
    {
        $data = $this->validated($payload, [
            'response_highlight'       => 'nullable|array',
            'response_highlight.start' => 'required_with:response_highlight|integer|min:0',
            'response_highlight.end'   => 'required_with:response_highlight|integer|gt:response_highlight.start',
            'response_highlight.text'  => 'required_with:response_highlight|string|max:2000',
        ]);

        return ['response_highlight' => $data['response_highlight'] ?? null];
    }

    /** { response_texts: [part1, part2] } — one entry per writing stage. */
    private function stagedWritingAnswer(array $payload, DuolingoModuleWiseQuestion $mapping): array
    {
        $stages = max(1, $mapping->question->turns()->count());
        $data   = $this->validated($payload, [
            'response_texts'   => "nullable|array|max:{$stages}",
            'response_texts.*' => 'nullable|string|max:' . self::MAX_TEXT,
        ]);

        return ['response_texts' => array_values(array_map(fn ($t) => (string) $t, $data['response_texts'] ?? []))];
    }

    private function recordingAnswer(Request $request, DuolingoAttempt $attempt, DuolingoModuleWiseQuestion $mapping, ?DuolingoAnswer $existing): array
    {
        $file = $this->validatedRecording($request);
        $this->deleteRecording($existing?->response_audio_url);

        return ['response_audio_url' => $this->storeRecording($file, $attempt, "q{$mapping->id}")];
    }

    /** One recording per turn; earlier turns are kept. */
    private function turnRecordingAnswer(Request $request, DuolingoAttempt $attempt, DuolingoModuleWiseQuestion $mapping, ?DuolingoAnswer $existing): array
    {
        $turns = max(1, $mapping->question->turns()->count());
        $turn  = (int) $request->validate(['turn' => "required|integer|min:1|max:{$turns}"])['turn'];
        $file  = $this->validatedRecording($request);

        $urls = $existing?->response_audio_urls ?? [];
        $this->deleteRecording($urls[(string) $turn] ?? null);
        $urls[(string) $turn] = $this->storeRecording($file, $attempt, "q{$mapping->id}_t{$turn}");
        ksort($urls);

        return ['response_audio_urls' => $urls];
    }

    private function validatedRecording(Request $request): UploadedFile
    {
        // MediaRecorder blobs often sniff as application/octet-stream, so mimes: rules reject them.
        $request->validate(['audio_blob' => 'required|file|max:51200']);

        return $request->file('audio_blob');
    }

    private function storeRecording(UploadedFile $file, DuolingoAttempt $attempt, string $slot): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::AUDIO_EXTENSIONS, true)) {
            $extension = 'webm';
        }

        $dir  = self::AUDIO_DIR . "/{$attempt->id}";
        $name = "{$slot}_" . time() . '_' . Str::random(6) . ".{$extension}";
        $file->move(public_path($dir), $name);

        return "{$dir}/{$name}";
    }

    private function deleteRecording(?string $path): void
    {
        if ($path && str_starts_with($path, self::AUDIO_DIR . '/') && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }

    private function authorizeOwner(DuolingoAttempt $attempt): void
    {
        abort_unless($attempt->user_id === auth()->id(), 403);
    }
}
