<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExamService;
use App\Services\ModuleService;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function __construct(
        private readonly ModuleService $service,
        private readonly ExamService   $examService,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->eloquent($this->service->getQuery())
                ->addColumn('exam_name',    fn($m) => $m->exam?->name ?? '—')
                ->addColumn('type_badge',   fn($m) => $m->type === 'free'
                    ? '<span class="badge bg-success">Free</span>'
                    : '<span class="badge bg-primary">Paid</span>')
                ->addColumn('action', fn($m) =>
                    '<a href="' . route('admin.modules.edit', $m->id) . '" class="btn btn-sm btn-warning me-1">Edit</a>
                     <button data-id="' . $m->id . '" class="btn btn-sm btn-danger delete-btn">Delete</button>')
                ->rawColumns(['type_badge', 'action'])
                ->make(true);
        }

        return view('admin.modules.index');
    }

    public function create()
    {
        $exams = $this->examService->all()->sortByDesc('id');
        return view('admin.modules.create', compact('exams'));
    }

    public function store(Request $request)
    {
        try {
            $this->service->create($request->all());
            return redirect()->route('admin.modules.index')->with('success', 'Module created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(int $id)
    {
        $module = $this->service->findOrFail($id);
        $exams  = $this->examService->all()->sortBy('name');
        return view('admin.modules.edit', compact('module', 'exams'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $this->service->update($id, $request->all());
            return redirect()->route('admin.modules.index')->with('success', 'Module updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Module deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
