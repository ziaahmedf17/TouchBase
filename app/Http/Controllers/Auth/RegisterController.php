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
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|max:255|unique:users',
            'phone'                => 'required|string|max:20',
            'business_type'        => 'required|string',
            'custom_business_type' => 'required_if:business_type,others|nullable|string|max:100',
            'business_description' => 'required|string|max:500',
            'password'             => 'required|string|min:8|confirmed',
        ]);

        $businessType = $data['business_type'] === 'others'
            ? $data['custom_business_type']
            : $data['business_type'];

        $user = User::create([
            'name'                 => $data['name'],
            'email'                => $data['email'],
            'phone'                => $data['phone'],
            'business_type'        => $businessType,
            'business_description' => $data['business_description'],
            'password'             => $data['password'],
        ]);

        $defaultRole = Role::where('slug', 'admin')->first();
        if ($defaultRole) {
            $user->assignRole($defaultRole);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
