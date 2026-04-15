<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\UserController;
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

// Route::get('/register', fn()=>view('auth.register'))->name('register');
// Route::post('/register/send-otp',[RegisterController::class,'sendOTP'])->name('register.sendOtp');

Route::get('/show-otp',[RegisterController::class,'verifyView'])->name('otp.verify.view');
Route::post('/verify-otp',[RegisterController::class,'verifyOTP'])->name('otp.verify');

Route::get('/login',[RegisterController::class,'loginView'])->name('login');
Route::post('/login',[RegisterController::class,'validateLogin'])->name('login.submit');
Route::get('/logout',[RegisterController::class,'logout'])->name('logout');

Route::get('/dashboard', [RegisterController::class, 'dashboard'])->name('dashboard');

Route::middleware('auth')->group(function(){
    //Route::get('/dashboard',[DashBoardController::class,'index'])->name('dashboard.admin');

     Route::get('/dashboard-student', [ExamController::class, 'dashboard'])->name('dashboard.student');

    Route::get('/exam/{module}/start', [ExamController::class, 'start'])->name('exam.start');

    Route::get('/exam~/{examName}', [ExamController::class, 'showExams'])->name('exams.show');

    Route::post('/logout',[AuthController::class,'logout'])->name('logout');

    Route::get('/profile', function(){
        //return view('profile');
        dd(auth()->user());
    })->name('profile');

});

// Admin CRUD routes (routes/admin.php)
Route::middleware(['auth', 'admin'])
    ->prefix('admin2')
    ->name('admin.')
    ->group(base_path('routes/admin.php'));

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
