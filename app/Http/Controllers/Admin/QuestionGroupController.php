<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\QuestionGroupService;
use App\Services\QuestionOptionsService;
use App\Services\QuestionService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class QuestionGroupController extends Controller
{
    public function __construct(
        private readonly QuestionGroupService   $service,
        private readonly QuestionService        $questionService,
        private readonly QuestionOptionsService $optionsService,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->eloquent($this->service->getQuery())
                ->addColumn('question_label', fn($g) => 'Q#' . $g->question_id . ($g->question ? ' — ' . \Str::limit($g->question->question_header, 40) : ''))
                ->addColumn('blocks_count', fn($g) => $g->blocks->count())
                ->addColumn('action', fn($g) =>
                    '<a href="' . route('admin.question-groups.edit', $g->id) . '" class="btn btn-sm btn-warning me-1">Edit</a>
                     <button data-id="' . $g->id . '" class="btn btn-sm btn-danger delete-btn">Delete</button>')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.question-groups.index');
    }

    public function create(Request $request)
    {
        $questions = $this->questionService->getQuery()->with('module')->orderBy('id', 'desc')->get();
        $preselectedQuestionId = $request->query('question_id');
        $options = $preselectedQuestionId
            ? $this->optionsService->byQuestion((int) $preselectedQuestionId)
            : collect();
        return view('admin.question-groups.create', compact('questions', 'options', 'preselectedQuestionId'));
    }

    public function store(Request $request)
    {
        $this->validateFiles($request);

        try {
            $data                    = $request->except(['part_audio_file', 'part_image_file']);
            $data['part_audio_url']  = $this->uploadFile($request->file('part_audio_file'), 'audio');
            $data['part_image_url']  = $this->uploadFile($request->file('part_image_file'), 'image');

            $this->service->create($data);
            return redirect()->route('admin.question-groups.index')->with('success', 'Question group created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(int $id)
    {
        $group     = $this->service->findOrFail($id);
        $questions = $this->questionService->getQuery()->with('module')->orderBy('id', 'desc')->get();
        $options   = $this->optionsService->byQuestion($group->question_id);
        $selectedIds = json_decode($group->question_options_group_ids ?? '[]', true);
        return view('admin.question-groups.edit', compact('group', 'questions', 'options', 'selectedIds'));
    }

    public function update(Request $request, int $id)
    {
        $this->validateFiles($request);

        try {
            $group = $this->service->findOrFail($id);

            $data                   = $request->except(['part_audio_file', 'part_image_file']);
            $data['part_audio_url'] = $this->uploadFile($request->file('part_audio_file'), 'audio', $group->part_audio_url);
            $data['part_image_url'] = $this->uploadFile($request->file('part_image_file'), 'image', $group->part_image_url);

            $this->service->update($id, $data);
            return redirect()->route('admin.question-groups.index')->with('success', 'Question group updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $group = $this->service->findOrFail($id);

            $this->deletePublicFile($group->part_audio_url);
            $this->deletePublicFile($group->part_image_url);

            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Group deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ── File helpers ──────────────────────────────────────────────────────────

    private function validateFiles(Request $request): void
    {
        $audioMimes = implode(',', config('upload_audio.mimes'));
        $imageMimes = implode(',', config('upload_image.mimes'));
        $imageMax   = config('upload_image.max_size_kb');

        $request->validate([
            'part_audio_file' => ['nullable', 'file', "mimes:{$audioMimes}"],
            'part_image_file' => ['nullable', 'file', "mimes:{$imageMimes}", "max:{$imageMax}"],
        ]);
    }

    private function uploadFile(?UploadedFile $file, string $type, ?string $existing = null): ?string
    {
        if (!$file) {
            return $existing; // no new upload — keep whatever is stored
        }

        $config = config("upload_{$type}");
        $dir    = public_path($config['path']);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Remove the old file when replacing
        $this->deletePublicFile($existing);

        $filename = time() . '_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return '/' . $config['path'] . '/' . $filename;
    }

    private function deletePublicFile(?string $relativePath): void
    {
        if (!$relativePath) return;
        $abs = public_path(ltrim($relativePath, '/'));
        if (file_exists($abs)) {
            @unlink($abs);
        }
    }
}
