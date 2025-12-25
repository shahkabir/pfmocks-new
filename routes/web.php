<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashBoardController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login',[AuthController::class,'loginView'])->name('login');
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth')->group(function(){

    Route::get('/dashboard',[DashBoardController::class,'index'])->name('dashboard');

    Route::post('/logout',[AuthController::class,'logout'])->name('logout');

    Route::get('/profile', function(){
        return view('profile');
    })->name('profile');

});
