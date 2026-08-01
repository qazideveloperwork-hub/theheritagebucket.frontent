<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(private readonly WishlistService $wishlistService)
    {
    }

    public function index()
    {
        return view('wishlist.index', [
            'products' => $this->wishlistService->products(),
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $product = Product::where('status', 'publish')->findOrFail($data['product_id']);

        $inWishlist = $this->wishlistService->toggle($product);

        return response()->json([
            'inWishlist' => $inWishlist,
            'count' => $this->wishlistService->count(),
        ]);
    }
}
