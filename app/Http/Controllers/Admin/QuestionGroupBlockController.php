<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question\QuestionGroup;
use App\Services\QuestionGroupBlockService;
use App\Services\QuestionGroupService;
use App\Services\QuestionOptionsService;
use Illuminate\Http\Request;

class QuestionGroupBlockController extends Controller
{
    public function __construct(
        private readonly QuestionGroupBlockService $service,
        private readonly QuestionGroupService      $groupService,
        private readonly QuestionOptionsService    $optionsService,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->eloquent($this->service->getQuery())
                ->addColumn('group_label', fn($b) => 'Group #' . $b->question_group_id . ($b->group?->question ? ' (Q#' . $b->group->question_id . ')' : ''))
                ->addColumn('instruction_short', fn($b) => \Str::limit($b->instruction_text, 60))
                ->addColumn('options_count', fn($b) => count(json_decode($b->question_option_ids ?? '[]', true)))
                ->addColumn('action', fn($b) =>
                    '<a href="' . route('admin.question-group-blocks.edit', $b->id) . '" class="btn btn-sm btn-warning me-1">Edit</a>
                     <button data-id="' . $b->id . '" class="btn btn-sm btn-danger delete-btn">Delete</button>')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.question-group-blocks.index');
    }

    public function create(Request $request)
    {
        $groups  = $this->groupService->getQuery()->orderByDesc('id')->get();
        $preselectedGroupId = $request->query('group_id');
        $options = collect();

        if ($preselectedGroupId) {
            $group = QuestionGroup::find($preselectedGroupId);
            if ($group) {
                $options = $this->optionsService->byQuestion($group->question_id);
            }
        }

        return view('admin.question-group-blocks.create', compact('groups', 'options', 'preselectedGroupId'));
    }

    public function store(Request $request)
    {
        try {
            $this->service->create($request->all());
            return redirect()->route('admin.question-group-blocks.index')->with('success', 'Block created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(int $id)
    {
        $block   = $this->service->findOrFail($id);
        $groups  = $this->groupService->getQuery()->get();
        $group   = QuestionGroup::find($block->question_group_id);
        $options = $group ? $this->optionsService->byQuestion($group->question_id) : collect();
        $selectedIds = json_decode($block->question_option_ids ?? '[]', true);
        return view('admin.question-group-blocks.edit', compact('block', 'groups', 'options', 'selectedIds'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $this->service->update($id, $request->all());
            return redirect()->route('admin.question-group-blocks.index')->with('success', 'Block updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Block deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
