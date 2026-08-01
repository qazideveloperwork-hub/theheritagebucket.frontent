<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Setting;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class CartService
{
    public const COOKIE_NAME = 'heritage_cart_token';

    private const DEFAULT_MAX_QUANTITY = 5;

    public function current(): ?Cart
    {
        $token = request()->cookie(self::COOKIE_NAME);

        if (! $token) {
            return null;
        }

        return Cart::with(['items.product.images', 'items.product.shippingCost', 'items.variation.size'])->where('token', $token)->first();
    }

    public function currentOrCreate(): Cart
    {
        $cart = $this->current();

        if ($cart) {
            return $cart;
        }

        $token = (string) Str::uuid();

        Cookie::queue(self::COOKIE_NAME, $token, 60 * 24 * 365);

        return Cart::create(['token' => $token]);
    }

    public function adminMaxQuantity(): int
    {
        return (int) (Setting::first()?->max_quantity_per_product ?? self::DEFAULT_MAX_QUANTITY);
    }

    /**
     * The smaller of the admin-configured max-per-product setting and the item's
     * real stock limit (which is itself null — "no cap" — when out of stock is
     * ruled out and the admin has manually marked it in-stock).
     */
    public function maxAllowedQuantity(Product $product, ?ProductVariation $variation = null): int
    {
        $adminMax = $this->adminMaxQuantity();
        $stockLimit = $variation ? $variation->stockLimit() : $product->stockLimit();

        return $stockLimit === null ? $adminMax : min($adminMax, $stockLimit);
    }

    /**
     * Returns the cart plus whether the requested quantity had to be clamped
     * down to the max allowed, so the controller can surface a notice.
     */
    public function addItem(Product $product, int $quantity = 1, ?ProductVariation $variation = null): array
    {
        $quantity = max(1, $quantity);
        $cart = $this->currentOrCreate();
        $maxAllowed = $this->maxAllowedQuantity($product, $variation);

        $item = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variation_id', $variation?->id)
            ->first();

        $desiredTotal = ($item?->quantity ?? 0) + $quantity;
        $finalQuantity = min($desiredTotal, $maxAllowed);

        if ($item) {
            $item->update(['quantity' => $finalQuantity]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'product_variation_id' => $variation?->id,
                'quantity' => $finalQuantity,
                'unit_price' => $variation ? $variation->effectivePrice() : $product->effectivePrice(),
            ]);
        }

        return [
            'cart' => $cart->fresh(['items.product.images', 'items.product.shippingCost', 'items.variation.size']),
            'clamped' => $finalQuantity < $desiredTotal,
            'maxAllowed' => $maxAllowed,
        ];
    }

    public function updateItemQuantity(CartItem $item, int $quantity): array
    {
        if ($quantity < 1) {
            $item->delete();

            return ['clamped' => false, 'maxAllowed' => null];
        }

        $maxAllowed = $this->maxAllowedQuantity($item->product, $item->variation);
        $finalQuantity = min($quantity, $maxAllowed);
        $item->update(['quantity' => $finalQuantity]);

        return ['clamped' => $finalQuantity < $quantity, 'maxAllowed' => $maxAllowed];
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function ownsItem(CartItem $item): bool
    {
        $token = request()->cookie(self::COOKIE_NAME);

        return $token && $item->cart->token === $token;
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }
}
