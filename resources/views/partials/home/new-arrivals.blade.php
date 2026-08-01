{{-- New Arrivals --}}
@if ($newArrivals->isNotEmpty())
    <section class="py-16 lg:py-section-padding px-5 lg:px-margin-desktop max-w-container-max mx-auto">
        <div class="text-center mb-16">
            <h2 class="font-serif text-headline-md text-on-surface mb-4">New Arrivals</h2>
            <p class="text-body-md text-on-surface-variant max-w-2xl mx-auto">Discover the latest additions to our chest, curated for the discerning eye that values timeless quality.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-8">
            @foreach ($newArrivals as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>
@endif
