<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PaymentAccount;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        $plans    = Plan::all()->keyBy('slug');
        return view('auth.register-payment', compact('accounts', 'plans'));
    }

    public function startTrial(Request $request)
    {
        if (!session('register_data')) {
            return redirect()->route('register');
        }

        $data = session('register_data');

        $user = User::create([
            ...$data,
            'account_status'  => 'active',
            'plan_type'       => 'trial',
            'plan_started_at' => now(),
            'plan_expires_at' => now()->addDays(14),
        ]);

        $user->assignRole('admin');
        session()->forget('register_data');
        Auth::login($user);

        return redirect()->route('dashboard')->with('trial_started', true);
    }

    public function storePayment(Request $request)
    {
        if (!session('register_data')) {
            return redirect()->route('register');
        }

        $request->validate([
            'plan_type'  => 'required|in:monthly,yearly,lifetime',
            'screenshot' => 'required|file|mimes:jpg,jpeg,png,gif,pdf|max:5120',
        ], [
            'plan_type.required'  => 'Please select a subscription plan.',
            'screenshot.required' => 'Please upload your payment screenshot.',
            'screenshot.mimes'    => 'Only JPG, PNG, GIF, or PDF files are accepted.',
            'screenshot.max'      => 'File size must not exceed 5 MB.',
        ]);

        $data     = session('register_data');
        $ext      = $request->file('screenshot')->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . strtolower($ext);
        $request->file('screenshot')->storeAs('payments', $filename, 'local');

        $user = User::create([
            ...$data,
            'account_status'       => 'payment_submitted',
            'payment_screenshot'   => $filename,
            'payment_submitted_at' => now(),
            'plan_type'            => $request->plan_type,
        ]);

        $user->assignRole('admin');
        session()->forget('register_data');
        Auth::login($user);

        return redirect()->route('account.pending');
    }

    public function showPending()
    {
        $user = Auth::user();
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }
        if ($user && $user->account_status === 'active') {
            return redirect()->route('dashboard');
        }
        $accounts = PaymentAccount::where('is_active', true)->get();
        return view('auth.pending', compact('accounts'));
    }

    public function resubmitPayment(Request $request)
    {
        $user = Auth::user();
        if ($user->account_status === 'active') {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'screenshot' => 'required|file|mimes:jpg,jpeg,png,gif,pdf|max:5120',
        ], [
            'screenshot.required' => 'Please upload your payment screenshot.',
            'screenshot.mimes'    => 'Only JPG, PNG, GIF, or PDF files are accepted.',
            'screenshot.max'      => 'File size must not exceed 5 MB.',
        ]);

        if ($user->payment_screenshot) {
            Storage::disk('local')->delete('payments/' . $user->payment_screenshot);
        }

        $ext      = $request->file('screenshot')->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . strtolower($ext);
        $request->file('screenshot')->storeAs('payments', $filename, 'local');

        $user->update([
            'account_status'       => 'payment_submitted',
            'payment_screenshot'   => $filename,
            'payment_submitted_at' => now(),
        ]);

        return redirect()->route('account.pending')
            ->with('success', 'Payment screenshot resubmitted. Our team will review it shortly.');
    }

    public function updateContact(Request $request)
    {
        $user = Auth::user();
        if ($user->account_status === 'active') {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $user->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        return redirect()->route('account.pending')
            ->with('contact_updated', true);
    }

    public function showPaymentRequired()
    {
        $user = Auth::user();
        if (!$user->is_suspended) {
            return redirect()->route('dashboard');
        }
        $accounts = PaymentAccount::where('is_active', true)->get();
        $plans    = Plan::all()->keyBy('slug');
        return view('auth.payment-required', compact('accounts', 'plans'));
    }

    public function resubmitRenewal(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_suspended) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'plan_type'  => 'required|in:monthly,yearly,lifetime',
            'screenshot' => 'required|file|mimes:jpg,jpeg,png,gif,pdf|max:5120',
        ], [
            'plan_type.required'  => 'Please select a renewal plan.',
            'screenshot.required' => 'Please upload your payment screenshot.',
            'screenshot.mimes'    => 'Only JPG, PNG, GIF, or PDF files are accepted.',
            'screenshot.max'      => 'File size must not exceed 5 MB.',
        ]);

        if ($user->payment_screenshot) {
            Storage::disk('local')->delete('payments/' . $user->payment_screenshot);
        }

        $ext      = $request->file('screenshot')->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . strtolower($ext);
        $request->file('screenshot')->storeAs('payments', $filename, 'local');

        $user->update([
            'account_status'       => 'payment_submitted',
            'payment_screenshot'   => $filename,
            'payment_submitted_at' => now(),
            'plan_type'            => $request->plan_type,
        ]);

        return redirect()->route('account.payment_required')
            ->with('success', 'Renewal payment submitted. Our team will verify and reactivate your account shortly.');
    }

    public function showTrialExpired()
    {
        $user = Auth::user();
        if ($user->account_status === 'payment_submitted') {
            return redirect()->route('account.pending');
        }
        if ($user->account_status === 'active') {
            if (!$user->isOnTrial() || ($user->plan_expires_at && !$user->plan_expires_at->isPast())) {
                return redirect()->route('dashboard');
            }
        }
        $accounts = PaymentAccount::where('is_active', true)->get();
        $plans    = Plan::all()->keyBy('slug');
        return view('auth.trial-expired', compact('accounts', 'plans'));
    }

    public function submitTrialUpgrade(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'plan_type'  => 'required|in:monthly,yearly,lifetime',
            'screenshot' => 'required|file|mimes:jpg,jpeg,png,gif,pdf|max:5120',
        ], [
            'plan_type.required'  => 'Please select a subscription plan.',
            'screenshot.required' => 'Please upload your payment screenshot.',
            'screenshot.mimes'    => 'Only JPG, PNG, GIF, or PDF files are accepted.',
            'screenshot.max'      => 'File size must not exceed 5 MB.',
        ]);

        if ($user->payment_screenshot) {
            Storage::disk('local')->delete('payments/' . $user->payment_screenshot);
        }

        $ext      = $request->file('screenshot')->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . strtolower($ext);
        $request->file('screenshot')->storeAs('payments', $filename, 'local');

        $user->update([
            'account_status'       => 'payment_submitted',
            'payment_screenshot'   => $filename,
            'payment_submitted_at' => now(),
            'plan_type'            => $request->plan_type,
        ]);

        return redirect()->route('account.pending')
            ->with('success', 'Payment submitted! Our team will activate your subscription shortly.');
    }
}
