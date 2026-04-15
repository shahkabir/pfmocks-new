<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\QuestionOptionsService;
use App\Services\QuestionService;
use Illuminate\Http\Request;

class QuestionOptionsController extends Controller
{
    public function __construct(
        private readonly QuestionOptionsService $service,
        private readonly QuestionService        $questionService,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->eloquent($this->service->getQuery())
                ->addColumn('question_short', fn($o) => 'Q#' . $o->question_id . ' — ' . \Str::limit($o->actual_question, 50))
                ->addColumn('is_correct_badge', fn($o) => $o->is_correct
                    ? '<span class="badge bg-success">Correct</span>'
                    : '<span class="badge bg-secondary">—</span>')
                ->addColumn('is_active_badge', fn($o) => $o->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('action', fn($o) =>
                    '<a href="' . route('admin.question-options.edit', $o->id) . '" class="btn btn-sm btn-warning me-1">Edit</a>
                     <button data-id="' . $o->id . '" class="btn btn-sm btn-danger delete-btn">Delete</button>')
                ->rawColumns(['is_correct_badge', 'is_active_badge', 'action'])
                ->make(true);
        }

        return view('admin.question-options.index');
    }

    public function create(Request $request)
    {
        $questions = $this->questionService->getQuery()->with('module')->orderBy('id', 'desc')->get();
        $preselectedQuestionId = $request->query('question_id');
        return view('admin.question-options.create', compact('questions', 'preselectedQuestionId'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->merge([
                'is_correct' => $request->boolean('is_correct'),
                'is_active'  => $request->boolean('is_active'),
            ])->all();
            $option = $this->service->create($data);
            return redirect()->route('admin.question-options.index')->with('success', 'Question option created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(int $id)
    {
        $option    = $this->service->findOrFail($id);
        $questions = $this->questionService->getQuery()->with('module')->orderBy('id', 'desc')->get();
        return view('admin.question-options.edit', compact('option', 'questions'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $data = $request->merge([
                'is_correct' => $request->boolean('is_correct'),
                'is_active'  => $request->boolean('is_active'),
            ])->all();
            $this->service->update($id, $data);
            return redirect()->route('admin.question-options.index')->with('success', 'Question option updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Option deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
