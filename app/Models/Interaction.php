<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaction extends Model
{
    protected $fillable = [
        'client_id',
        'notification_id',
        'type',
        'status',
        'notes',
        'response_notes',
        'contacted_at',
        'response_at',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
        'response_at'  => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'call'     => 'Call',
            'whatsapp' => 'WhatsApp',
            'email'    => 'Email',
            'visit'    => 'Visit',
            default    => 'Other',
        };
    }

    public function typeBadgeClass(): string
    {
        return match ($this->type) {
            'call'     => 'badge-visit',
            'whatsapp' => 'badge-birthday',
            'email'    => 'badge-anniversary',
            'visit'    => 'badge-custom',
            default    => 'badge-secondary',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'reached_out'      => 'Reached Out',
            'no_response'      => 'No Response',
            'responded'        => 'Responded',
            'follow_up_needed' => 'Follow-up Needed',
            default            => ucfirst($this->status),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'reached_out'      => 'badge-reached',
            'no_response'      => 'badge-no-response',
            'responded'        => 'badge-responded',
            'follow_up_needed' => 'badge-followup',
            default            => '',
        };
    }
}
