<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name_hu',
        'name_en',
        'name_sr_lat',
        'name_sr_cyr',
        'sort_order',
        'is_active',
    ];

    public function menu()
    {
        return $this->hasMany(Menu::class);
    }
}
