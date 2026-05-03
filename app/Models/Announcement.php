<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['message', 'is_active', 'created_by'];

    protected $casts = ['is_active' => 'boolean'];
}
