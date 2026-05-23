<?php

use Illuminate\Support\Facades\Route;
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
