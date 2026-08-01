<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

/**
 * Mirrors CartService's guest-identity pattern: a signed-in user's wishlist
 * rows are keyed by user_id, a guest's by a long-lived cookie token, so
 * saved items survive across visits without needing an account.
 */
class WishlistService
{
    public const COOKIE_NAME = 'heritage_wishlist_token';

    public function has(Product $product): bool
    {
        return $this->query()->where('product_id', $product->id)->exists();
    }

    /**
     * Returns true if the product is now saved, false if it was just removed.
     */
    public function toggle(Product $product): bool
    {
        $existing = $this->query()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        if (auth()->check()) {
            Wishlist::create(['user_id' => auth()->id(), 'product_id' => $product->id]);
        } else {
            Wishlist::create(['token' => $this->guestTokenOrCreate(), 'product_id' => $product->id]);
        }

        return true;
    }

    public function count(): int
    {
        return $this->query()->count();
    }

    public function products(): Collection
    {
        $productIds = $this->query()->pluck('product_id');

        return Product::with(['images', 'colors', 'categories'])
            ->whereIn('id', $productIds)
            ->get()
            ->sortBy(fn (Product $product) => array_search($product->id, $productIds->all()))
            ->values();
    }

    /**
     * Called on login/register: folds any guest wishlist rows (identified by
     * the cookie token) into the newly authenticated account, skipping
     * products the account already has saved to avoid unique-index clashes.
     */
    public function mergeGuestIntoUser(User $user): void
    {
        $token = request()->cookie(self::COOKIE_NAME);

        if (! $token) {
            return;
        }

        $alreadySaved = Wishlist::where('user_id', $user->id)->pluck('product_id');

        Wishlist::where('token', $token)
            ->whereNotIn('product_id', $alreadySaved)
            ->update(['user_id' => $user->id, 'token' => null]);

        Wishlist::where('token', $token)->delete();

        Cookie::queue(Cookie::forget(self::COOKIE_NAME));
    }

    private function query(): Builder
    {
        if (auth()->check()) {
            return Wishlist::where('user_id', auth()->id());
        }

        $token = request()->cookie(self::COOKIE_NAME);

        return $token ? Wishlist::where('token', $token) : Wishlist::whereKey(0);
    }

    private function guestTokenOrCreate(): string
    {
        $token = request()->cookie(self::COOKIE_NAME);

        if ($token) {
            return $token;
        }

        $token = (string) Str::uuid();

        Cookie::queue(self::COOKIE_NAME, $token, 60 * 24 * 365);

        return $token;
    }
}
