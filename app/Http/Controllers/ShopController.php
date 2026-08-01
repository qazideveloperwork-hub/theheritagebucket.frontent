<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['categories', 'colors'])->where('status', 'publish');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->query('q').'%');
        }

        $selectedCategories = (array) $request->query('categories', []);

        if (! empty($selectedCategories)) {
            $query->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $selectedCategories));
        }

        if ($request->filled('max_price')) {
            $query->where('regular_price', '<=', (float) $request->query('max_price'));
        }

        match ($request->query('sort', 'featured')) {
            'newest' => $query->latest(),
            'price_asc' => $query->orderBy('regular_price', 'asc'),
            'price_desc' => $query->orderBy('regular_price', 'desc'),
            default => $query->orderByDesc('is_featured')->orderBy('name'),
        };

        $products = $query->paginate(9)->withQueryString();

        $categories = Category::where('status', 'publish')->withCount('products')->orderBy('name')->get();

        return view('shop', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategories' => array_map('intval', $selectedCategories),
            'sort' => $request->query('sort', 'featured'),
            'maxPrice' => $request->query('max_price'),
            'search' => $request->query('q'),
        ]);
    }
}
