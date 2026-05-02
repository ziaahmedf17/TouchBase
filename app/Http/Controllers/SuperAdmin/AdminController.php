<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $plan   = $request->input('plan');

        $query = User::with('roles')
            ->withCount(['subUsers', 'clients'])
            ->whereHas('roles', fn($q) => $q->where('slug', 'admin'));

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        match ($status) {
            'active'    => $query->where('account_status', 'active')->where('is_suspended', false),
            'pending'   => $query->where('account_status', 'payment_submitted'),
            'suspended' => $query->where('is_suspended', true),
            default     => null,
        };

        if ($plan === 'none') {
            $query->whereNull('plan_type');
        } elseif (in_array($plan, ['monthly', 'yearly', 'lifetime'])) {
            $query->where('plan_type', $plan);
        }

        $admins = $query->latest()->paginate(20)->withQueryString();

        return view('superadmin.admins.index', compact('admins', 'search', 'status', 'plan'));
    }

    public function show(User $admin)
    {
        $admin->loadCount(['subUsers', 'clients']);
        $admin->load('subUsers.roles');
        $recentClients = $admin->clients()->latest()->take(5)->get();
        $tickets       = \App\Models\Ticket::where('user_id', $admin->id)->latest()->take(5)->get();
        $plans         = Plan::all()->keyBy('slug');
        return view('superadmin.admins.show', compact('admin', 'recentClients', 'tickets', 'plans'));
    }

    public function create()
    {
        return view('superadmin.admins.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create($data);
        $user->assignRole('admin');

        return redirect()->route('superadmin.admins.index')
            ->with('success', "Admin \"{$user->name}\" created.");
    }

    public function edit(User $admin)
    {
        return view('superadmin.admins.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $admin->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $admin->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            ...($data['password'] ? ['password' => $data['password']] : []),
        ]);

        return redirect()->route('superadmin.admins.index')
            ->with('success', "Admin \"{$admin->name}\" updated.");
    }

    public function destroy(User $admin)
    {
        $name = $admin->name;
        $admin->roles()->detach();
        $admin->subUsers()->each(function ($user) {
            $user->roles()->detach();
            $user->delete();
        });
        $admin->clients()->each(fn($c) => $c->delete());
        $admin->delete();

        return redirect()->route('superadmin.admins.index')
            ->with('success', "Admin \"{$name}\" and all their data deleted.");
    }

    public function clients(User $admin)
    {
        $clients = $admin->clients()->withCount('events', 'notifications')->orderBy('name')->paginate(20);
        return view('superadmin.admins.clients', compact('admin', 'clients'));
    }

    public function suspend(User $admin)
    {
        $admin->update(['is_suspended' => true]);
        return back()->with('success', "Account for \"{$admin->name}\" has been suspended.");
    }

    public function unsuspend(User $admin)
    {
        $admin->update(['is_suspended' => false]);
        return back()->with('success', "Account for \"{$admin->name}\" has been reactivated.");
    }

    public function setPlan(Request $request, User $admin)
    {
        $data = $request->validate([
            'plan_type' => 'required|in:monthly,yearly,lifetime',
        ]);

        $plan = Plan::where('slug', $data['plan_type'])->firstOrFail();

        $admin->update([
            'plan_type'       => $plan->slug,
            'plan_started_at' => now(),
            'plan_expires_at' => $plan->expiresAt(),
        ]);

        return back()->with('success', "Plan updated to {$plan->name} for \"{$admin->name}\".");
    }
}
