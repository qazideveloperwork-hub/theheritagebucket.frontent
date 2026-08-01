@extends('layouts.app')

@section('title', 'Page Not Found | Heritage Basket')

@php
    $homeData = \App\Http\Controllers\HomeController::homeData();
@endphp

@section('content')
    <div>
        <section class="py-16 lg:py-24 px-5 lg:px-margin-desktop max-w-container-max mx-auto text-center">
            <span class="text-label-md uppercase tracking-[0.2em] text-primary mb-4 block">404</span>
            <h1 class="font-serif text-headline-lg text-on-surface mb-6">We couldn't find that page</h1>
            <p class="text-body-lg text-on-surface-variant max-w-xl mx-auto mb-10">The link you followed may be broken, or the page may have been moved. Here's what's trending at Heritage Basket instead.</p>
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-10 py-5 bg-primary text-white text-button hover:bg-primary-container transition-all active:scale-[0.98]">
                Back to Home
            </a>
        </section>

        @include('partials.home.hero', $homeData)

        @include('partials.home.categories', $homeData)

        {{-- Divider --}}
        <div class="flex items-center justify-center py-8">
            <div class="h-px bg-outline-variant w-1/4"></div>
            <div class="mx-6 text-primary flex gap-2">
                <span class="material-symbols-outlined text-[12px]">filter_vintage</span>
            </div>
            <div class="h-px bg-outline-variant w-1/4"></div>
        </div>

        @include('partials.home.new-arrivals', $homeData)

        @include('partials.home.featured', $homeData)
    </div>
@endsection
