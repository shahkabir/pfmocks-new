<?php

namespace App\Http\Controllers\Admin\Pte;

use App\Http\Controllers\Controller;
use App\Services\ModuleService;
use App\Services\Pte\PteModuleService;
use App\Services\Pte\PteSectionService;
use Illuminate\Http\Request;

class PteModuleController extends Controller
{
    public function __construct(
        private readonly PteModuleService  $service,
        private readonly ModuleService     $moduleService,
        private readonly PteSectionService $sectionService,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->eloquent($this->service->getQuery())
                ->addColumn('module_name',  fn($m) => $m->module?->name ?? '—')
                ->addColumn('section_name', fn($m) => $m->section?->name ?? '—')
                ->addColumn('action', fn($m) =>
                    '<a href="' . route('admin.pte.modules.edit', $m->id) . '" class="btn btn-sm btn-warning me-1">Edit</a>
                     <button data-id="' . $m->id . '" class="btn btn-sm btn-danger delete-btn">Delete</button>')
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.pte.modules.index');
    }

    public function create()
    {
        return view('admin.pte.modules.create', [
            'modules'  => $this->moduleService->all(),
            'sections' => $this->sectionService->all(),
        ]);
    }

    public function edit(int $id)
    {
        return view('admin.pte.modules.edit', [
            'pteModule' => $this->service->findOrFail($id),
            'modules'   => $this->moduleService->all(),
            'sections'  => $this->sectionService->all(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $this->service->create($request->all());
            return redirect()->route('admin.pte.modules.index')->with('success', 'PTE module mapping created.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $this->service->update($id, $request->all());
            return redirect()->route('admin.pte.modules.index')->with('success', 'PTE module mapping updated.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'PTE module mapping deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
