<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReservationEventType extends Model
{
    protected $fillable = [
        'name_en',
        'name_hu',
        'name_sr',
        'name_sr_cyrl',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(
            Reservation::class,
            'event_type_id'
        );
    }
}