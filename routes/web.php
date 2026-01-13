<?php

use App\Http\Controllers\appManager;
use App\Http\Controllers\userAuthController;
use App\Http\Controllers\userDashController;
use Illuminate\Support\Facades\Route;

Route::get('/', [appManager::class, "index"])->name('home');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [userDashController::class, "dashboard"])->name('dashboard');
    Route::get('/history', [userDashController::class, "userTrxHistory"])->name('history');
    Route::get('/logout', [userAuthController::class, "userLogout"])->name('userlogout');
});

Route::middleware(['checkSessingExist'])->group(function () {
    Route::get('/login', [appManager::class, "login"])->name('login');
    Route::get('/register', [appManager::class, "register"])->name('register');
    Route::post('/register-user', [userAuthController::class, 'registerUser'])->name("reguser");
    Route::post('/login-user', [userAuthController::class, 'loginUser'])->name("loginuser");
});
