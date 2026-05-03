<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'gender',
        'phone',
        'address',
        'notes',
        'next_visit_date',
        'visit_reminder_days',
    ];

    protected $casts = [
        'next_visit_date'     => 'date',
        'visit_reminder_days' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    public function whatsappUrl(): string
    {
        $phone = preg_replace('/\D/', '', $this->phone ?? '');
        return "https://wa.me/{$phone}";
    }

    public function telUrl(): string
    {
        return "tel:{$this->phone}";
    }
}
