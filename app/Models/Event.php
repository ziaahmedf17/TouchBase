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
        'is_annual',
        'reminder_days',
    ];

    protected $casts = [
        'event_date'    => 'date',
        'is_annual'     => 'boolean',
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

    /**
     * For annual events: next occurrence from today.
     * For one-time events: the event_date itself.
     */
    public function nextOccurrence(): Carbon
    {
        if (! $this->is_annual) {
            return $this->event_date->copy();
        }

        $today = now()->startOfDay();
        $occurrence = Carbon::create(
            $today->year,
            $this->event_date->month,
            $this->event_date->day
        );

        if ($occurrence->lt($today)) {
            $occurrence->addYear();
        }

        return $occurrence;
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
