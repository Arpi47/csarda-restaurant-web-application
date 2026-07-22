<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminActivityLog extends Model
{
    protected $fillable = [
        'admin_id', 'action', 'subject_type', 'subject_id',
        'route', 'method', 'ip_address', 'user_agent', 'meta'
    ];

    protected $appends = ['created_at_local'];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function getCreatedAtLocalAttribute()
    {
        $timezone = session('admin_timezone', config('app.timezone'));

        return $this->created_at
            ? $this->created_at->timezone($timezone)
            : null;
    }
}
