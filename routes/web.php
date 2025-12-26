<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', fn()=>view('auth.register'))->name('register');
Route::post('/register/send-otp',[RegisterController::class,'sendOtp'])->name('register.sendOtp');

Route::get('/verify-otp',[RegisterController::class,'verifyView'])->name('otp.verify.view');
Route::post('/verify-otp',[RegisterController::class,'verifyOtp'])->name('otp.verify');

Route::get('/login',[AuthController::class,'loginView'])->name('login');
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth')->group(function(){

    Route::get('/dashboard',[DashBoardController::class,'index'])->name('dashboard');

    Route::post('/logout',[AuthController::class,'logout'])->name('logout');

    Route::get('/profile', function(){
        return view('profile');
    })->name('profile');

});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::resource('modules', ModuleController::class)
            ->except(['show']);
});


Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [ExamController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/exam/{module}/start', [ExamController::class, 'start'])
        ->name('exam.start');

});
