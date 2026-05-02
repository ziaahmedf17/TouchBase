<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['price'];

    protected $casts = ['price' => 'decimal:2'];

    public function isLifetime(): bool
    {
        return $this->slug === 'lifetime';
    }

    public function expiresAt(): ?\Carbon\Carbon
    {
        if ($this->duration_days === 0) return null;
        return now()->addDays($this->duration_days);
    }

    public function formattedPrice(): string
    {
        return 'Rs. ' . number_format($this->price, 0);
    }
}
