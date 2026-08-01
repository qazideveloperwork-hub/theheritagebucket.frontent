<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = ['token'];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function subtotal(): float
    {
        return (float) $this->items->sum(fn (CartItem $item) => $item->unit_price * $item->quantity);
    }

    private const DEFAULT_SHIPPING_COST = 250.00;

    /**
     * Each cart line item contributes its product's shipping cost once, regardless of
     * quantity — ordering more of the same item doesn't multiply its shipping cost.
     * Products with no shipping cost set fall back to a flat default.
     */
    public function shippingCost(): float
    {
        return (float) $this->items->sum(
            fn (CartItem $item) => (float) ($item->product->shippingCost?->shipping_cost ?? self::DEFAULT_SHIPPING_COST)
        );
    }

    public function itemCount(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
