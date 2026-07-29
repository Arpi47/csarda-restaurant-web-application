<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialOpeningHour extends Model
{
    protected $fillable = [
        'date',
        'is_active',
        'open_time',
        'close_time',
        'last_reservation_time',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];
}
