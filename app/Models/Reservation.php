<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'fname',
        'lname',
        'email',
        'date_time',
        'guests',
        'status',
        'language',
        'status_changed_at',
        'status_changed_by',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'status_changed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(
            Admin::class,
            'status_changed_by'
        );
    }
}
