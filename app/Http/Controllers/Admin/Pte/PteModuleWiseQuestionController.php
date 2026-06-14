<?php

namespace App\Http\Controllers\Admin\Pte;

use App\Http\Controllers\Controller;
use App\Services\Pte\PteModuleService;
use App\Services\Pte\PteModuleWiseQuestionService;
use App\Services\Pte\PteQuestionGranularService;
use Illuminate\Http\Request;

class PteModuleWiseQuestionController extends Controller
{
    public function __construct(
        private readonly PteModuleWiseQuestionService $service,
        private readonly PteModuleService             $pteModuleService,
        private readonly PteQuestionGranularService   $questionService,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->eloquent($this->service->getQuery())
                ->addColumn('pte_module', fn($r) => 'PteModule#' . $r->pte_module_id)
                ->addColumn('question',   fn($r) => $r->question?->question_granular_id ?? '—')
                ->addColumn('action', fn($r) =>
                    '<a href="' . route('admin.pte.module-questions.edit', $r->id) . '" class="btn btn-sm btn-warning me-1">Edit</a>
                     <button data-id="' . $r->id . '" class="btn btn-sm btn-danger delete-btn">Delete</button>')
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.pte.module-questions.index');
    }

    public function create()
    {
        return view('admin.pte.module-questions.create', [
            'pteModules' => $this->pteModuleService->getQuery()->get(),
        ]);
    }

    public function edit(int $id)
    {
        return view('admin.pte.module-questions.edit', [
            'mapping'    => $this->service->findOrFail($id)->load('pteModule.section'),
            'pteModules' => $this->pteModuleService->getQuery()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pte_module_id'                 => 'required|exists:pte_module,id',
            'pte_question_granular_ids'     => 'required|array|min:1',
            'pte_question_granular_ids.*'   => 'integer|exists:pte_question_granular,id',
        ]);

        $created = $this->service->bulkCreate(
            (int) $data['pte_module_id'],
            $data['pte_question_granular_ids'],
        );

        $msg = $created > 0
            ? "{$created} question(s) added to the PTE module."
            : 'No new mappings created — all selected questions were already attached.';

        return redirect()->route('admin.pte.module-questions.index')->with('success', $msg);
    }

    public function update(Request $request, int $id)
    {
        try {
            $this->service->update($id, $request->all());
            return redirect()->route('admin.pte.module-questions.index')->with('success', 'Mapping updated.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Mapping deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
