<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'client_id',
        'type',
        'label',
        'event_date',
        'recurrence',
        'reminder_days',
    ];

    protected $casts = [
        'event_date'    => 'date',
        'reminder_days' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /** Human-readable event type label */
    public function typeLabel(): string
    {
        return match ($this->type) {
            'birthday'    => 'Birthday',
            'anniversary' => 'Anniversary',
            'visit'       => 'Visit',
            'custom'      => $this->label ?: 'Custom Event',
            default       => ucfirst($this->type),
        };
    }

    public function recurrenceLabel(): string
    {
        return match ($this->recurrence) {
            'weekly'   => 'Weekly',
            'biweekly' => 'Bi-weekly',
            'monthly'  => 'Monthly',
            'annual'   => 'Annual',
            default    => 'None',
        };
    }

    /**
     * Next occurrence of this event from today, based on recurrence.
     */
    public function nextOccurrence(): Carbon
    {
        $today = now()->startOfDay();
        $base  = $this->event_date->copy()->startOfDay();

        if ($this->recurrence === 'none' || ! $this->recurrence) {
            return $base;
        }

        if ($base->gte($today)) {
            return $base;
        }

        switch ($this->recurrence) {
            case 'weekly':
                $weeks = (int) ceil($base->diffInDays($today) / 7);
                $next  = $base->copy()->addWeeks($weeks);
                if ($next->lt($today)) $next->addWeek();
                return $next;

            case 'biweekly':
                $periods = (int) ceil($base->diffInDays($today) / 14);
                $next    = $base->copy()->addDays($periods * 14);
                if ($next->lt($today)) $next->addDays(14);
                return $next;

            case 'monthly':
                $months = $base->diffInMonths($today);
                $next   = $base->copy()->addMonths($months);
                if ($next->lt($today)) $next->addMonth();
                return $next;

            case 'annual':
                $next = Carbon::create($today->year, $base->month, $base->day)->startOfDay();
                if ($next->lt($today)) $next->addYear();
                return $next;

            default:
                return $base;
        }
    }

    /** Badge colour for calendar/UI */
    public function badgeClass(): string
    {
        return match ($this->type) {
            'birthday'    => 'badge-birthday',
            'anniversary' => 'badge-anniversary',
            'visit'       => 'badge-visit',
            default       => 'badge-custom',
        };
    }
}
