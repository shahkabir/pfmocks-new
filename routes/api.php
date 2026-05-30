<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExamApiController;
use App\Http\Controllers\Api\ScholarshipApiController;

/*
|--------------------------------------------------------------------------
| Public API routes (auto-prefixed /api). Used by the business website.
|--------------------------------------------------------------------------
*/

Route::prefix('scholarships')->group(function () {
    Route::get('/',          [ScholarshipApiController::class, 'index'])->name('api.scholarships.index');
    Route::get('/meta',      [ScholarshipApiController::class, 'meta'])->name('api.scholarships.meta');
    Route::get('/{slug}',    [ScholarshipApiController::class, 'show'])->name('api.scholarships.show');
});

Route::prefix('exams')->group(function () {
    Route::get('/',      [ExamApiController::class, 'index'])->name('api.exams.index');
    Route::get('/{tag}', [ExamApiController::class, 'show'])->name('api.exams.show');
});
