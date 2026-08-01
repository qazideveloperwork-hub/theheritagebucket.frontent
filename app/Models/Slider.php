<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slider extends Model
{
    public const TYPE_HOME_BANNER = 'home_banner';

    public const TYPE_HOME_CATEGORY = 'home_category';

    public const TYPE_HOME_NEW_ARRIVAL = 'home_new_arrival';

    public const TYPE_ARTISAN_STORY = 'artisan_story';

    public const TYPE_FAVOURITE_PRODUCT = 'favourite_product';

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
