<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExamService;
use App\Services\ModuleService;
use App\Services\QuestionService;
use Illuminate\Http\Request;

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
        try {
            $this->service->create($request->all());
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
        try {
            $this->service->update($id, $request->all());
            return redirect()->route('admin.questions.index')->with('success', 'Question updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Question deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
