@php
    $showcaseCategories = ($categorySlides ?? collect())->isNotEmpty() ? $categorySlides : ($categories ?? collect());

    // Resolve to plain [category, image] pairs first (categorySlides need the
    // ->continue(!$category) filtering resolved up front) so we can pad the
    // row out to exactly 4 slots below — a row with fewer than 4 real <td>s
    // at width="25%" gets its cells stretched by some renderers to fill the
    // row, which visibly gaps the items apart instead of leaving trailing
    // blank space.
    $categoryTiles = $showcaseCategories->take(4)->map(function ($slide) {
        $category = $slide->category ?? $slide;

        if (! $category) {
            return null;
        }

        return [
            'category' => $category,
            'image' => $slide->image?->mediumUrl() ?? $slide->products?->first()?->cover_image_url ?? null,
        ];
    })->filter()->values();
    while ($categoryTiles->count() < 4) {
        $categoryTiles->push(null);
    }

    $newArrivalTiles = ($newArrivals ?? collect())->take(4)->values();
    while ($newArrivalTiles->count() < 4 && $newArrivalTiles->isNotEmpty()) {
        $newArrivalTiles->push(null);
    }

    $featuredTiles = ($featured ?? collect())->take(4)->values();
    while ($featuredTiles->count() < 4 && $featuredTiles->isNotEmpty()) {
        $featuredTiles->push(null);
    }
@endphp

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-family: Helvetica, Arial, sans-serif;">
    <tr>
        <td style="padding-top:8px; border-top:1px solid #e6d9c5;">
            <p style="font-family: Georgia, serif; font-size:18px; color:#523923; margin:24px 0 16px;">Discover More</p>
        </td>
    </tr>

    @if ($categoryTiles->filter()->isNotEmpty())
        <tr>
            <td style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#a5360d; padding-bottom:8px;">Shop by Category</td>
        </tr>
        <tr>
            <td>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        @foreach ($categoryTiles as $tile)
                            <td width="25%" valign="top" style="padding:4px;">
                                @if ($tile)
                                    <a href="{{ route('shop.index', ['categories' => [$tile['category']->id]]) }}" style="text-decoration:none;">
                                        @if ($tile['image'])
                                            <img src="{{ $tile['image'] }}" alt="{{ $tile['category']->name }}" width="120" height="120" style="width:120px; height:120px; max-width:100%; object-fit:cover; border:1px solid #e6d9c5; display:block;">
                                        @endif
                                        <p style="font-size:12px; color:#1b1b18; text-align:center; margin:6px 0 0;">{{ $tile['category']->name }}</p>
                                    </a>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </table>
            </td>
        </tr>
    @endif

    @if ($newArrivalTiles->filter()->isNotEmpty())
        <tr>
            <td style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#a5360d; padding:20px 0 8px;">New Arrivals</td>
        </tr>
        <tr>
            <td>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        @foreach ($newArrivalTiles as $product)
                            <td width="25%" valign="top" style="padding:4px;">
                                @if ($product)
                                    <a href="{{ route('products.show', $product) }}" style="text-decoration:none;">
                                        <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" width="120" height="120" style="width:120px; height:120px; max-width:100%; object-fit:cover; border:1px solid #e6d9c5; display:block;">
                                        <p style="font-size:12px; color:#1b1b18; margin:6px 0 0;">{{ $product->name }}</p>
                                        <p style="font-size:12px; color:#a5360d; font-weight:bold; margin:2px 0 0;">Rs. {{ number_format((float) ($product->discount_price ?? $product->regular_price)) }}</p>
                                    </a>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </table>
            </td>
        </tr>
    @endif

    @if ($featuredTiles->filter()->isNotEmpty())
        <tr>
            <td style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#a5360d; padding:20px 0 8px;">Artisan Favourites</td>
        </tr>
        <tr>
            <td>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        @foreach ($featuredTiles as $product)
                            <td width="25%" valign="top" style="padding:4px;">
                                @if ($product)
                                    <a href="{{ route('products.show', $product) }}" style="text-decoration:none;">
                                        <img src="{{ $product->cover_image_url }}" alt="{{ $product->name }}" width="120" height="120" style="width:120px; height:120px; max-width:100%; object-fit:cover; border:1px solid #e6d9c5; display:block;">
                                        <p style="font-size:12px; color:#1b1b18; margin:6px 0 0;">{{ $product->name }}</p>
                                        <p style="font-size:12px; color:#a5360d; font-weight:bold; margin:2px 0 0;">Rs. {{ number_format((float) ($product->discount_price ?? $product->regular_price)) }}</p>
                                    </a>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </table>
            </td>
        </tr>
    @endif

    <tr>
        <td align="center" style="padding-top:20px;">
            <a href="{{ route('shop.index') }}" style="display:inline-block; background-color:#a5360d; color:#ffffff; text-decoration:none; font-size:13px; font-weight:bold; padding:12px 28px;">Shop the Collection</a>
        </td>
    </tr>
</table>
