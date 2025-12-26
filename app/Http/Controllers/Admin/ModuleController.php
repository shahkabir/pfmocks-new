<?php

namespace App\Http\Controllers\Admin;

use App\Models\Exam\Exam;
use Illuminate\Http\Request;
use App\Models\Module\Module;
use App\Http\Controllers\Controller;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::with('exam')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.modules.index', compact('modules'));
    }

    public function create()
    {
        $exams = Exam::orderBy('name')->get();
        return view('admin.modules.create', compact('exams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        Module::create($request->all());

        return redirect()
            ->route('admin.modules.index')
            ->with('success', 'Module created successfully');
    }

    public function edit(Module $module)
    {
        $exams = Exam::orderBy('name')->get();
        return view('admin.modules.edit', compact('module', 'exams'));
    }

    public function update(Request $request, Module $module)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $module->update($request->all());

        return redirect()
            ->route('admin.modules.index')
            ->with('success', 'Module updated successfully');
    }

    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()
            ->route('admin.modules.index')
            ->with('success', 'Module deleted successfully');
    }
}
