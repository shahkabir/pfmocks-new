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

Route::get('/admin-lte', function () {
    return view('layouts.index');
});

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/register', fn()=>view('auth.register'))->name('register');
Route::post('/register/send-otp',[RegisterController::class,'sendOtp'])->name('register.sendOtp');

Route::get('/verify-otp',[RegisterController::class,'verifyView'])->name('otp.verify.view');
Route::post('/verify-otp',[RegisterController::class,'verifyOtp'])->name('otp.verify');

Route::get('/login',[RegisterController::class,'loginView'])->name('login');
Route::post('/login',[RegisterController::class,'login']);

Route::middleware('auth')->group(function(){

    Route::get('/dashboard',[DashBoardController::class,'index'])->name('dashboard.admin');

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
        ->name('dashboard.student');

    Route::get('/exam/{module}/start', [ExamController::class, 'start'])
        ->name('exam.start');

});

//CRUD: User Page to Admin
Route::controller(UserController::class)->group(function () {
    Route::get('user-list', [UserController::class, 'showUserList'])->name('admin.user.list');
    Route::get('user-list-data', [UserController::class, 'getUserListData'])->name('admin.user.list.data');
    Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('admin.user.edit');
    Route::put('user/update', [UserController::class, 'update'])->name('admin.user.update');
    Route::delete('user/delete/{id}', [UserController::class, 'destroy'])->name('admin.user.delete');

});
