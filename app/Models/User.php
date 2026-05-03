<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'business_name',
        'business_type',
        'business_description',
        'password',
        'tenant_id',
        'account_status',
        'payment_screenshot',
        'payment_submitted_at',
        'plan_type',
        'plan_started_at',
        'plan_expires_at',
        'is_suspended',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'    => 'datetime',
        'payment_submitted_at' => 'datetime',
        'plan_started_at'      => 'datetime',
        'plan_expires_at'      => 'datetime',
        'is_suspended'         => 'boolean',
        'password'             => 'hashed',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function subUsers(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'tenant_id');
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles->contains('slug', $slug);
    }

    public function hasPermission(string $slug): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($slug)) return true;
        }
        return false;
    }

    public function isSuperAdmin(): bool { return $this->hasRole('super_admin'); }
    public function isAdmin(): bool      { return $this->hasRole('admin'); }
    public function isActive(): bool     { return $this->account_status === 'active'; }

    public function tenantId(): ?int
    {
        if ($this->isSuperAdmin()) return null;
        if ($this->isAdmin())      return $this->id;
        return $this->tenant_id;
    }

    public function assignRole(string|Role $role): void
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }
        $this->roles()->syncWithoutDetaching($role);
    }

    // ── Account status helpers ──────────────────────────────────

    public function accountStatusLabel(): string
    {
        return match ($this->account_status) {
            'payment_submitted' => 'Pending Approval',
            'active'            => 'Active',
            'rejected'          => 'Rejected',
            default             => ucfirst($this->account_status ?? ''),
        };
    }

    public function accountStatusBadgeClass(): string
    {
        return match ($this->account_status) {
            'payment_submitted' => 'badge-warning',
            'active'            => 'badge-success',
            'rejected'          => 'badge-danger',
            default             => 'badge-neutral',
        };
    }

    // ── Plan / subscription helpers ─────────────────────────────

    public function planLabel(): string
    {
        return match ($this->plan_type) {
            'monthly'  => 'Monthly',
            'yearly'   => 'Yearly',
            'lifetime' => 'Lifetime',
            default    => 'No Plan',
        };
    }

    public function daysUntilExpiry(): ?int
    {
        if (!$this->plan_expires_at) return null;
        return (int) now()->diffInDays($this->plan_expires_at, false);
    }

    /**
     * Returns alert type for the admin dashboard:
     * 'expiring' (1–14 days left), 'grace' (0 to -7 days past expiry), or null.
     */
    public function planAlertType(): ?string
    {
        $days = $this->daysUntilExpiry();
        if ($days === null) return null;
        if ($days >= 0 && $days <= 14) return 'expiring';
        if ($days < 0 && $days >= -7)  return 'grace';
        return null;
    }
}
