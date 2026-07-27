<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = [
        'category_id',
        'name_hu',
        'name_en',
        'name_sr_lat',
        'name_sr_cyr',
        'description_hu',
        'description_en',
        'description_sr_lat',
        'description_sr_cyr',
        'price',
        'image',
        'sort_order',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
