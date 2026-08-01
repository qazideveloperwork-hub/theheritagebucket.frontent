<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use App\Services\WishlistService;

class ProductController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly WishlistService $wishlistService,
    ) {
    }

    public function show(Product $product)
    {
        abort_if($product->status !== 'publish', 404);

        $product->load(['categories', 'images', 'galleryMedia', 'colors', 'colorMedia.media', 'infos', 'inventory', 'variations.size', 'variations.info', 'variations.inventory']);
        $product->load(['relatedProducts' => fn ($query) => $query->with('categories', 'colors')->where('status', 'publish')]);

        if ($product->isVariable()) {
            $product->setRelation(
                'variations',
                $product->variations->sortBy(fn ($variation) => $variation->size?->sort_order ?? 0)->values()
            );
        }

        $related = $product->relatedProducts->take(4);
        $relatedIsCurated = $related->isNotEmpty();

        if (! $relatedIsCurated) {
            $categoryIds = $product->categories->pluck('id');

            $related = Product::with(['categories', 'colors'])
                ->where('status', 'publish')
                ->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $categoryIds))
                ->where('id', '!=', $product->id)
                ->take(4)
                ->get();
        }

        return view('products.show', [
            'product' => $product,
            'related' => $related,
            'relatedIsCurated' => $relatedIsCurated,
            'maxQuantity' => $this->cartService->maxAllowedQuantity($product),
            'cartService' => $this->cartService,
            'inWishlist' => $this->wishlistService->has($product),
        ]);
    }
}
