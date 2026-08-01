<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'type',
        'name',
        'slug',
        'sku',
        'regular_price',
        'discount_price',
        'discount_percentage',
        'sale_price',
        'stock_status',
        'stock_status_manual',
        'cover_image_url',
        'artisan_name',
        'artisan_bio',
        'artisan_image_url',
        'dimensions',
        'material',
        'care_instructions',
        'badge',
        'is_featured',
        'is_new_arrival',
        'is_coming_soon',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'regular_price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'care_instructions' => 'array',
            'is_featured' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_coming_soon' => 'boolean',
            'stock_status_manual' => 'boolean',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'product_colors');
    }

    public function colorMedia(): HasMany
    {
        return $this->hasMany(ProductColorMedia::class)->orderBy('sort_order');
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_related', 'product_id', 'related_product_id')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function infos(): BelongsToMany
    {
        return $this->belongsToMany(Info::class, 'product_infos')->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function shippingCost(): HasOne
    {
        return $this->hasOne(ProductShippingCost::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function isVariable(): bool
    {
        return $this->type === 'variable';
    }

    /**
     * Simple products only — variable products carry no stock of their own,
     * it lives on each ProductVariation's own inventory row instead.
     */
    public function availableQuantity(): int
    {
        return (int) ($this->inventory?->quantity ?? 0);
    }

    /**
     * Null means "no quantity-based cap" — either out of stock (0) or the admin
     * has manually marked it in-stock, in which case the tracked quantity isn't
     * authoritative and shouldn't artificially limit how many can be added.
     */
    public function stockLimit(): ?int
    {
        if ($this->stock_status === 'out_of_stock') {
            return 0;
        }

        return $this->stock_status_manual ? null : $this->availableQuantity();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function galleryMedia(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'product_media')->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function description(): HasOne
    {
        return $this->hasOne(ProductDescription::class);
    }

    public function getDisplayPriceAttribute(): string
    {
        return number_format((float) ($this->discount_price ?? $this->regular_price), 0);
    }

    /**
     * Same sale_price-over-discount_price priority as the storefront badge logic
     * (see product-badge.blade.php): sale_price wins if set, then discount_price.
     */
    public function currentPrice(): ?float
    {
        if ($this->sale_price) {
            return (float) $this->sale_price;
        }

        if ($this->discount_price && $this->discount_percentage) {
            return (float) $this->discount_price;
        }

        return null;
    }

    public function effectivePrice(): float
    {
        return $this->currentPrice() ?? (float) $this->regular_price;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function coverImageUrl(): Attribute
    {
        return Attribute::make(get: fn (?string $value) => static::resolveMediaUrl($value));
    }

    protected function floatingImageUrl(): Attribute
    {
        return Attribute::make(get: fn (?string $value) => static::resolveMediaUrl($value));
    }

    protected function artisanImageUrl(): Attribute
    {
        return Attribute::make(get: fn (?string $value) => static::resolveMediaUrl($value));
    }

    public static function resolveMediaUrl(?string $value): ?string
    {
        if (blank($value) || Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return Setting::mediaBaseUrl().'/'.ltrim($value, '/');
    }
}
