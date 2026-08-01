<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'size_id',
        'sku',
        'regular_price',
        'discount_price',
        'discount_percentage',
        'sale_price',
        'purchase_price',
        'stock_status',
        'stock_status_manual',
        'weight',
    ];

    protected function casts(): array
    {
        return [
            'regular_price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'purchase_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'stock_status_manual' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function info(): BelongsToMany
    {
        return $this->belongsToMany(Info::class, 'product_info')->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function availableQuantity(): int
    {
        return (int) ($this->inventory?->quantity ?? 0);
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

    public function inStock(): bool
    {
        return $this->stock_status === 'in_stock';
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
}
