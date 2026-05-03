<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $year  = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $month = max(1, min(12, $month));
        $year  = max(2000, min(2100, $year));

        $current     = Carbon::create($year, $month, 1);
        $prev        = $current->copy()->subMonth();
        $next        = $current->copy()->addMonth();
        $daysInMonth = $current->daysInMonth;
        $startDow    = $current->dayOfWeek;

        $tenantId = auth()->user()->tenantId();

        // ── Events (birthday, anniversary, visit, custom) ─────────────
        $eventsQuery = Event::with('client');
        if ($tenantId) {
            $eventsQuery->whereHas('client', fn($q) => $q->where('tenant_id', $tenantId));
        }

        $calendarData = [];

        foreach ($eventsQuery->get() as $event) {
            foreach ($this->getEventDaysInMonth($event, $year, $month) as $day) {
                $calendarData[$day][] = [
                    'client_name' => $event->client?->name ?? '—',
                    'client_url'  => $event->client ? route('clients.show', $event->client) : null,
                    'type'        => $event->typeLabel(),
                    'badge'       => $event->badgeClass(),
                    'label'       => $event->label,
                    'phone'       => $event->client?->phone,
                ];
            }
        }

        // ── Client next_visit_date ─────────────────────────────────────
        $visitQuery = Client::whereNotNull('next_visit_date')
            ->whereYear('next_visit_date', $year)
            ->whereMonth('next_visit_date', $month);
        if ($tenantId) {
            $visitQuery->where('tenant_id', $tenantId);
        }

        foreach ($visitQuery->get() as $client) {
            $day = $client->next_visit_date->day;
            $calendarData[$day][] = [
                'client_name' => $client->name,
                'client_url'  => route('clients.show', $client),
                'type'        => 'Next Visit',
                'badge'       => 'badge-visit',
                'label'       => null,
                'phone'       => $client->phone,
            ];
        }

        // Sort each day's events: visits last, others first
        foreach ($calendarData as &$dayItems) {
            usort($dayItems, fn($a, $b) => strcmp($a['type'], $b['type']));
        }
        unset($dayItems);

        return view('calendar.index', compact(
            'current', 'prev', 'next',
            'year', 'month', 'daysInMonth', 'startDow',
            'calendarData'
        ));
    }

    private function getEventDaysInMonth(Event $event, int $year, int $month): array
    {
        $days       = [];
        $base       = $event->event_date->copy()->startOfDay();
        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd   = $monthStart->copy()->endOfMonth()->startOfDay();

        switch ($event->recurrence) {
            case 'annual':
                if ($base->month === $month && $base->day <= $monthStart->daysInMonth) {
                    $days[] = $base->day;
                }
                break;

            case 'monthly':
                if ($monthStart->gte($base->copy()->startOfMonth())) {
                    $day = $base->day;
                    if ($day <= $monthStart->daysInMonth) {
                        $days[] = $day;
                    }
                }
                break;

            case 'weekly':
            case 'biweekly':
                $interval = $event->recurrence === 'weekly' ? 7 : 14;
                $cursor   = $base->copy();
                if ($cursor->lt($monthStart)) {
                    $steps = (int) floor($cursor->diffInDays($monthStart) / $interval);
                    $cursor->addDays($steps * $interval);
                }
                while ($cursor->lte($monthEnd)) {
                    if ($cursor->year === $year && $cursor->month === $month) {
                        $days[] = $cursor->day;
                    }
                    $cursor->addDays($interval);
                }
                break;

            case 'none':
            default:
                if ($base->year === $year && $base->month === $month) {
                    $days[] = $base->day;
                }
                break;
        }

        return $days;
    }
}
