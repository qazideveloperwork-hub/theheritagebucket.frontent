<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckoutRequest;
use App\Mail\OrderConfirmationEmail;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function create()
    {
        $cart = $this->cartService->current();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your basket is empty — add something beautiful first.');
        }

        if ($cart->items->contains(fn ($item) => ! $item->isInStock())) {
            return redirect()->route('cart.index')->with('status', 'One or more items in your basket are out of stock. Please remove them before checking out.');
        }

        return view('checkout.create', [
            'cart' => $cart,
            'shippingFee' => $cart->shippingCost(),
        ]);
    }

    public function store(StoreCheckoutRequest $request): RedirectResponse
    {
        $cart = $this->cartService->current();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your basket is empty — add something beautiful first.');
        }

        if ($cart->items->contains(fn ($item) => ! $item->isInStock())) {
            return redirect()->route('cart.index')->with('status', 'One or more items in your basket are out of stock. Please remove them before checking out.');
        }

        $data = $request->validated();

        $order = DB::transaction(function () use ($data, $cart) {
            $subtotal = $cart->subtotal();
            $shippingFee = $cart->shippingCost();

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'HB-'.strtoupper(Str::random(8)),
                'customer_name' => $data['customer_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'shipping_address' => $data['shipping_address'],
                'address_line_2' => $data['address_line_2'],
                'city' => $data['city'],
                'province' => $data['province'],
                'postal_code' => $data['postal_code'],
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $subtotal + $shippingFee,
                'payment_method' => $data['payment_method'],
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_variation_id' => $item->product_variation_id,
                    'product_name' => $item->product->name,
                    'variation_label' => $item->variation?->size?->name,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'line_total' => $item->lineTotal(),
                ]);

                $this->decrementStock($item->variation ?: $item->product, $item->quantity);
            }

            $this->cartService->clear($cart);

            return $order;
        });

        auth()->user()?->update([
            'phone' => $data['phone'],
            'street' => $data['shipping_address'],
            'address_line_2' => $data['address_line_2'],
            'city' => $data['city'],
            'province' => $data['province'],
            'postal_code' => $data['postal_code'],
        ]);

        Mail::to($order->email)->send(new OrderConfirmationEmail($order));

        return redirect()->route('checkout.confirmation', $order->order_number);
    }

    public function confirmation(Order $order)
    {
        return view('checkout.confirmation', [
            'order' => $order->load('items'),
        ]);
    }

    /**
     * Mirrors InventoryController::update()'s stock_status sync in the admin
     * app: only auto-flip stock_status when the admin hasn't manually pinned it.
     */
    private function decrementStock(Product|ProductVariation $owner, int $quantity): void
    {
        $inventory = $owner->inventory;

        if (! $inventory) {
            return;
        }

        $newQuantity = max(0, $inventory->quantity - $quantity);
        $inventory->update(['quantity' => $newQuantity]);

        if (! $owner->stock_status_manual) {
            $owner->update(['stock_status' => $newQuantity > 0 ? 'in_stock' : 'out_of_stock']);
        }
    }
}
