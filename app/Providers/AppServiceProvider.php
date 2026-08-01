<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class);
        $this->app->singleton(WishlistService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers()->symbols());

        View::composer('layouts.app', function ($view) {
            $cart = $this->app->make(CartService::class)->current();

            $view->with('cartItemCount', $cart?->itemCount() ?? 0);
            $view->with('wishlistCount', $this->app->make(WishlistService::class)->count());
        });

        View::composer(['products.show', 'checkout.create', 'checkout.confirmation'], function ($view) {
            $view->with('siteSettings', Setting::first());
        });
    }
}
