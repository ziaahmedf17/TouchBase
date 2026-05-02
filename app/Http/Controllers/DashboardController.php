<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Event;
use App\Models\Interaction;
use App\Models\Notification;
use App\Services\ReminderService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private ReminderService $reminders) {}

    public function index()
    {
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        $this->reminders->checkAndCreateNotifications();

        $tenantId = auth()->user()->tenantId();

        $clientQuery = $tenantId ? Client::where('tenant_id', $tenantId) : Client::query();
        $notifQuery  = $this->notifQuery($tenantId);

        // Clients not contacted in 30+ days
        $notContactedCount = (clone $clientQuery)
            ->whereNotIn('id',
                Interaction::select('client_id')
                    ->where('contacted_at', '>=', now()->subDays(30))
            )
            ->count();

        // Upcoming birthdays & anniversaries within 7 days
        $upcomingEvents = Event::with('client')
            ->whereIn('type', ['birthday', 'anniversary'])
            ->when($tenantId, fn($q) => $q->whereHas('client', fn($c) => $c->where('tenant_id', $tenantId)))
            ->get()
            ->filter(fn($e) =>
                $e->nextOccurrence()->gte(now()->startOfDay()) &&
                $e->nextOccurrence()->lte(now()->addDays(7)->endOfDay())
            )
            ->sortBy(fn($e) => $e->nextOccurrence())
            ->values();

        $stats = [
            'total_clients'        => (clone $clientQuery)->count(),
            'unread_notifications' => (clone $notifQuery)->where('is_read', false)->count(),
            'upcoming_visits'      => (clone $clientQuery)
                ->whereNotNull('next_visit_date')
                ->whereDate('next_visit_date', '>=', now())
                ->whereDate('next_visit_date', '<=', now()->addDays(7))
                ->count(),
            'not_contacted'        => $notContactedCount,
            'upcoming_events'      => $upcomingEvents->count(),
        ];

        $recentNotifications = (clone $notifQuery)->with('client', 'event')
            ->latest()->take(5)->get();

        $planAlert = auth()->user()->planAlertType();
        $daysLeft  = auth()->user()->daysUntilExpiry();

        return view('dashboard', compact('stats', 'recentNotifications', 'planAlert', 'daysLeft', 'upcomingEvents'));
    }

    public function updateAlerts()
    {
        $created = $this->reminders->checkAndCreateNotifications();

        $tenantId    = auth()->user()->tenantId();
        $notifQuery  = $this->notifQuery($tenantId);

        $unreadCount = (clone $notifQuery)->where('is_read', false)->count();

        $recentNotifications = (clone $notifQuery)->with('client', 'event')
            ->latest()->take(5)->get();

        $html = view('partials.recent_notifications', compact('recentNotifications'))->render();

        return response()->json([
            'created'      => $created,
            'unread_count' => $unreadCount,
            'unread_stat'  => $unreadCount,
            'message'      => $created > 0
                ? "{$created} new alert(s) created."
                : 'All alerts are up to date.',
            'html'         => $html,
        ]);
    }

    private function notifQuery(?int $tenantId)
    {
        $q = Notification::query();
        if ($tenantId) {
            $q->whereHas('client', fn($c) => $c->where('tenant_id', $tenantId));
        }
        return $q;
    }
}
