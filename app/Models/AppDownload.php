<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppDownload extends Model
{
    protected $fillable = [
        'platform',
        'url',
    ];
}