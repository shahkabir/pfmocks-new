<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\QuestionOptionsController;
use App\Http\Controllers\Admin\QuestionGroupController;
use App\Http\Controllers\Admin\QuestionGroupBlockController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ReferralProgramController;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\Admin\EvaluationController as AdminEvaluationController;
use App\Http\Controllers\Admin\Pte\PteSectionController;
use App\Http\Controllers\Admin\Pte\PteQuestionSubTypeController;
use App\Http\Controllers\Admin\Pte\PteModuleController as AdminPteModuleController;
use App\Http\Controllers\Admin\Pte\PteQuestionGranularController;
use App\Http\Controllers\Admin\Pte\PteModuleWiseQuestionController;

/*
|--------------------------------------------------------------------------
| Admin CRUD Routes
| Middleware: auth, admin  |  Prefix: /admin  |  Name: admin.*
|--------------------------------------------------------------------------
*/

// Exams
Route::resource('exams', ExamController::class)->except(['show']);

// Modules
Route::resource('modules', ModuleController::class)->except(['show']);

// Questions
Route::resource('questions', QuestionController::class)->except(['show']);

// Question Options
Route::post('question-options/csv-import', [QuestionOptionsController::class, 'csvImportStore'])->name('question-options.csv-import');
Route::resource('question-options', QuestionOptionsController::class)->except(['show']);

// Question Groups
Route::resource('question-groups', QuestionGroupController::class)->except(['show']);

// Question Group Blocks
Route::resource('question-group-blocks', QuestionGroupBlockController::class)->except(['show']);

// Evaluations (admin assigns writing/speaking attempts to evaluators)
Route::get ('evaluations',         [AdminEvaluationController::class, 'index'])->name('evaluations.index');
Route::get ('evaluations/list',    [AdminEvaluationController::class, 'list'])->name('evaluations.list');
Route::post('evaluations/assign',  [AdminEvaluationController::class, 'assign'])->name('evaluations.assign');

// Scholarships (admin CRUD)
Route::get   ('scholarships/list',  [ScholarshipController::class, 'list'])->name('scholarships.list');
Route::resource('scholarships',     ScholarshipController::class)->except(['show']);

// Referral Programs (admin CRUD)
Route::get   ('referral-programs/list',  [ReferralProgramController::class, 'list'])->name('referral-programs.list');
Route::resource('referral-programs',     ReferralProgramController::class)->except(['show']);

// Payments (admin verification)
Route::get('payments',                    [AdminPaymentController::class, 'index'])->name('payments.index');
Route::get('payments/list',               [AdminPaymentController::class, 'list'])->name('payments.list');
Route::get('payments/{id}',               [AdminPaymentController::class, 'show'])->name('payments.show');
Route::post('payments/{id}/approve',      [AdminPaymentController::class, 'approve'])->name('payments.approve');
Route::post('payments/{id}/reject',       [AdminPaymentController::class, 'reject'])->name('payments.reject');

// ── PTE ─────────────────────────────────────────────────────────────────────
Route::prefix('pte')->name('pte.')->group(function () {
    Route::resource('sections',          PteSectionController::class)->except(['show']);
    Route::resource('sub-types',         PteQuestionSubTypeController::class)->except(['show']);
    Route::resource('modules',           AdminPteModuleController::class)->except(['show']);
    Route::resource('questions',         PteQuestionGranularController::class)->except(['show']);
    Route::resource('module-questions',  PteModuleWiseQuestionController::class)->except(['show']);
});

// ── AJAX helpers for dependent dropdowns ──────────────────────────────────
Route::prefix('ajax')->name('ajax.')->group(function () {

    // Load modules for a given exam
    Route::get('modules-by-exam/{examId}', function (int $examId) {
        $modules = \App\Models\Module\Module::where('exam_id', $examId)
            ->orderByDesc('id')
            ->get(['id', 'name', 'module_type']);
        return response()->json($modules);
    })->name('modules-by-exam');

    // Load questions for a given module
    Route::get('questions-by-module/{moduleId}', function (int $moduleId) {
        $questions = \App\Models\Question\Question::where('module_id', $moduleId)
            ->orderByDesc('id')
            ->get(['id', 'question_header', 'type']);
        return response()->json($questions);
    })->name('questions-by-module');

    // Load options for a given question
    Route::get('options-by-question/{questionId}', function (int $questionId) {
        $options = \App\Models\Question\QuestionOptions::where('question_id', $questionId)
            ->orderByDesc('id')
            ->get(['id', 'actual_question', 'option_text', 'question_type']);

        return response()->json($options);
    })->name('options-by-question');

    // Load PTE questions available for a given PTE module (filtered by its section,
    // grouped by sub-type). Returns existing mappings too so the UI can mark/skip them.
    Route::get('pte-questions-by-pte-module/{pteModuleId}', function (int $pteModuleId) {
        $pteModule = \App\Models\Pte\PteModule::with('section:id,name,tag')->findOrFail($pteModuleId);

        $existingIds = \App\Models\Pte\PteModuleWiseQuestion::where('pte_module_id', $pteModuleId)
            ->pluck('pte_question_granular_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        $questions = \App\Models\Pte\PteQuestionGranular::with('subType:id,name,tag,display_order')
            ->whereHas('subType', fn ($q) => $q->where('pte_section_id', $pteModule->pte_section_id))
            ->where('is_active', true)
            ->orderBy('pte_sub_type_id')
            ->orderBy('id')
            ->get(['id', 'question_granular_id', 'question_text', 'pte_sub_type_id']);

        $groups = $questions->groupBy('pte_sub_type_id')->map(function ($items) {
            $first = $items->first();
            return [
                'sub_type_id'   => $first->pte_sub_type_id,
                'sub_type_name' => $first->subType?->name,
                'sub_type_tag'  => $first->subType?->tag,
                'questions'     => $items->map(fn ($q) => [
                    'id'                   => (int) $q->id,
                    'question_granular_id' => $q->question_granular_id,
                    'preview'              => \Illuminate\Support\Str::limit(strip_tags((string) $q->question_text), 110),
                ])->values(),
            ];
        })->values();

        return response()->json([
            'pte_module' => [
                'id'           => $pteModule->id,
                'section_name' => $pteModule->section?->name,
                'section_tag'  => $pteModule->section?->tag,
            ],
            'existing_question_ids' => $existingIds,
            'groups'                => $groups,
        ]);
    })->name('pte-questions-by-pte-module');

    // Load options for a given question group (for block creation)
    Route::get('options-by-group/{groupId}', function (int $groupId) {
        $group = \App\Models\Question\QuestionGroup::findOrFail($groupId);
        $options = \App\Models\Question\QuestionOptions::where('question_id', $group->question_id)
            ->orderByDesc('id')
            ->get(['id', 'actual_question', 'option_text', 'question_type']);
        return response()->json($options);
    })->name('options-by-group');
});
