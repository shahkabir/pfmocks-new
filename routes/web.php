<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Models\User;
use Termwind\Components\Raw;

// Route::get('/admin-lte', function () {
//     return view('layouts.index');
// });

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/csrf-token', function () {
    return response()->json([
        'token' => csrf_token()
    ]);
});

Route::get('/register', [RegisterController::class, 'registerView'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::get('/show-otp',[RegisterController::class,'verifyView'])->name('otp.verify.view');
Route::post('/verify-otp',[RegisterController::class,'verifyOTP'])->name('otp.verify');

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

Route::get('/login',[RegisterController::class,'loginView'])->name('login');
Route::post('/login',[RegisterController::class,'validateLogin'])->name('login.submit');
Route::get('/logout',[RegisterController::class,'logout'])->name('logout');

Route::get('/dashboard', [RegisterController::class, 'dashboard'])->name('dashboard');

Route::middleware('auth')->group(function(){
    //Route::get('/dashboard',[DashBoardController::class,'index'])->name('dashboard.admin');

    Route::get('/dashboard-student', [ExamController::class, 'dashboard'])->name('dashboard.student');

    Route::get('/exam/{module}/start', [ExamController::class, 'start'])->name('exam.start');
    Route::get('/exam/result/{userExamId}', [ExamController::class, 'showResult'])->name('exam.result');

    Route::get('/exam~/{examName}', [ExamController::class, 'showExams'])->name('exams.show');

    Route::post('/logout',[AuthController::class,'logout'])->name('logout');

    Route::get('/profile', function () {
        return view('profile', ['user' => auth()->user()]);
    })->name('profile');

    // ── Payment (student-facing) ─────────────────────────────────────────
    Route::get('/payment/module-info/{moduleId}',
        [\App\Http\Controllers\PaymentController::class, 'moduleInfo'])
        ->name('payment.module-info');

    Route::post('/payment/submit',
        [\App\Http\Controllers\PaymentController::class, 'submit'])
        ->name('payment.submit');

    // ── Referral (student-facing) ────────────────────────────────────────
    Route::get ('/referral',           [\App\Http\Controllers\ReferralController::class, 'index'])
        ->name('referral.index');
    Route::post('/referral/send-email',[\App\Http\Controllers\ReferralController::class, 'sendEmail'])
        ->name('referral.send-email');

    // ── SOP services (student-facing) ────────────────────────────────────
    Route::get ('/services/sop',
        [\App\Http\Controllers\SopController::class, 'index'])
        ->name('sop.index');
    Route::get ('/services/sop/module-info/{moduleId}',
        [\App\Http\Controllers\SopController::class, 'moduleInfo'])
        ->name('sop.module-info');
    Route::post('/services/sop/submit',
        [\App\Http\Controllers\SopController::class, 'submit'])
        ->name('sop.submit');
    Route::get ('/services/sop/{submissionId}/download/{type}',
        [\App\Http\Controllers\SopController::class, 'download'])
        ->name('sop.download');

    // ── Scholarships browse (student / evaluator) ──────────────────────
    Route::get('/scholarships',
        [\App\Http\Controllers\ScholarshipController::class, 'index'])
        ->name('scholarships.index');
});

// Admin CRUD routes (routes/admin.php)
Route::middleware(['auth', 'admin'])
    ->prefix('admin2')
    ->name('admin.')
    ->group(base_path('routes/admin.php'));

// Evaluator routes
Route::middleware(['auth', 'evaluator'])
    ->prefix('evaluator')
    ->name('evaluator.')
    ->group(function () {
        Route::get ('/evaluations',
            [\App\Http\Controllers\Evaluator\EvaluationController::class, 'index'])
            ->name('evaluations.index');

        Route::get ('/evaluations/{id}',
            [\App\Http\Controllers\Evaluator\EvaluationController::class, 'show'])
            ->name('evaluations.show');

        Route::post('/evaluations/{id}',
            [\App\Http\Controllers\Evaluator\EvaluationController::class, 'update'])
            ->name('evaluations.update');
    });

Route::get('/ielts-writing',function(){
    return view('exams.ielts.writing');
});

Route::post('/ielts-writing/submit', [ExamController::class, 'submitIELTSWriting'])
    ->name('exam.ielts.writing.submit');

Route::get('/ielts-reading',function(){
    return view('exams.ielts.reading');
});

Route::post('/ielts-reading/submit', [ExamController::class, 'submitIELTSReadingAndListening'])
    ->name('exam.ielts.reading.submit');

Route::get('/ielts-listening',function(){
    return view('exams.ielts.listening');
});

Route::post('/ielts-listening/submit', [ExamController::class, 'submitIELTSReadingAndListening'])
    ->name('exam.ielts.listening.submit');

Route::get('/ielts-speaking',function(){
    return view('exams.ielts.speaking');
});

Route::post('/speaking-upload-audio', [ExamController::class, 'submitIeltsSpeakingAudio'])
    ->name('exam.ielts.speaking.upload_audio');

Route::post('/ielts-speaking/submit', [ExamController::class, 'submitIeltsSpeakingFinalize'])
    ->name('exam.ielts.speaking.submit');

Route::post('/general-mcq/submit', [ExamController::class, 'submitGeneralMCQ'])
    ->name('exam.general.mcq.submit');

//CRUD: User Page to Admin
Route::controller(UserController::class)->group(function () {
    Route::get('user-list', [UserController::class, 'showUserList'])->name('admin.user.list');
    Route::get('user-list-data', [UserController::class, 'getUserListData'])->name('admin.user.list.data');
    Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('admin.user.edit');
    Route::put('user/update', [UserController::class, 'update'])->name('admin.user.update');
    Route::delete('user/delete/{id}', [UserController::class, 'destroy'])->name('admin.user.delete');

});
