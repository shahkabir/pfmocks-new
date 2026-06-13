<?php

namespace App\Http\Controllers\Admin\Pte;

use App\Http\Controllers\Controller;
use App\Services\Pte\PteSectionService;
use Illuminate\Http\Request;

class PteSectionController extends Controller
{
    public function __construct(private readonly PteSectionService $service) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->eloquent($this->service->getQuery())
                ->addColumn('action', fn($s) =>
                    '<a href="' . route('admin.pte.sections.edit', $s->id) . '" class="btn btn-sm btn-warning me-1">Edit</a>
                     <button data-id="' . $s->id . '" class="btn btn-sm btn-danger delete-btn">Delete</button>')
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.pte.sections.index');
    }

    public function create()      { return view('admin.pte.sections.create'); }
    public function edit(int $id) { return view('admin.pte.sections.edit', ['section' => $this->service->findOrFail($id)]); }

    public function store(Request $request)
    {
        try {
            $this->service->create($request->all());
            return redirect()->route('admin.pte.sections.index')->with('success', 'Section created.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $this->service->update($id, $request->all());
            return redirect()->route('admin.pte.sections.index')->with('success', 'Section updated.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Section deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
