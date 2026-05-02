<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PaymentAccount;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function storeStep1(Request $request)
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

        session([
            'register_data' => [
                'name'                 => $data['name'],
                'email'                => $data['email'],
                'phone'                => $data['phone'],
                'business_type'        => $businessType,
                'business_description' => $data['business_description'],
                'password'             => $data['password'],
            ],
        ]);

        return redirect()->route('register.payment');
    }

    public function showPayment()
    {
        if (!session('register_data')) {
            return redirect()->route('register');
        }
        $accounts = PaymentAccount::where('is_active', true)->get();
        return view('auth.register-payment', compact('accounts'));
    }

    public function storePayment(Request $request)
    {
        if (!session('register_data')) {
            return redirect()->route('register');
        }

        $request->validate([
            'screenshot' => 'required|file|mimes:jpg,jpeg,png,gif,pdf|max:5120',
        ], [
            'screenshot.required' => 'Please upload your payment screenshot.',
            'screenshot.mimes'    => 'Only JPG, PNG, GIF, or PDF files are accepted.',
            'screenshot.max'      => 'File size must not exceed 5 MB.',
        ]);

        $data = session('register_data');
        $ext  = $request->file('screenshot')->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . strtolower($ext);
        $request->file('screenshot')->storeAs('payments', $filename, 'local');

        $user = User::create([
            ...$data,
            'account_status'       => 'payment_submitted',
            'payment_screenshot'   => $filename,
            'payment_submitted_at' => now(),
        ]);

        $user->assignRole('admin');
        session()->forget('register_data');
        Auth::login($user);

        return redirect()->route('account.pending');
    }

    public function showPending()
    {
        $user = Auth::user();
        if ($user && $user->account_status === 'active') {
            return redirect()->route('dashboard');
        }
        return view('auth.pending');
    }
}
