<?php

use App\Http\Controllers\userDashController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/request-to-add-balance', [userDashController::class, "requstAddBalance"])->name('reqaddbalance');
