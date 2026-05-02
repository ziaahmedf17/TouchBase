<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'action',
        'description',
        'causer_id',
        'subject_type',
        'subject_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    public static function record(string $action, string $description, ?int $subjectId = null, string $subjectType = 'user'): void
    {
        static::create([
            'action'       => $action,
            'description'  => $description,
            'causer_id'    => Auth::id(),
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'created_at'   => now(),
        ]);
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            'payment_approved'   => 'Approved',
            'payment_rejected'   => 'Rejected',
            'admin_suspended'    => 'Suspended',
            'admin_unsuspended'  => 'Reactivated',
            'plan_set'           => 'Plan Set',
            'price_updated'      => 'Price Updated',
            default              => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    public function actionBadgeStyle(): string
    {
        return match ($this->action) {
            'payment_approved'  => 'background:#dcfce7;color:#166534;',
            'payment_rejected'  => 'background:#fee2e2;color:#991b1b;',
            'admin_suspended'   => 'background:#fee2e2;color:#991b1b;',
            'admin_unsuspended' => 'background:#dcfce7;color:#166534;',
            'plan_set'          => 'background:#dbeafe;color:#1e40af;',
            'price_updated'     => 'background:#fef3c7;color:#92400e;',
            default             => 'background:#f1f5f9;color:#475569;',
        };
    }
}
