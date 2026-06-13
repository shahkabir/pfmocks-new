<?php

namespace App\Http\Controllers\Admin\Pte;

use App\Http\Controllers\Controller;
use App\Services\Pte\PteQuestionSubTypeService;
use App\Services\Pte\PteSectionService;
use Illuminate\Http\Request;

class PteQuestionSubTypeController extends Controller
{
    public function __construct(
        private readonly PteQuestionSubTypeService $service,
        private readonly PteSectionService         $sectionService,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->eloquent($this->service->getQuery())
                ->addColumn('section_name', fn($s) => $s->section?->name ?? '—')
                ->addColumn('action', fn($s) =>
                    '<a href="' . route('admin.pte.sub-types.edit', $s->id) . '" class="btn btn-sm btn-warning me-1">Edit</a>
                     <button data-id="' . $s->id . '" class="btn btn-sm btn-danger delete-btn">Delete</button>')
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.pte.sub-types.index');
    }

    public function create()
    {
        return view('admin.pte.sub-types.create', ['sections' => $this->sectionService->all()]);
    }

    public function edit(int $id)
    {
        return view('admin.pte.sub-types.edit', [
            'subType'  => $this->service->findOrFail($id),
            'sections' => $this->sectionService->all(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $this->service->create($request->all());
            return redirect()->route('admin.pte.sub-types.index')->with('success', 'Sub-type created.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $this->service->update($id, $request->all());
            return redirect()->route('admin.pte.sub-types.index')->with('success', 'Sub-type updated.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Sub-type deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
