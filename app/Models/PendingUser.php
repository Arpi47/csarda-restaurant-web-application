<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'profile_image',
        'language',
        'verification_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
