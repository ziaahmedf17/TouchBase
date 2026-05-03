<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];

        if ($user->isAdmin()) {
            $rules['business_name'] = 'nullable|string|max:100';
        }

        $data = $request->validate($rules);

        $update = [
            'name'  => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'],
            ...($data['password'] ? ['password' => $data['password']] : []),
        ];

        if ($user->isAdmin()) {
            $update['business_name'] = $data['business_name'] ?? null;
        }

        $user->update($update);

        return back()->with('success', 'Profile updated successfully.');
    }
}
