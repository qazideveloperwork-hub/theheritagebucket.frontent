<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function index()
    {
        $cart = $this->cartService->current();

        return view('cart.index', [
            'cart' => $cart,
            'cartService' => $this->cartService,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variation_id' => ['nullable', 'integer', 'exists:product_variations,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $product = Product::where('status', 'publish')->findOrFail($data['product_id']);

        $variation = null;

        if ($product->isVariable()) {
            abort_unless(! empty($data['variation_id']), 422, 'Please select a size.');
            $variation = $product->variations()->findOrFail($data['variation_id']);
        }

        $maxAllowed = $this->cartService->maxAllowedQuantity($product, $variation);
        abort_if($maxAllowed < 1, 422, 'This item is currently out of stock.');

        $result = $this->cartService->addItem($product, $data['quantity'] ?? 1, $variation);

        $payload = $this->cartPayload($result['cart']);

        if ($result['clamped']) {
            $payload['notice'] = "Only {$result['maxAllowed']} of this item is available — quantity adjusted.";
        }

        return response()->json($payload);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        abort_unless($this->cartService->ownsItem($cartItem), 403);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $result = $this->cartService->updateItemQuantity($cartItem, $data['quantity']);

        $cart = $this->cartService->current();

        $payload = $this->cartPayload($cart);

        if ($result['clamped']) {
            $payload['notice'] = "Only {$result['maxAllowed']} of this item is available — quantity adjusted.";
        }

        return response()->json($payload);
    }

    public function destroy(CartItem $cartItem)
    {
        abort_unless($this->cartService->ownsItem($cartItem), 403);

        $this->cartService->removeItem($cartItem);

        $cart = $this->cartService->current();

        return response()->json($this->cartPayload($cart));
    }

    private function cartPayload(?Cart $cart): array
    {
        if (! $cart) {
            return ['itemCount' => 0, 'subtotal' => 0, 'shippingCost' => 0, 'items' => [], 'stockIssues' => []];
        }

        $cart->load('items.product.images', 'items.product.shippingCost', 'items.variation.size');

        $adminMax = $this->cartService->adminMaxQuantity();

        $items = $cart->items->map(function (CartItem $item) use ($adminMax) {
            $maxAllowed = $this->cartService->maxAllowedQuantity($item->product, $item->variation);
            $realStockLimit = $item->stockLimit();
            $inStock = $item->isInStock();
            // The real stock count is only the binding constraint (worth warning about)
            // when it's lower than the admin's per-order cap — otherwise the cap is a
            // policy limit, not scarcity, and shouldn't be shown as a "low stock" alert.
            $stockIsBindingConstraint = $realStockLimit !== null && $realStockLimit < $adminMax;

            return [
                'id' => $item->id,
                'productName' => $item->product->name,
                'productSlug' => $item->product->slug,
                'image' => $item->product->cover_image_url,
                'sizeName' => $item->variation?->size?->name,
                'unitPrice' => (float) $item->unit_price,
                'quantity' => $item->quantity,
                'lineTotal' => $item->lineTotal(),
                'maxAllowed' => $maxAllowed,
                'realStockLimit' => $stockIsBindingConstraint ? $realStockLimit : null,
                'inStock' => $inStock,
                'hasIssue' => ! $inStock || $item->quantity > $maxAllowed,
            ];
        })->values();

        return [
            'itemCount' => $cart->itemCount(),
            'subtotal' => $cart->subtotal(),
            'shippingCost' => $cart->shippingCost(),
            'items' => $items,
            'stockIssues' => $items->filter(fn (array $item) => $item['hasIssue'])
                ->map(fn (array $item) => [
                    'name' => $item['productName'].($item['sizeName'] ? " ({$item['sizeName']})" : ''),
                    'image' => $item['image'],
                    'message' => ! $item['inStock']
                        ? 'Out of stock'
                        : ($item['realStockLimit'] !== null
                            ? "Only {$item['realStockLimit']} in stock — you have {$item['quantity']} in your basket"
                            : "Maximum {$item['maxAllowed']} allowed per order — you have {$item['quantity']} in your basket"),
                ])->values(),
        ];
    }
}
