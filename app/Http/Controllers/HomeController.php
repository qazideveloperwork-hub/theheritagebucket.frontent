<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', static::homeData());
    }

    public static function homeData(): array
    {
        $hero = Slider::ofType(Slider::TYPE_HOME_BANNER)->first();

        $categorySlides = Slider::ofType(Slider::TYPE_HOME_CATEGORY)
            ->with(['category', 'image'])
            ->whereHas('category', fn ($query) => $query->where('status', 'publish'))
            ->orderBy('sort_order')
            ->get();

        if ($categorySlides->isEmpty()) {
            $categories = Category::where('status', 'publish')
                ->withCount('products')
                ->with(['products' => fn ($query) => $query->where('status', 'publish')->latest()->limit(1)])
                ->having('products_count', '>', 0)
                ->orderBy('name')
                ->take(4)
                ->get();
        }

        $newArrivals = Slider::ofType(Slider::TYPE_HOME_NEW_ARRIVAL)
            ->whereHas('product', fn ($query) => $query->where('status', 'publish'))
            ->with(['product.categories', 'product.colors'])
            ->orderBy('sort_order')
            ->get()
            ->pluck('product');

        if ($newArrivals->isEmpty()) {
            $newArrivals = Product::with(['categories', 'colors'])
                ->where('status', 'publish')
                ->where('is_new_arrival', true)
                ->latest()
                ->take(4)
                ->get();
        }

        $artisanStory = Slider::ofType(Slider::TYPE_ARTISAN_STORY)->first();

        $featured = Slider::ofType(Slider::TYPE_FAVOURITE_PRODUCT)
            ->whereHas('product', fn ($query) => $query->where('status', 'publish'))
            ->with(['product.categories', 'product.colors'])
            ->orderBy('sort_order')
            ->get()
            ->pluck('product');

        if ($featured->isEmpty()) {
            $featured = Product::with(['categories', 'colors'])
                ->where('status', 'publish')
                ->where('is_featured', true)
                ->inRandomOrder()
                ->take(4)
                ->get();
        }

        return [
            'hero' => $hero,
            'categorySlides' => $categorySlides,
            'categories' => $categories ?? collect(),
            'newArrivals' => $newArrivals,
            'artisanStory' => $artisanStory,
            'featured' => $featured,
        ];
    }
}
