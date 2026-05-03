<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number',
        'user_id',
        'subject',
        'description',
        'status',
        'admin_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'open'          => 'Open',
            'working_on_it' => 'Working on it',
            'resolved'      => 'Resolved',
            'closed'        => 'Closed',
            default         => ucfirst($this->status),
        };
    }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'open'          => 'badge-info',
            'working_on_it' => 'badge-warning',
            'resolved'      => 'badge-success',
            'closed'        => 'badge-neutral',
            default         => 'badge-neutral',
        };
    }
}
