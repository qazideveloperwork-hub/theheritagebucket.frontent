@extends('layouts.app')

@section('title', 'Artisanal Collections | Heritage Basket')

@section('content')
    <div class="max-w-container-max mx-auto min-h-screen">
        <section class="relative py-16 px-5 lg:px-margin-desktop overflow-hidden">
            <nav class="flex items-center gap-2 text-label-md text-on-surface-variant mb-4">
                <a href="{{ route('home') }}" class="hover:text-primary">Home</a>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <span class="text-primary font-semibold">Collections</span>
            </nav>
            <h1 class="font-serif text-headline-lg text-on-surface mb-4">Artisanal Collections</h1>
            <p class="max-w-2xl text-body-lg text-on-surface-variant">Discover a curated selection of handcrafted masterpieces. Each piece tells a story of tradition, preserved by the skilled hands of our master artisans.</p>
            @if ($search)
                <p class="mt-4 text-body-md text-on-surface-variant">
                    Showing results for "<span class="text-on-surface font-semibold">{{ $search }}</span>"
                    <a href="{{ route('shop.index') }}" class="text-primary hover:underline ml-2">Clear</a>
                </p>
            @endif
        </section>

        <div class="flex flex-col lg:flex-row px-5 lg:px-margin-desktop gap-8 py-10 border-t border-outline-variant">
            {{-- Filters --}}
            <aside class="w-full lg:w-64 flex-shrink-0">
                <form method="GET" action="{{ route('shop.index') }}" class="space-y-8">
                    <input type="hidden" name="q" value="{{ $search }}">
                    <div>
                        <h3 class="text-label-md uppercase tracking-widest text-on-surface-variant mb-4">Category</h3>
                        <div class="space-y-3">
                            @foreach ($categories as $category)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input
                                        type="checkbox"
                                        name="categories[]"
                                        value="{{ $category->id }}"
                                        {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}
                                        onchange="this.form.submit()"
                                        class="w-4 h-4 rounded border-outline text-primary focus:ring-primary"
                                    >
                                    <span class="text-body-md text-on-surface group-hover:text-primary transition-colors">{{ $category->name }} ({{ $category->products_count }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t border-outline-variant pt-8">
                        <h3 class="text-label-md uppercase tracking-widest text-on-surface-variant mb-4">Max Price</h3>
                        <div class="px-1">
                            <input
                                type="range"
                                name="max_price"
                                min="2000"
                                max="35000"
                                step="500"
                                value="{{ $maxPrice ?? 35000 }}"
                                onchange="this.form.submit()"
                                class="w-full h-1 bg-surface-container rounded-lg appearance-none cursor-pointer accent-primary"
                            >
                            <div class="flex justify-between mt-2 text-label-md text-on-surface-variant">
                                <span>Rs. 2,000</span>
                                <span>Rs. {{ number_format((int) ($maxPrice ?? 35000)) }}+</span>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="sort" value="{{ $sort }}">

                    <div class="pt-4">
                        <div class="p-6 bg-surface-container rounded-lg border border-outline-variant">
                            <span class="material-symbols-outlined text-primary mb-2 block">verified</span>
                            <h4 class="font-serif text-headline-sm text-on-surface mb-2">Artisan Support</h4>
                            <p class="text-label-md text-on-surface-variant">Every purchase goes directly back to our artisan workshops.</p>
                        </div>
                    </div>
                </form>
            </aside>

            {{-- Product Grid --}}
            <div class="flex-1">
                <div class="flex justify-between items-center mb-8 pb-4 border-b border-outline-variant">
                    <p class="text-body-md text-on-surface-variant"><span class="font-bold text-on-surface">{{ $products->total() }}</span> handcrafted items found</p>
                    <form method="GET" action="{{ route('shop.index') }}" class="flex items-center gap-3">
                        @foreach ($selectedCategories as $categoryId)
                            <input type="hidden" name="categories[]" value="{{ $categoryId }}">
                        @endforeach
                        @if ($maxPrice)
                            <input type="hidden" name="max_price" value="{{ $maxPrice }}">
                        @endif
                        <span class="text-label-md text-on-surface-variant uppercase tracking-widest">Sort by:</span>
                        <select name="sort" onchange="this.form.submit()" class="bg-transparent border-none text-body-md text-on-surface focus:ring-0 cursor-pointer font-semibold">
                            <option value="featured" {{ $sort === 'featured' ? 'selected' : '' }}>Featured</option>
                            <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                            <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </form>
                </div>

                @if ($products->isEmpty())
                    <p class="text-body-md text-on-surface-variant py-20 text-center">No products match these filters yet — try widening your search.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-y-12 gap-x-8">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="mt-20 flex justify-center">
                        {{ $products->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
