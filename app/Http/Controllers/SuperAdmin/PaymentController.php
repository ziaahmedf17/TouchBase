<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $submissions = User::with('roles')
            ->whereHas('roles', fn($q) => $q->where('slug', 'admin'))
            ->whereNotNull('payment_submitted_at')
            ->latest('payment_submitted_at')
            ->paginate(25);

        return view('superadmin.payments.index', compact('submissions'));
    }

    public function show(User $admin)
    {
        $plans = Plan::all()->keyBy('slug');
        return view('superadmin.payments.show', compact('admin', 'plans'));
    }

    public function screenshot(string $filename)
    {
        $path = storage_path('app/private/payments/' . basename($filename));
        abort_unless(file_exists($path), 404);
        return response()->file($path);
    }

    public function approve(Request $request, User $admin)
    {
        $request->validate([
            'plan_type' => 'required|in:monthly,yearly,lifetime',
        ]);

        $plan = Plan::where('slug', $request->plan_type)->firstOrFail();

        $admin->update([
            'account_status'  => 'active',
            'is_suspended'    => false,
            'plan_type'       => $plan->slug,
            'plan_started_at' => now(),
            'plan_expires_at' => $plan->expiresAt(),
        ]);

        return redirect()->route('superadmin.payments.index')
            ->with('success', "Account for \"{$admin->name}\" approved ({$plan->name} plan). Remember to call them on {$admin->phone}.");
    }

    public function reject(User $admin)
    {
        $admin->update(['account_status' => 'rejected']);
        return redirect()->route('superadmin.payments.index')
            ->with('success', "Account for \"{$admin->name}\" rejected.");
    }
}
