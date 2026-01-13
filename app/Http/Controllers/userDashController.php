<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class userDashController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        return Inertia::render("Dashboard", [
            'user' => $user ? $user->only(['id', 'name', 'email']) : null
        ]);
    }

    public function userTrxHistory()
    {
        return Inertia::render("TrxHistory");
    }
}
