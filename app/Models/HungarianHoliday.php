<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HungarianHoliday extends Model
{
    protected $fillable = [
        'google_event_id',
        'name',
        'date',
        'restaurant_is_active',
        'restaurant_open_time',
        'restaurant_close_time',
        'restaurant_last_reservation_time',
        'kitchen_is_active',
        'kitchen_open_time',
        'kitchen_close_time',
        'kitchen_last_order_time',
    ];

    protected $casts = [
        'date' => 'date',
        'restaurant_is_active' => 'boolean',
        'kitchen_is_active' => 'boolean',
    ];
}
