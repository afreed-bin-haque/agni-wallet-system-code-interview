<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class appManager extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome');
    }

    public function dashboard()
    {
        return Inertia::render("Dashboard");
    }
    public function login()
    {
        return Inertia::render("Login");
    }
    public function register()
    {
        return Inertia::render("Register");
    }
}
