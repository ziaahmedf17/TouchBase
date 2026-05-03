<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    protected $fillable = [
        'client_id',
        'event_id',
        'message',
        'is_read',
        'triggered_date',
    ];

    protected $casts = [
        'is_read'        => 'boolean',
        'triggered_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    public function whatsappMessage(): string
    {
        $name     = $this->client?->name ?? 'there';
        $biz      = $this->client?->tenant?->business_name;
        $sign     = $biz ? "\n\n— {$biz}" : '';

        return match ($this->event?->type) {
            'birthday'    => "Happy Birthday {$name}! 🎂\nWishing you a wonderful day full of joy and happiness.{$sign}",
            'anniversary' => "Happy Anniversary {$name}! 🎉\nCongratulations on this special milestone.{$sign}",
            'visit'       => "Hi {$name}, 👋\nJust a friendly reminder for your upcoming visit.\nLooking forward to seeing you! 😊{$sign}",
            default       => "Hi {$name}, 😊\nJust a friendly reminder from us.\nHope to connect with you soon!{$sign}",
        };
    }
}
