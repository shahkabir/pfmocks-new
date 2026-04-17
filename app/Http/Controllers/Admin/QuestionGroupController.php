<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\QuestionGroupService;
use App\Services\QuestionOptionsService;
use App\Services\QuestionService;
use Illuminate\Http\Request;

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
            ? $this->optionsService->byQuestion((int)$preselectedQuestionId)
            : collect();
        return view('admin.question-groups.create', compact('questions', 'options', 'preselectedQuestionId'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $this->service->create($request->all());
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
        try {
            $this->service->update($id, $request->all());
            return redirect()->route('admin.question-groups.index')->with('success', 'Question group updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Group deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
