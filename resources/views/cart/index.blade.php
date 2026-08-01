@extends('layouts.app')

@section('title', 'Your Basket | Heritage Basket')

@section('content')
    @php
        $items = $cart?->items ?? collect();
        $subtotal = $cart?->subtotal() ?? 0;
        $shippingCost = $cart?->shippingCost() ?? 0;

        $stockIssuesPayload = $items
            ->filter(function ($item) use ($cartService) {
                $maxAllowed = $cartService->maxAllowedQuantity($item->product, $item->variation);

                return ! $item->isInStock() || $item->quantity > $maxAllowed;
            })
            ->map(function ($item) use ($cartService) {
                $maxAllowed = $cartService->maxAllowedQuantity($item->product, $item->variation);

                return [
                    'name' => $item->product->name.($item->variation?->size ? " ({$item->variation->size->name})" : ''),
                    'image' => $item->product->cover_image_url,
                    'message' => ! $item->isInStock()
                        ? 'Out of stock'
                        : "Only {$maxAllowed} available — you have {$item->quantity} in your basket",
                ];
            })->values();

        $maxLeadDays = $items->isNotEmpty()
            ? $items->max(fn ($item) => $item->product->expected_delivery_days ?? 3)
            : null;
        $expectedDeliveryDate = $maxLeadDays !== null ? now()->addDays($maxLeadDays + 2) : null;
    @endphp

    <div
        class="max-w-3xl mx-auto px-5 lg:px-0 py-16 lg:py-section-padding"
        x-data="cartSummary({{ $subtotal }}, {{ $shippingCost }}, {{ Illuminate\Support\Js::from($stockIssuesPayload ?? []) }}, '{{ route('checkout.create') }}')"
        @cart:changed.document="update($event.detail)"
        @cart:quantity-preview.document="previewQuantityChange($event.detail.delta)"
    >
        <h1 class="font-serif text-headline-lg text-on-surface mb-10">Your Basket</h1>

        <div id="cart-items" x-show="!isEmpty">
            @php $adminMax = $cartService->adminMaxQuantity(); @endphp
            @forelse ($items as $item)
                @php
                    $maxAllowed = $cartService->maxAllowedQuantity($item->product, $item->variation);
                    $realStockLimit = $item->stockLimit();
                    $realStockLimit = ($realStockLimit !== null && $realStockLimit < $adminMax) ? $realStockLimit : null;
                @endphp
                <div
                    x-data="cartRow({{ $item->id }}, {{ $item->quantity }}, {{ $item->unit_price }}, {{ $maxAllowed }}, {{ $item->isInStock() ? 'true' : 'false' }}, {{ Illuminate\Support\Js::from($item->product->name) }}, {{ Illuminate\Support\Js::from($item->product->cover_image_url) }}, {{ Illuminate\Support\Js::from($realStockLimit) }})"
                    class="flex items-center gap-6 py-6 border-b border-outline-variant"
                >
                    <div class="w-24 h-24 border border-outline-variant overflow-hidden flex-shrink-0 bg-surface-container-low">
                        <img src="{{ $item->product->cover_image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <a href="{{ route('products.show', $item->product) }}" class="font-serif text-headline-sm text-on-surface hover:text-primary">{{ $item->product->name }}</a>
                        @if ($item->variation?->size)
                            <p class="text-label-md text-on-surface-variant">Size: {{ $item->variation->size->name }}</p>
                        @endif
                        <p class="text-body-md text-on-surface-variant">Rs. {{ number_format((float) $item->unit_price) }} each</p>
                    </div>
                    <div class="flex flex-col items-center gap-1">
                        <div class="flex items-center border border-outline-variant">
                            <button type="button" class="px-3 py-2 hover:bg-surface-container-low disabled:opacity-40 disabled:cursor-not-allowed" @click="decrement" :disabled="quantity <= 1">
                                <span class="material-symbols-outlined text-[18px]">remove</span>
                            </button>
                            <span class="w-10 text-center" x-text="quantity"></span>
                            <button type="button" class="px-3 py-2 hover:bg-surface-container-low disabled:opacity-40 disabled:cursor-not-allowed" @click="increment" :disabled="atMax">
                                <span class="material-symbols-outlined text-[18px]">add</span>
                            </button>
                        </div>
                        <p x-show="!inStock" class="text-label-md text-error font-semibold text-center">Out of stock</p>
                        <p x-show="inStock && lowStock" class="text-label-md text-error text-center">Only <span x-text="realStockLimit"></span> left</p>
                        <p x-show="notice" x-text="notice" x-cloak class="text-label-md text-error text-center"></p>
                    </div>
                    <p class="w-28 text-right font-bold text-primary">Rs. <span x-text="lineTotal"></span></p>
                    <button type="button" class="text-on-surface-variant hover:text-error" @click="remove" :disabled="busy">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </div>
            @empty
            @endforelse
        </div>

        @if ($expectedDeliveryDate)
            <div class="flex items-center gap-2 py-6 text-body-md text-on-surface-variant">
                <span class="material-symbols-outlined text-primary">local_shipping</span>
                You will receive this order by <span class="font-semibold text-on-surface">{{ $expectedDeliveryDate->format('d M Y') }}</span>
            </div>
        @endif

        <div x-show="isEmpty" x-cloak class="text-center py-20">
            <span class="material-symbols-outlined text-6xl text-outline-variant mb-4 block">shopping_basket</span>
            <p class="text-body-lg text-on-surface-variant mb-6">Your basket is empty.</p>
            <a href="{{ route('shop.index') }}" class="inline-block bg-primary text-white px-8 py-3 text-button hover:bg-primary-container transition-all">Continue Shopping</a>
        </div>

        <div x-show="!isEmpty" class="mt-10 flex flex-col items-end gap-2">
            <p class="text-body-lg text-on-surface-variant">Subtotal: <span class="font-bold text-on-surface" x-text="'Rs. ' + Math.round(subtotal).toLocaleString()"></span></p>
            <p class="text-body-lg text-on-surface-variant">Shipping cost: <span class="font-bold text-on-surface" x-text="'Rs. ' + Math.round(shippingFee).toLocaleString()"></span></p>
            <p class="text-headline-sm font-serif text-on-surface">Total: <span class="font-bold text-primary" x-text="'Rs. ' + Math.round(total).toLocaleString()"></span></p>
            <button type="button" @click="proceedToCheckout()" class="inline-block bg-primary text-white px-10 py-4 text-button hover:bg-primary-container transition-all mt-4">Proceed to Checkout</button>
        </div>
    </div>

    {{-- Shared modal for cart stock/quantity issues (Alpine.store('errorModal')) --}}
    <div
        x-data
        x-show="$store.errorModal.open"
        x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 px-5"
        @click.self="$store.errorModal.close()"
        @keydown.escape.window="$store.errorModal.close()"
    >
        <div class="bg-surface w-full max-w-md p-8 text-center" @click.stop>
            <template x-if="$store.errorModal.image && $store.errorModal.items.length === 0">
                <img :src="$store.errorModal.image" :alt="$store.errorModal.title" class="w-20 h-20 object-cover mx-auto mb-4 border border-outline-variant">
            </template>

            <h3 class="font-serif text-headline-sm text-on-surface mb-2" x-show="$store.errorModal.title" x-text="$store.errorModal.title"></h3>
            <p class="text-body-md text-on-surface-variant mb-4" x-show="$store.errorModal.message" x-text="$store.errorModal.message"></p>

            <template x-if="$store.errorModal.items.length > 0">
                <div class="text-left space-y-3 mb-4 max-h-72 overflow-y-auto">
                    <template x-for="(item, index) in $store.errorModal.items" :key="index">
                        <div class="flex items-center gap-3 border border-outline-variant p-3">
                            <img :src="item.image" :alt="item.name" class="w-12 h-12 object-cover flex-shrink-0 bg-surface-container-low">
                            <div>
                                <p class="text-body-md text-on-surface font-semibold" x-text="item.name"></p>
                                <p class="text-label-md text-error" x-text="item.message"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <button type="button" @click="$store.errorModal.close()" class="inline-block bg-primary text-white px-8 py-3 text-button hover:bg-primary-container transition-all">Close</button>
        </div>
    </div>

    <script>
        document.addEventListener('cart:changed', (event) => {
            if (event.detail.itemCount === 0) {
                document.getElementById('cart-items').innerHTML = '';
            }
        });
    </script>
@endsection
