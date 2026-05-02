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

        // Clamp to valid range
        $month = max(1, min(12, $month));
        $year  = max(2000, min(2100, $year));

        $current   = Carbon::create($year, $month, 1);
        $prev      = $current->copy()->subMonth();
        $next      = $current->copy()->addMonth();
        $daysInMonth = $current->daysInMonth;

        // Day of week the month starts on (0=Sun)
        $startDow = $current->dayOfWeek;

        // Build a map: day_number => [events]
        $calendarData = [];

        $tenantId = auth()->user()->tenantId();
        $eventsQuery = Event::with('client');
        if ($tenantId) {
            $eventsQuery->whereHas('client', fn($q) => $q->where('tenant_id', $tenantId));
        }
        $events = $eventsQuery->get();

        foreach ($events as $event) {
            $days = $this->getEventDaysInMonth($event, $year, $month);
            foreach ($days as $day) {
                $calendarData[$day][] = $event;
            }
        }

        return view('calendar.index', compact(
            'current', 'prev', 'next',
            'year', 'month', 'daysInMonth', 'startDow',
            'calendarData'
        ));
    }

    /**
     * Returns which days of the given month/year this event falls on,
     * based on its recurrence type.
     */
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
                // Only show from the month the event started
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
                // Fast-forward close to month start
                if ($cursor->lt($monthStart)) {
                    $steps  = (int) floor($cursor->diffInDays($monthStart) / $interval);
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
