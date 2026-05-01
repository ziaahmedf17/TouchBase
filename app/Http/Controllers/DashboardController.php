<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\ReminderService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private ReminderService $reminders) {}

    public function index()
    {
        // Auto-check on every dashboard load
        $this->reminders->checkAndCreateNotifications();

        $stats = [
            'total_clients'        => \App\Models\Client::count(),
            'unread_notifications' => Notification::where('is_read', false)->count(),
            'upcoming_visits'      => \App\Models\Client::whereNotNull('next_visit_date')
                ->whereDate('next_visit_date', '>=', now())
                ->whereDate('next_visit_date', '<=', now()->addDays(7))
                ->count(),
        ];

        $recentNotifications = Notification::with('client', 'event')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentNotifications'));
    }

    /**
     * "Update Alerts" manual trigger — returns JSON for AJAX call.
     */
    public function updateAlerts()
    {
        $created = $this->reminders->checkAndCreateNotifications();

        $unreadCount = Notification::where('is_read', false)->count();

        return response()->json([
            'created'      => $created,
            'unread_count' => $unreadCount,
            'message'      => $created > 0
                ? "{$created} new alert(s) created."
                : 'All alerts are up to date.',
        ]);
    }
}
