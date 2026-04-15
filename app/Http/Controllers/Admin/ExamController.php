<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExamService;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct(private readonly ExamService $service) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->eloquent($this->service->getQuery())
                ->addColumn('is_active', fn($e) => $e->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>')
                ->addColumn('modules_count', fn($e) => $e->modules()->count())
                ->addColumn('action', fn($e) =>
                    '<a href="' . route('admin.exams.edit', $e->id) . '" class="btn btn-sm btn-warning me-1">Edit</a>
                     <button data-id="' . $e->id . '" class="btn btn-sm btn-danger delete-btn">Delete</button>')
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }

        return view('admin.exams.index');
    }

    public function create()
    {
        return view('admin.exams.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->merge(['is_active' => $request->boolean('is_active')])->all();
            $this->service->create($data);
            return redirect()->route('admin.exams.index')->with('success', 'Exam created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(int $id)
    {
        $exam = $this->service->findOrFail($id);
        return view('admin.exams.edit', compact('exam'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $data = $request->merge(['is_active' => $request->boolean('is_active')])->all();
            $this->service->update($id, $data);
            return redirect()->route('admin.exams.index')->with('success', 'Exam updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Exam deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
