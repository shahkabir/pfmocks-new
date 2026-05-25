<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExamService;
use App\Services\ModuleService;
use App\Services\QuestionService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    public function __construct(
        private readonly QuestionService $service,
        private readonly ModuleService   $moduleService,
        private readonly ExamService     $examService,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->eloquent($this->service->getQuery())
                ->addColumn('module_name', fn($q) => $q->module?->name ?? '—')
                ->addColumn('header_short', fn($q) => \Str::limit($q->question_header, 60))
                ->addColumn('action', fn($q) =>
                    '<a href="' . route('admin.questions.edit', $q->id) . '" class="btn btn-sm btn-warning me-1">Edit</a>
                     <button data-id="' . $q->id . '" class="btn btn-sm btn-danger delete-btn">Delete</button>')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.questions.index');
    }

    public function create()
    {
        $exams   = $this->examService->all()->sortByDesc('id');
        $modules = $this->moduleService->all()->sortByDesc('id');
        return view('admin.questions.create', compact('exams', 'modules'));
    }

    public function store(Request $request)
    {
        $this->validateFiles($request);

        try {
            $data               = $request->except(['audio_file', 'image_file']);
            $data['audio_path'] = $this->uploadFile($request->file('audio_file'), 'audio');
            $data['image_path'] = $this->uploadFile($request->file('image_file'), 'image');

            $this->service->create($data);
            return redirect()->route('admin.questions.index')->with('success', 'Question created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(int $id)
    {
        $question = $this->service->findOrFail($id);
        $exams    = $this->examService->all()->sortBy('name');
        $modules  = $this->moduleService->all()->sortBy('name');
        return view('admin.questions.edit', compact('question', 'exams', 'modules'));
    }

    public function update(Request $request, int $id)
    {
        $this->validateFiles($request);

        try {
            $question = $this->service->findOrFail($id);

            $data               = $request->except(['audio_file', 'image_file']);
            $data['audio_path'] = $this->uploadFile($request->file('audio_file'), 'audio', $question->audio_path);
            $data['image_path'] = $this->uploadFile($request->file('image_file'), 'image', $question->image_path);

            $this->service->update($id, $data);
            return redirect()->route('admin.questions.index')->with('success', 'Question updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $question = $this->service->findOrFail($id);

            $this->deletePublicFile($question->audio_path);
            $this->deletePublicFile($question->image_path);

            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Question deleted.']);
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
            'audio_file' => ['nullable', 'file', "mimes:{$audioMimes}"],
            'image_file' => ['nullable', 'file', "mimes:{$imageMimes}", "max:{$imageMax}"],
        ]);
    }

    private function uploadFile(?UploadedFile $file, string $type, ?string $existing = null): ?string
    {
        if (!$file) {
            return $existing;
        }

        $config = config("upload_{$type}");
        $dir    = public_path($config['path']);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

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
