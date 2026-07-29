<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpeningHour extends Model
{
    protected $fillable = [
        'day_of_week',
        'is_active',
        'open_time',
        'close_time',
        'last_reservation_time',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
