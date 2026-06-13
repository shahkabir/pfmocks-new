<?php

namespace App\Http\Controllers\Admin\Pte;

use App\Http\Controllers\Controller;
use App\Services\Pte\PteQuestionGranularService;
use App\Services\Pte\PteQuestionSubTypeService;
use Illuminate\Http\Request;

class PteQuestionGranularController extends Controller
{
    public function __construct(
        private readonly PteQuestionGranularService $service,
        private readonly PteQuestionSubTypeService  $subTypeService,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->eloquent($this->service->getQuery())
                ->addColumn('granular_id', fn($q) => $q->question_granular_id)
                ->addColumn('sub_type',    fn($q) => $q->subType?->name ?? '—')
                ->addColumn('section',     fn($q) => $q->subType?->section?->name ?? '—')
                ->addColumn('action', fn($q) =>
                    '<a href="' . route('admin.pte.questions.edit', $q->id) . '" class="btn btn-sm btn-warning me-1">Edit</a>
                     <button data-id="' . $q->id . '" class="btn btn-sm btn-danger delete-btn">Delete</button>')
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.pte.questions.index');
    }

    public function create()
    {
        return view('admin.pte.questions.create', [
            'subTypes' => $this->subTypeService->getQuery()->orderBy('display_order')->get(),
        ]);
    }

    public function edit(int $id)
    {
        return view('admin.pte.questions.edit', [
            'question' => $this->service->findOrFail($id)->load(['options', 'blanks', 'segments', 'highlightWords']),
            'subTypes' => $this->subTypeService->getQuery()->orderBy('display_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $this->service->create($request->all());
            return redirect()->route('admin.pte.questions.index')->with('success', 'Question created.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $this->service->update($id, $request->all());
            return redirect()->route('admin.pte.questions.index')->with('success', 'Question updated.');
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
