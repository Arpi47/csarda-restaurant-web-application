<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'is_super_admin',
        'is_suspended',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'is_suspended' => 'boolean',
        ];
    }

    public function getProfileImageUrlAttribute(): string
    {
        if (
            $this->profile_image &&
            file_exists(public_path($this->profile_image))
        ) {
            return asset($this->profile_image);
        }
        return asset('images/default-admin.png');
    }
}
