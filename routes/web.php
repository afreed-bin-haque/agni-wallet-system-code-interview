<?php

use App\Http\Controllers\appManager;
use Illuminate\Support\Facades\Route;

Route::get('/', [appManager::class, "index"])->name('home');
Route::middleware(['dashSessionVerify'])->group(function () {
    Route::get('/dashboard', [appManager::class, "dashboard"])->name('dashboard');
});

Route::get('/login', [appManager::class, "login"])->name('dashboard');
Route::get('/register', [appManager::class, "register"])->name('register');
