<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    // ── Authenticated: change password ─────────────────────────────
    public function showForm()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update(['password' => $request->password]);

        return back()->with('success', 'Password changed successfully.');
    }

    // ── Guest: forgot password (email + phone verification) ─────────
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function processForgot(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        $inputPhone = preg_replace('/\D/', '', $request->phone);
        $userPhone  = $user ? preg_replace('/\D/', '', $user->phone ?? '') : '';

        if (!$user || empty($userPhone) || $inputPhone !== $userPhone) {
            return back()->withInput()->withErrors([
                'email' => 'No account found with that email and phone combination.',
            ]);
        }

        session([
            'pw_reset_user_id' => $user->id,
            'pw_reset_expires' => now()->addMinutes(15)->timestamp,
        ]);

        return redirect()->route('password.reset.form');
    }

    public function showResetForm()
    {
        if (!$this->hasValidResetSession()) {
            return redirect()->route('password.forgot')
                ->withErrors(['expired' => 'Your reset session expired. Please try again.']);
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        if (!$this->hasValidResetSession()) {
            return redirect()->route('password.forgot')
                ->withErrors(['expired' => 'Your reset session expired.']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::find(session('pw_reset_user_id'));
        if (!$user) {
            return redirect()->route('password.forgot');
        }

        $user->update(['password' => $request->password]);
        session()->forget(['pw_reset_user_id', 'pw_reset_expires']);

        return redirect()->route('login')
            ->with('success', 'Password reset successfully. Please sign in with your new password.');
    }

    private function hasValidResetSession(): bool
    {
        return session()->has('pw_reset_user_id')
            && session('pw_reset_expires', 0) > now()->timestamp;
    }
}
