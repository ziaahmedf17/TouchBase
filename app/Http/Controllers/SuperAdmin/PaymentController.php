<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
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
        return view('superadmin.payments.show', compact('admin'));
    }

    public function screenshot(string $filename)
    {
        $path = storage_path('app/private/payments/' . basename($filename));
        abort_unless(file_exists($path), 404);
        return response()->file($path);
    }

    public function approve(User $admin)
    {
        $admin->update(['account_status' => 'active']);
        return redirect()->route('superadmin.payments.index')
            ->with('success', "Account for \"{$admin->name}\" approved. Remember to call them on {$admin->phone}.");
    }

    public function reject(User $admin)
    {
        $admin->update(['account_status' => 'rejected']);
        return redirect()->route('superadmin.payments.index')
            ->with('success', "Account for \"{$admin->name}\" rejected.");
    }
}
