<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $adminQuery = fn() => User::whereHas('roles', fn($q) => $q->where('slug', 'admin'));

        $stats = [
            'total_admins'    => $adminQuery()->count(),
            'active_admins'   => $adminQuery()->where('account_status', 'active')->where('is_suspended', false)->count(),
            'pending_admins'  => $adminQuery()->where('account_status', 'payment_submitted')->count(),
            'suspended_admins'=> $adminQuery()->where('is_suspended', true)->count(),
            'total_sub_users' => User::whereNotNull('tenant_id')->count(),
            'total_clients'   => Client::count(),
            'open_tickets'    => Ticket::where('status', 'open')->count(),
            'working_tickets' => Ticket::where('status', 'working_on_it')->count(),
        ];

        // Plan distribution (active + non-suspended admins only)
        $plans = Plan::all()->keyBy('slug');
        $planCounts = [];
        foreach (['monthly', 'yearly', 'lifetime'] as $slug) {
            $planCounts[$slug] = $adminQuery()
                ->where('account_status', 'active')
                ->where('plan_type', $slug)
                ->count();
        }

        // Upcoming renewals in next 30 days
        $upcomingRenewals = $adminQuery()
            ->where('account_status', 'active')
            ->where('is_suspended', false)
            ->whereNotNull('plan_expires_at')
            ->whereBetween('plan_expires_at', [now(), now()->addDays(30)])
            ->orderBy('plan_expires_at')
            ->get(['id', 'name', 'email', 'phone', 'plan_type', 'plan_expires_at']);

        // Admins needing action
        $pendingApprovals = $adminQuery()
            ->where('account_status', 'payment_submitted')
            ->latest('payment_submitted_at')
            ->get(['id', 'name', 'email', 'phone', 'business_type', 'plan_type', 'payment_submitted_at']);

        $suspendedAdmins = $adminQuery()
            ->where('is_suspended', true)
            ->latest()
            ->get(['id', 'name', 'email', 'phone', 'plan_type', 'plan_expires_at']);

        $recentAdmins = $adminQuery()->with('roles')->latest()->take(5)->get();

        $recentTickets = Ticket::with('user')
            ->whereIn('status', ['open', 'working_on_it'])
            ->latest()
            ->take(5)
            ->get();

        return view('superadmin.dashboard', compact(
            'stats', 'plans', 'planCounts', 'upcomingRenewals',
            'pendingApprovals', 'suspendedAdmins',
            'recentAdmins', 'recentTickets'
        ));
    }
}
