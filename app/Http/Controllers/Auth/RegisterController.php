<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create($data);

        // Assign default role if it exists (seeded via RolesPermissionsSeeder)
        $defaultRole = Role::where('slug', 'user')->first();
        if ($defaultRole) {
            $user->assignRole($defaultRole);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
