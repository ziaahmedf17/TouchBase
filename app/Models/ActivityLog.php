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

    public function actionBadgeClass(): string
    {
        return match ($this->action) {
            'payment_approved',
            'admin_unsuspended' => 'badge-success',
            'payment_rejected',
            'admin_suspended'   => 'badge-danger',
            'plan_set'          => 'badge-info',
            'price_updated'     => 'badge-warning',
            default             => 'badge-neutral',
        };
    }
}
