<?php

namespace App\Models;

use App\Models\MenuReview;
use App\Models\MenuReviewComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(MenuImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(MenuReview::class);
    }

    public function reviewComments(): HasMany
    {
        return $this->hasMany(MenuReviewComment::class);
    }
}
