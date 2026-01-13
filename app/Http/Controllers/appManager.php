<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class appManager extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function login()
    {
        return Inertia::render("Login");
    }
    public function register()
    {
        return Inertia::render("Register");
    }
    public function changeLang(Request $request)
    {
        if (in_array($request->lang, ['en', 'bn'])) {
            session(['locale' => $request->lang]);
        }
        return response()->json(['status' => true]);
    }
}
