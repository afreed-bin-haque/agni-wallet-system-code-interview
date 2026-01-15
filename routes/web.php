<?php

use App\Http\Controllers\appManager;
use App\Http\Controllers\pdfTestController;
use App\Http\Controllers\userAuthController;
use App\Http\Controllers\userDashController;
use Illuminate\Support\Facades\Route;

Route::get('/', [appManager::class, "index"])->name('home');
Route::get('/test-pdf', [pdfTestController::class, "testPdf"])->name('testpdf');
Route::get('/language', [appManager::class, "changeLang"])->name('languageswitch');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [userDashController::class, "dashboard"])->name('dashboard');
    Route::get('/history', [userDashController::class, "userTrxHistory"])->name('history');
    Route::get('/add-balance', [userDashController::class, "addBalance"])->name('addbalance');
    Route::post('/request-to-add-balance', [userDashController::class, "requstAddBalance"])->name('reqaddbalance');
    Route::get('/logout', [userAuthController::class, "userLogout"])->name('userlogout');
});

Route::middleware(['checkSessingExist'])->group(function () {
    Route::get('/login', [appManager::class, "login"])->name('login');
    Route::get('/register', [appManager::class, "register"])->name('register');
    Route::post('/register-user', [userAuthController::class, 'registerUser'])->name("reguser");
    Route::post('/login-user', [userAuthController::class, 'loginUser'])->name("loginuser");
});
Route::get('/bkash', [userDashController::class, 'ExecutePayment'])->name('reroute.mfsp_bkash');
