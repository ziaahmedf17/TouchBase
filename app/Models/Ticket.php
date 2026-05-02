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

    public function statusBadgeStyle(): string
    {
        return match($this->status) {
            'open'          => 'background:#dbeafe;color:#1d4ed8;',
            'working_on_it' => 'background:#fef9c3;color:#92400e;',
            'resolved'      => 'background:#dcfce7;color:#166534;',
            'closed'        => 'background:#f1f5f9;color:#64748b;',
            default         => '',
        };
    }
}
