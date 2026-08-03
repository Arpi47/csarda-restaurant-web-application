<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialOpeningHour extends Model
{
    protected $fillable = [
        'type',
        'date',
        'is_active',
        'open_time',
        'close_time',
        'last_reservation_time',
        'is_google_calendar',
        'google_calendar_event_id',
        'is_manually_overridden',
        'is_manually_deleted',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
        'is_google_calendar' => 'boolean',
        'is_manually_overridden' => 'boolean',
        'is_manually_deleted' => 'boolean',
    ];
}
