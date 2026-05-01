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

        $events = Event::with('client')->get();

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
     * Returns which days of the given month/year this event falls on.
     * Annual events are matched by month+day. One-time events by exact date.
     */
    private function getEventDaysInMonth(Event $event, int $year, int $month): array
    {
        $days = [];

        if ($event->is_annual) {
            if ($event->event_date->month === $month) {
                $day = $event->event_date->day;
                $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
                if ($day <= $daysInMonth) {
                    $days[] = $day;
                }
            }
        } else {
            if ($event->event_date->year === $year && $event->event_date->month === $month) {
                $days[] = $event->event_date->day;
            }
        }

        return $days;
    }
}
