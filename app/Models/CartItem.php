<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'product_variation_id', 'quantity', 'unit_price'];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function lineTotal(): float
    {
        return (float) $this->unit_price * $this->quantity;
    }

    /**
     * Resolves against the variation's own stock when this line item is a
     * specific size, otherwise the parent product's stock. Null means no
     * quantity-based cap applies (see Product/ProductVariation::stockLimit()).
     */
    public function stockLimit(): ?int
    {
        return $this->variation ? $this->variation->stockLimit() : $this->product->stockLimit();
    }

    public function isInStock(): bool
    {
        return $this->variation ? $this->variation->inStock() : $this->product->stock_status === 'in_stock';
    }
}
