<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = User::with('roles')
            ->whereHas('roles', fn($q) => $q->where('slug', 'admin'))
            ->whereNotNull('payment_submitted_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        match ($status) {
            'pending'  => $query->where('account_status', 'payment_submitted'),
            'active'   => $query->where('account_status', 'active'),
            'rejected' => $query->where('account_status', 'rejected'),
            default    => null,
        };

        $submissions = $query->latest('payment_submitted_at')->paginate(25)->withQueryString();

        return view('superadmin.payments.index', compact('submissions', 'search', 'status'));
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
