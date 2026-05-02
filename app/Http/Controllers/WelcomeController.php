<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return Auth::user()->isSuperAdmin()
                ? redirect()->route('superadmin.dashboard')
                : redirect()->route('dashboard');
        }

        $plans = Plan::all()->keyBy('slug');
        return view('welcome', compact('plans'));
    }
}
