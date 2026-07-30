<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'email_verified_at',
        'profile_image',
        'is_suspended',
        'deletion_requested',
        'deletion_requested_at',
        'deletion_will_be_final_at',
        'deletion_attempts_last_24h',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_suspended' => 'boolean',
        'deletion_requested' => 'boolean',
        'deletion_requested_at' => 'datetime',
        'deletion_will_be_final_at' => 'datetime',
        'deletion_attempts_last_24h' => 'integer',
    ];

    public function getFullNameAttribute(): string
    {
        return trim(
            "{$this->first_name} {$this->last_name}"
        );
    }

    public function getProfileImageUrlAttribute(): ?string
    {
        if (! $this->profile_image) {
            return null;
        }
        if (
            str_starts_with(
                $this->profile_image,
                'http'
            )
        ) {
            return $this->profile_image;
        }

        return asset(
            'storage/'.$this->profile_image
        );
    }

    public function canChangePassword(): bool
    {
        return ! is_null($this->password);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(
            SocialAccount::class
        );
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(
            new ResetPasswordNotification($token)
        );
    }

    public function hasGoogleAccount(): bool
    {
        return $this->socialAccounts
            ->contains('provider', 'google');
    }

    public function canBeEditedByAdmin(): bool
    {
        return $this->password !== null &&
            ! $this->hasGoogleAccount();
    }
}
