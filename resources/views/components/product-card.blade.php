@props(['product'])

<div class="group">
    <a href="{{ route('products.show', $product) }}" class="block relative aspect-[4/5] border border-outline-variant overflow-hidden mb-4 bg-surface-container-low">
        <img
            src="{{ $product->cover_image_url }}"
            alt="{{ $product->name }}"
            loading="lazy"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
        >
        <x-product-badge :product="$product" class="absolute top-4 left-4" />
        @if ($product->badge)
            <span class="absolute top-4 right-4 bg-tertiary text-on-tertiary px-3 py-1 text-[12px] font-medium rounded-full uppercase tracking-wider">
                {{ $product->badge }}
            </span>
        @endif
    </a>
    <a href="{{ route('products.show', $product) }}" class="block">
        <h3 class="font-serif text-[20px] text-on-surface mb-1 group-hover:text-primary transition-colors">{{ $product->name }}</h3>
    </a>
    <p class="text-label-md text-on-surface-variant mb-2">{{ $product->categories->first()?->name }}</p>
    @if ($product->colors->isNotEmpty())
        <div class="flex items-center gap-1.5 mb-3">
            @foreach ($product->colors as $color)
                @if ($color->hex_code)
                    <span class="w-3 h-3 rounded-full border border-outline-variant" style="background-color: {{ $color->hex_code }}" title="{{ $color->name }}"></span>
                @endif
            @endforeach
        </div>
    @endif
    <div class="flex justify-between items-center">
        <span class="text-body-md text-primary font-bold">Rs. {{ number_format((float) ($product->discount_price ?? $product->regular_price)) }}</span>
    </div>
</div>
