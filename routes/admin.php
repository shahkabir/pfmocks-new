<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\QuestionOptionsController;
use App\Http\Controllers\Admin\QuestionGroupController;
use App\Http\Controllers\Admin\QuestionGroupBlockController;

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
Route::resource('question-options', QuestionOptionsController::class)->except(['show']);

// Question Groups
Route::resource('question-groups', QuestionGroupController::class)->except(['show']);

// Question Group Blocks
Route::resource('question-group-blocks', QuestionGroupBlockController::class)->except(['show']);

// ── AJAX helpers for dependent dropdowns ──────────────────────────────────
Route::prefix('ajax')->name('ajax.')->group(function () {

    // Load modules for a given exam
    Route::get('modules-by-exam/{examId}', function (int $examId) {
        $modules = \App\Models\Module\Module::where('exam_id', $examId)
            ->orderBy('name')
            ->get(['id', 'name', 'module_type']);
        return response()->json($modules);
    })->name('modules-by-exam');

    // Load questions for a given module
    Route::get('questions-by-module/{moduleId}', function (int $moduleId) {
        $questions = \App\Models\Question\Question::where('module_id', $moduleId)
            ->orderBy('sort_order')
            ->get(['id', 'question_header', 'type']);
        return response()->json($questions);
    })->name('questions-by-module');

    // Load options for a given question
    Route::get('options-by-question/{questionId}', function (int $questionId) {
        $options = \App\Models\Question\QuestionOptions::where('question_id', $questionId)
            ->orderBy('sort_order')
            ->get(['id', 'actual_question', 'option_text', 'question_type']);
        return response()->json($options);
    })->name('options-by-question');

    // Load options for a given question group (for block creation)
    Route::get('options-by-group/{groupId}', function (int $groupId) {
        $group = \App\Models\Question\QuestionGroup::findOrFail($groupId);
        $options = \App\Models\Question\QuestionOptions::where('question_id', $group->question_id)
            ->orderBy('sort_order')
            ->get(['id', 'actual_question', 'option_text', 'question_type']);
        return response()->json($options);
    })->name('options-by-group');
});
