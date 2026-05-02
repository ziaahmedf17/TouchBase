<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $adminRole = Role::where('slug', 'admin')->first();

        $stats = [
            'total_admins'    => $adminRole ? $adminRole->users()->count() : 0,
            'total_sub_users' => User::whereNotNull('tenant_id')->count(),
            'total_clients'   => Client::count(),
            'total_tickets'   => Ticket::count(),
            'open_tickets'    => Ticket::where('status', 'open')->count(),
            'working_tickets' => Ticket::where('status', 'working_on_it')->count(),
        ];

        $recentAdmins = User::with('roles')
            ->whereHas('roles', fn($q) => $q->where('slug', 'admin'))
            ->latest()
            ->take(5)
            ->get();

        $recentTickets = Ticket::with('user')
            ->whereIn('status', ['open', 'working_on_it'])
            ->latest()
            ->take(5)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recentAdmins', 'recentTickets'));
    }
}
