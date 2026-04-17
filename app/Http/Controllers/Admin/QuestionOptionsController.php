<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\QuestionOptionsMcqImport;
use App\Models\CsvImport;
use App\Services\QuestionOptionsService;
use App\Services\QuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class QuestionOptionsController extends Controller
{
    public function __construct(
        private readonly QuestionOptionsService $service,
        private readonly QuestionService        $questionService,
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->eloquent($this->service->getQuery())
                ->addColumn('question_short', fn($o) => 'Q#' . $o->question_id . ' — ' . \Str::limit($o->actual_question, 50))
                ->addColumn('is_correct_badge', fn($o) => $o->is_correct
                    ? '<span class="badge bg-success">Correct</span>'
                    : '<span class="badge bg-secondary">—</span>')
                ->addColumn('is_active_badge', fn($o) => $o->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('action', fn($o) =>
                    '<a href="' . route('admin.question-options.edit', $o->id) . '" class="btn btn-sm btn-warning me-1">Edit</a>
                     <button data-id="' . $o->id . '" class="btn btn-sm btn-danger delete-btn">Delete</button>')
                ->rawColumns(['is_correct_badge', 'is_active_badge', 'action'])
                ->make(true);
        }

        return view('admin.question-options.index');
    }

    public function create(Request $request)
    {
        $questions = $this->questionService->getQuery()->with('module')->orderBy('id', 'desc')->get();
        $preselectedQuestionId = $request->query('question_id');
        return view('admin.question-options.create', compact('questions', 'preselectedQuestionId'));
    }

    public function store(Request $request)
    {
        try {
            $isMcq = in_array($request->input('question_type'), ['mcq_single', 'mcq_multiple']);

            if ($isMcq && $request->has('options')) {
                $base = [
                    'question_id'                => $request->input('question_id'),
                    'question_type'              => $request->input('question_type'),
                    'actual_question'            => $request->input('actual_question'),
                    'correct_answer_explanation' => $request->input('correct_answer_explanation'),
                    'question_image_path'        => $request->input('question_image_path'),
                    'question_audio_path'        => $request->input('question_audio_path'),
                    'is_active'                  => $request->boolean('is_active'),
                ];
                $sort = (int) $request->input('sort_order_start', 1);
                foreach ($request->input('options', []) as $opt) {
                    $this->service->create(array_merge($base, [
                        'option_text' => $opt['option_text'] ?? null,
                        'is_correct'  => !empty($opt['is_correct']),
                        'sort_order'  => $sort++,
                    ]));
                }
                $count = count($request->input('options'));
                return redirect()->route('admin.question-options.index')
                    ->with('success', "{$count} option(s) created successfully.");
            }

            $data = $request->merge([
                'is_correct' => $request->boolean('is_correct'),
                'is_active'  => $request->boolean('is_active'),
            ])->all();
            $this->service->create($data);
            return redirect()->route('admin.question-options.index')->with('success', 'Question option created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(int $id)
    {
        $option    = $this->service->findOrFail($id);
        $questions = $this->questionService->getQuery()->with('module')->orderBy('id', 'desc')->get();
        return view('admin.question-options.edit', compact('option', 'questions'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $data = $request->merge([
                'is_correct' => $request->boolean('is_correct'),
                'is_active'  => $request->boolean('is_active'),
            ])->all();
            $this->service->update($id, $data);
            return redirect()->route('admin.question-options.index')->with('success', 'Question option updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function csvImportStore(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'csv_file'    => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:' . config('csv_import.max_file_size_kb', 10240),
            ],
        ]);

        $file     = $request->file('csv_file');
        $disk     = config('csv_import.disk', 'local');
        $path     = config('csv_import.path', 'csv_imports');
        $stored   = $file->store($path, $disk);

        $import = CsvImport::create([
            'question_id'       => $request->input('question_id'),
            'question_type'     => 'mcq_single',
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename'   => $stored,
            'disk'              => $disk,
            'status'            => 'processing',
            'imported_by'       => auth()->id(),
        ]);

        try {
            Excel::import(
                new QuestionOptionsMcqImport($import->question_id, $import->id),
                $stored,
                $disk,
                \Maatwebsite\Excel\Excel::CSV
            );

            $import->refresh();
            $import->update(['status' => $import->failed_rows > 0 ? 'completed' : 'completed']);

            $msg = "CSV import complete — {$import->processed_rows} question(s) imported";
            if ($import->failed_rows > 0) {
                $msg .= ", {$import->failed_rows} row(s) failed";
            }

            return redirect()->route('admin.question-options.index')->with('success', $msg . '.');
        } catch (\Throwable $e) {
            $import->update(['status' => 'failed', 'errors' => [['row' => 0, 'error' => $e->getMessage()]]]);
            return back()->withErrors(['csv_file' => 'Import failed: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Option deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
