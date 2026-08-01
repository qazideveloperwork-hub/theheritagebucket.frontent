@extends('layouts.app')

@section('title', 'Your Wishlist | Heritage Basket')

@section('content')
    <div class="max-w-container-max mx-auto px-5 lg:px-margin-desktop py-16 lg:py-section-padding">
        <header class="mb-12 text-center lg:text-left">
            <h1 class="font-serif text-headline-lg text-on-surface mb-2">Your Wishlist</h1>
            <p class="text-on-surface-variant text-body-md">Pieces you've saved to revisit later.</p>
        </header>

        @if ($products->isEmpty())
            <div class="py-20 text-center">
                <p class="text-body-md text-on-surface-variant mb-6">Your wishlist is empty — start saving pieces you love.</p>
                <a href="{{ route('shop.index') }}" class="inline-block bg-primary text-on-primary px-8 py-3 text-button hover:bg-primary-container transition-all">Browse Products</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-y-12 gap-x-8">
                @foreach ($products as $product)
                    <div x-data="wishlistButton({{ $product->id }}, true)" x-show="inWishlist" x-cloak>
                        <x-product-card :product="$product" />
                        <button
                            type="button"
                            @click="toggle()"
                            :disabled="loading"
                            class="mt-3 flex items-center gap-1 text-label-md text-on-surface-variant hover:text-error transition-colors disabled:opacity-60"
                        >
                            <span class="material-symbols-outlined text-[16px]">favorite</span>
                            <span x-text="loading ? 'Removing…' : 'Remove from wishlist'"></span>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
