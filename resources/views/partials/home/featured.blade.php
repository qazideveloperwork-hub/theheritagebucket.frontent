{{-- Featured products --}}
@if ($featured->isNotEmpty())
    <section class="py-16 lg:py-section-padding px-5 lg:px-margin-desktop max-w-container-max mx-auto">
        <div class="text-center mb-16">
            <h2 class="font-serif text-headline-md text-on-surface mb-4">Artisan Favourites</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-8">
            @foreach ($featured as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>
@endif
