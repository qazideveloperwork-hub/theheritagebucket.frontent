@extends('layouts.app')

@section('title', $product->name.' | Heritage Basket')
@section('description', $product->description?->short_description ?? $product->name)

@section('content')
    @php
        $defaultGalleryImages = collect([$product->cover_image_url])
            ->merge($product->galleryMedia->map(fn ($media) => $media->largeUrl()))
            ->merge($product->images->pluck('url'))
            ->filter()
            ->unique()
            ->values();

        $variationsPayload = $product->isVariable()
            ? $product->variations->map(fn ($variation) => [
                'id' => $variation->id,
                'sizeName' => $variation->size?->name,
                'regularPrice' => (float) $variation->regular_price,
                'currentPrice' => $variation->currentPrice(),
                'price' => $variation->effectivePrice(),
                'inStock' => $variation->inStock(),
                'infos' => $variation->info->pluck('title')->values(),
                'weight' => $variation->weight ? (float) $variation->weight : null,
                'maxQuantity' => $cartService->maxAllowedQuantity($product, $variation),
            ])->values()
            : collect();
    @endphp
    <div class="max-w-container-max mx-auto px-5 lg:px-margin-desktop py-16 lg:py-section-padding">
        <section
            class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-gutter items-start mb-24"
            x-data="{
                activeImages: {{ Illuminate\Support\Js::from($defaultGalleryImages) }},
                current: 0,
                isVariable: {{ $product->isVariable() ? 'true' : 'false' }},
                variations: {{ Illuminate\Support\Js::from($variationsPayload) }},
                selectedVariationId: null,
                simpleMaxQuantity: {{ $maxQuantity }},
                selectedQty: 1,
                get selectedVariation() {
                    return this.selectedVariationId ? this.variations.find(v => v.id === this.selectedVariationId) : null;
                },
                get displayVariation() {
                    if (this.selectedVariation) return this.selectedVariation;
                    if (this.variations.length) {
                        return this.variations.reduce((min, v) => v.price < min.price ? v : min, this.variations[0]);
                    }
                    return null;
                },
                get maxQuantity() {
                    if (this.isVariable) {
                        return this.selectedVariation ? this.selectedVariation.maxQuantity : 0;
                    }
                    return this.simpleMaxQuantity;
                },
                init() {
                    this.$watch('selectedVariationId', () => {
                        if (this.selectedQty > this.maxQuantity) {
                            this.selectedQty = Math.max(1, this.maxQuantity);
                        }
                    });
                },
            }"
        >
            {{-- Gallery --}}
            <div class="space-y-4">
                <div
                    class="relative aspect-[4/5] overflow-hidden border border-outline-variant bg-surface-container-low select-none cursor-grab active:cursor-grabbing"
                    style="touch-action: pan-y;"
                    x-data="{ dragging: false, startX: 0 }"
                    @pointerdown="dragging = true; startX = $event.clientX"
                    @pointermove="if (dragging) $event.preventDefault()"
                    @pointerup="if (dragging) {
                        const delta = $event.clientX - startX;
                        if (activeImages.length > 1) {
                            if (delta < -50) current = (current + 1) % activeImages.length;
                            else if (delta > 50) current = (current - 1 + activeImages.length) % activeImages.length;
                        }
                        dragging = false;
                    }"
                    @pointerleave="dragging = false"
                >
                    <img :src="activeImages[current]" alt="{{ $product->name }}" draggable="false" class="w-full h-full object-cover pointer-events-none">
                    <x-product-badge :product="$product" class="absolute top-4 left-4" />
                    @if ($product->badge)
                        <span class="absolute top-4 right-4 bg-tertiary text-on-tertiary px-3 py-1 text-[12px] font-medium rounded-full uppercase tracking-wider">
                            {{ $product->badge }}
                        </span>
                    @endif
                    <button
                        type="button"
                        x-data="wishlistButton({{ $product->id }}, {{ $inWishlist ? 'true' : 'false' }})"
                        @click="toggle()"
                        :disabled="loading"
                        :aria-pressed="inWishlist"
                        class="absolute bottom-3 right-3 p-2 transition disabled:opacity-60"
                        aria-label="Save {{ $product->name }} to wishlist"
                    >
                        <span
                            class="material-symbols-outlined wishlist-icon text-[24px]"
                            :class="inWishlist ? 'text-primary' : 'text-white'"
                            :style="{ '--icon-fill': inWishlist ? 1 : 0 }"
                        >favorite</span>
                    </button>
                </div>
                <div class="flex items-center gap-2" x-show="activeImages.length > 1">
                    <button type="button"
                            x-show="activeImages.length > 4"
                            @click="current = (current - 1 + activeImages.length) % activeImages.length"
                            class="shrink-0 w-8 h-8 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors"
                            aria-label="Scroll thumbnails left">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <div x-ref="thumbStrip" x-effect="document.getElementById('thumb-' + current)?.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' })" class="flex-1 flex gap-4 overflow-x-auto scroll-smooth">
                        <template x-for="(image, index) in activeImages" :key="index">
                            <div :id="'thumb-' + index"
                                 class="w-20 h-20 shrink-0 aspect-square border overflow-hidden cursor-pointer"
                                 :class="index === current ? 'border-primary' : 'border-outline-variant'"
                                 @click="current = index">
                                <img :src="image" alt="{{ $product->name }} detail" loading="lazy" class="w-full h-full object-cover opacity-80 hover:opacity-100 transition-opacity">
                            </div>
                        </template>
                    </div>
                    <button type="button"
                            x-show="activeImages.length > 4"
                            @click="current = (current + 1) % activeImages.length"
                            class="shrink-0 w-8 h-8 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors"
                            aria-label="Scroll thumbnails right">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
            </div>

            {{-- Details & Purchase --}}
            <div class="md:pl-8 lg:pl-12 flex flex-col h-full">
                @php $primaryCategory = $product->categories->first(); @endphp
                <nav class="flex items-center gap-2 text-on-surface-variant text-label-md mb-4">
                    <a href="{{ route('shop.index') }}" class="hover:text-primary">Collections</a>
                    @if ($primaryCategory)
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                        <a href="{{ route('shop.index', ['categories' => [$primaryCategory->id]]) }}" class="hover:text-primary">{{ $primaryCategory->name }}</a>
                    @endif
                </nav>

                @if ($product->badge)
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 bg-tertiary-container text-on-tertiary-fixed-variant rounded-full text-[10px] font-medium tracking-widest uppercase">{{ $product->badge }}</span>
                    </div>
                @endif

                <h1 class="font-serif text-headline-lg text-on-surface mb-4">{{ $product->name }}</h1>

                @if ($product->isVariable())
                    <template x-if="displayVariation">
                        <div class="flex items-baseline gap-3 mb-8">
                            <span class="font-serif text-headline-sm text-primary" x-text="'Rs. ' + Math.round(displayVariation.currentPrice || displayVariation.regularPrice).toLocaleString()"></span>
                            <span x-show="displayVariation.currentPrice" class="text-body-md text-on-surface-variant line-through" x-text="'Rs. ' + Math.round(displayVariation.regularPrice).toLocaleString()"></span>
                        </div>
                    </template>
                    <template x-if="!displayVariation">
                        <p class="font-serif text-headline-sm text-primary mb-8">Select a size to see the price</p>
                    </template>
                @else
                    <div class="flex items-baseline gap-3 mb-8">
                        @if ($product->currentPrice())
                            <span class="font-serif text-headline-sm text-primary">Rs. {{ number_format($product->currentPrice()) }}</span>
                            <span class="text-body-md text-on-surface-variant line-through">Rs. {{ number_format((float) $product->regular_price) }}</span>
                        @else
                            <span class="font-serif text-headline-sm text-primary">Rs. {{ number_format($product->effectivePrice()) }}</span>
                        @endif
                    </div>
                @endif

                @if (! $product->isVariable() && $product->stock_status === 'out_of_stock')
                    <p class="text-body-md text-error font-semibold mb-8">This product is currently out of stock.</p>
                @endif

                @if ($product->isVariable())
                    <template x-if="displayVariation && displayVariation.weight">
                        <p class="text-body-md text-on-surface-variant mb-4">Weight: <span x-text="displayVariation.weight"></span> kg</p>
                    </template>
                @elseif ($product->weight)
                    <p class="text-body-md text-on-surface-variant mb-4">Weight: {{ rtrim(rtrim(number_format((float) $product->weight, 2), '0'), '.') }} kg</p>
                @endif

                @if ($product->isVariable())
                    <div class="mb-8">
                        <p class="text-label-md text-on-surface-variant uppercase tracking-wider mb-3">Size</p>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($product->variations as $variation)
                                <button type="button"
                                        @click="selectedVariationId = {{ $variation->id }}"
                                        :class="selectedVariationId === {{ $variation->id }} ? 'border-primary bg-primary-container text-on-primary-container' : 'border-outline-variant text-on-surface hover:border-primary'"
                                        class="px-5 py-2.5 border text-body-md transition-colors {{ ! $variation->inStock() ? 'text-on-surface-variant line-through' : '' }}">
                                    {{ $variation->size?->name }}
                                </button>
                            @endforeach
                        </div>
                        <p x-show="!selectedVariationId" class="mt-2 text-label-md text-on-surface-variant">Please select a size.</p>
                        <p x-show="selectedVariation && !selectedVariation.inStock" class="mt-2 text-label-md text-error">Out of stock.</p>
                    </div>
                    <template x-if="selectedVariation && selectedVariation.infos.length">
                        <div class="mb-8 space-y-2">
                            <template x-for="info in selectedVariation.infos" :key="info">
                                <p class="flex items-center gap-2 text-body-md text-on-surface-variant">
                                    <span class="material-symbols-outlined text-primary text-[20px]">info</span>
                                    <span x-text="info"></span>
                                </p>
                            </template>
                        </div>
                    </template>
                @elseif ($product->infos->isNotEmpty())
                    <div class="mb-8 space-y-2">
                        @foreach ($product->infos as $info)
                            <p class="flex items-center gap-2 text-body-md text-on-surface-variant">
                                <span class="material-symbols-outlined text-primary text-[20px]">info</span>
                                {{ $info->title }}
                            </p>
                        @endforeach
                    </div>
                @endif

                @if ($product->colors->isNotEmpty())
                    @php
                        $colorSwatches = $product->colors->map(function ($color) use ($product) {
                            $images = $product->colorMedia
                                ->where('color_id', $color->id)
                                ->map(fn ($pcm) => $pcm->media->largeUrl())
                                ->values();

                            return ['color' => $color, 'images' => $images];
                        })->filter(fn ($entry) => $entry['images']->isNotEmpty());
                    @endphp
                    @if ($colorSwatches->isNotEmpty())
                        <div class="mb-8">
                            <p class="text-label-md text-on-surface-variant uppercase tracking-wider mb-3">Colors</p>
                            <div class="flex flex-wrap gap-3">
                                @foreach ($colorSwatches as $entry)
                                    <button type="button"
                                            @click="activeImages = {{ Illuminate\Support\Js::from($entry['images']) }}; current = 0"
                                            class="w-14 h-14 border border-outline-variant overflow-hidden hover:border-primary transition-colors"
                                            title="{{ $entry['color']->name }}">
                                        <img src="{{ $entry['images']->first() }}" alt="{{ $entry['color']->name }}" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                <div x-data="addToCartButton({{ $product->id }})" class="space-y-4">
                    <div>
                        <label class="text-label-md text-on-surface-variant block mb-2" for="quantity">Quantity</label>
                        <div class="flex items-center border border-outline-variant w-fit">
                            <button type="button" class="px-4 py-2 hover:bg-surface-container-low transition-colors" @click="selectedQty = Math.max(1, selectedQty - 1)">
                                <span class="material-symbols-outlined">remove</span>
                            </button>
                            <input id="quantity" type="number" min="1" x-model.number="selectedQty" class="w-12 text-center bg-transparent border-none focus:ring-0">
                            <button type="button" class="px-4 py-2 hover:bg-surface-container-low transition-colors disabled:opacity-40 disabled:cursor-not-allowed" :disabled="selectedQty >= maxQuantity" @click="selectedQty = Math.min(maxQuantity, selectedQty + 1)">
                                <span class="material-symbols-outlined">add</span>
                            </button>

                            <button
                                type="button"
                                @click="add(selectedQty, selectedVariationId)"
                                :disabled="loading || maxQuantity < 1 || (isVariable && (!selectedVariationId || !selectedVariation.inStock))"
                                class="ml-4 flex-1 min-w-[200px] bg-primary text-on-primary py-3 px-6 text-button transition-transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-60"
                            >
                                <span x-show="!added" x-text="loading ? 'Adding…' : 'Add to Basket'"></span>
                                <span x-show="added" x-cloak>Added ✓</span>
                            </button>
                        </div>
                        <p x-show="selectedQty >= maxQuantity && maxQuantity > 0" class="mt-2 text-label-md text-error">Only <span x-text="maxQuantity"></span> available.</p>
                    </div>
                </div>

                @php
                    $deliveryFrom = $product->expected_delivery_days ?? 3;
                    $deliveryTo = $deliveryFrom + 2;
                @endphp
                <div class="mt-8 flex flex-wrap gap-6 text-on-surface-variant text-label-md">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">local_shipping</span>
                        Standard Shipping ({{ $deliveryFrom }}-{{ $deliveryTo }} days)
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">verified</span>
                        Authenticity Guaranteed
                    </div>
                    @if ($siteSettings?->whatsapp_number)
                        @php
                            $waMessage = "Hi, I have a question about {$product->name} - ".route('products.show', $product);
                        @endphp
                        <a href="https://wa.me/{{ $siteSettings->whatsapp_number }}?text={{ urlencode($waMessage) }}" target="_blank" rel="noopener" class="flex items-center gap-2 hover:text-primary transition-colors">
                            <x-whatsapp-icon class="w-8 h-8" />
                            Ask on WhatsApp
                        </a>
                    @endif
                </div>

                @if ($product->description?->short_description)
                    <p class="text-body-md text-on-surface-variant leading-relaxed mt-8">{{ $product->description->short_description }}</p>
                @endif

                @if ($product->description?->content)
                    <div class="border-y border-outline-variant py-8 mt-8 space-y-4" x-data="{ expanded: false }">
                        <h3 class="text-label-md text-on-surface-variant uppercase tracking-wider">The Story</h3>
                        <div class="text-body-md text-on-surface-variant leading-relaxed" :class="expanded ? '' : 'line-clamp-5'">{!! $product->description->content !!}</div>
                        <button type="button" @click="expanded = !expanded" class="text-primary font-semibold hover:underline text-body-md" x-text="expanded ? 'Read less' : 'Read more'"></button>
                    </div>
                @endif
            </div>
        </section>

        @if ($product->artisan_name || $product->dimensions || $product->material || $product->care_instructions)
            <div class="relative py-12 flex items-center justify-center">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-outline-variant"></div>
                </div>
                <div class="relative bg-background px-6">
                    <span class="material-symbols-outlined text-primary">spa</span>
                </div>
            </div>

            <section class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16">
                @if ($product->artisan_name)
                    <div class="space-y-6">
                        <h2 class="font-serif text-headline-md text-on-surface">Artisan Details</h2>
                        <div class="flex items-start gap-6">
                            @if ($product->artisan_image_url)
                                <div class="w-24 h-24 rounded-full overflow-hidden flex-shrink-0 border-2 border-primary-fixed">
                                    <img src="{{ $product->artisan_image_url }}" alt="{{ $product->artisan_name }}" loading="lazy" class="w-full h-full object-cover">
                                </div>
                            @endif
                            <div class="space-y-2">
                                <p class="font-serif text-headline-sm">{{ $product->artisan_name }}</p>
                                @if ($product->artisan_bio)
                                    <p class="text-body-md text-on-surface-variant leading-relaxed">{{ $product->artisan_bio }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if ($product->dimensions || $product->material || $product->care_instructions)
                    <div class="space-y-6">
                        <h2 class="font-serif text-headline-md text-on-surface">Care &amp; Specifications</h2>
                        <div class="grid grid-cols-2 gap-6">
                            @if ($product->dimensions)
                                <div class="space-y-2">
                                    <p class="text-label-md text-on-surface uppercase font-bold">Dimensions</p>
                                    <p class="text-body-md text-on-surface-variant">{{ $product->dimensions }}</p>
                                </div>
                            @endif
                            @if ($product->material)
                                <div class="space-y-2">
                                    <p class="text-label-md text-on-surface uppercase font-bold">Material</p>
                                    <p class="text-body-md text-on-surface-variant">{{ $product->material }}</p>
                                </div>
                            @endif
                        </div>
                        @if (!empty($product->care_instructions))
                            <div class="bg-surface-container-low p-6 border border-outline-variant">
                                <h4 class="text-label-md text-on-surface-variant uppercase mb-4 font-bold flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">info</span>
                                    Care Instructions
                                </h4>
                                <ul class="space-y-3 text-body-md text-on-surface-variant list-disc pl-5">
                                    @foreach ($product->care_instructions as $instruction)
                                        <li>{{ $instruction }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </section>
        @endif

        @if ($related->isNotEmpty())
            <section class="mt-16 lg:mt-section-padding">
                <div class="flex items-end justify-between mb-8">
                    @if ($relatedIsCurated)
                        <h2 class="font-serif text-headline-md">You may also like</h2>
                    @else
                        <h2 class="font-serif text-headline-md">More from {{ $primaryCategory?->name }}</h2>
                        <a href="{{ route('shop.index', ['categories' => [$primaryCategory?->id]]) }}" class="text-primary border-b border-primary pb-1 hover:text-secondary hover:border-secondary transition-all">View Collection</a>
                    @endif
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
