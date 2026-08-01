@extends('layouts.app')

@section('title', 'Heritage Basket | Handcrafted Artisanal Treasures')

@section('content')
    <div>
        @include('partials.home.hero')

        @include('partials.home.categories')

        {{-- Divider --}}
        <div class="flex items-center justify-center py-8">
            <div class="h-px bg-outline-variant w-1/4"></div>
            <div class="mx-6 text-primary flex gap-2">
                <span class="material-symbols-outlined text-[12px]">filter_vintage</span>
            </div>
            <div class="h-px bg-outline-variant w-1/4"></div>
        </div>

        @include('partials.home.new-arrivals')

        {{-- Featured / Artisan Story --}}
        <section id="artisan-story" class="py-16 lg:py-section-padding bg-surface-container overflow-hidden">
            <div class="px-5 lg:px-margin-desktop max-w-container-max mx-auto grid grid-cols-1 lg:grid-cols-2 items-center gap-16">
                <div class="relative">
                    <div class="relative z-10 border border-outline-variant aspect-square md:aspect-[4/5] overflow-hidden">
                        <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ $artisanStory?->image?->largeUrl() ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuCM1FWoSjpxDOHVumAUOPXdq0acK8zJrn2YAJEey4d1JF6oOleaN_ypG0AD5zN0CBNYbS_bpVMg8DHZAyJG3TqeysM12fbHkWxYf_I55D3gsb9hqbRrW_3IIoWU0R4UMdrpCp9Vc9J04Of_2S-gcOh0bd2M7aWTs9LXe3vME9V4fNIHgCUscWJsBUdwS5Kn0cDUffv1qkx7akCZLZUePw6Pd3Cn_MkUpQcA7lmWJqsHfXKPKejsacURE0a8CgMaPVagywZwoFGWtF5u' }}');"></div>
                    </div>
                </div>
                <div class="relative z-10">
                    <span class="text-label-md uppercase tracking-[0.2em] text-primary mb-4 block">{{ $artisanStory?->heading_1 ?? 'Meet Our Hands' }}</span>
                    <h2 class="font-serif text-headline-lg text-on-surface mb-8">{{ $artisanStory?->heading_2 ?? 'The Artisan Story' }}</h2>
                    <p class="text-body-lg text-on-surface-variant mb-6 leading-relaxed">{{ $artisanStory?->description ?? 'Every piece at Heritage Basket tells a story of generations. Behind each hand-painted tile and each hand-woven thread is an artisan who has dedicated their life to perfecting their craft.' }}</p>
                    <p class="text-body-md text-on-surface-variant mb-10 italic">"{{ $artisanStory?->quote ?? "We don't just sell products; we carry the breath of our ancestors in these vessels. Each pattern is a prayer, each stitch a legacy." }}"</p>
                </div>
            </div>
        </section>

        @include('partials.home.featured')

        {{-- Newsletter --}}
        <section class="py-16 lg:py-section-padding px-5 lg:px-margin-desktop max-w-container-max mx-auto">
            <div class="max-w-4xl mx-auto bg-surface-bright border border-outline-variant p-12 md:p-20 text-center relative overflow-hidden">
                <h2 class="font-serif text-headline-md text-on-surface mb-6">Join the Collective</h2>
                <p class="text-body-md text-on-surface-variant mb-10 max-w-xl mx-auto">Subscribe to receive stories from our artisan workshops, early access to new collections, and invitations to private showcases.</p>
                <form action="{{ route('newsletter.store') }}" method="POST" class="flex flex-col md:flex-row gap-4 max-w-md mx-auto" novalidate
                      x-data="formValidator([
                          { name: 'email', id: 'home_newsletter_email', label: 'Email Address', rules: ['required', 'email'] },
                      ], { email: @js(old('email')) })"
                      @submit="onSubmit">
                    @csrf
                    <div class="flex-grow text-left">
                        <input type="email" id="home_newsletter_email" name="email" x-model="values.email"
                               @blur="validateField({ name: 'email', rules: ['required', 'email'] })"
                               placeholder="Your email address"
                               class="w-full bg-white border-none border-b py-3 px-4 text-body-md text-on-surface focus:ring-0 focus:border-primary outline-none"
                               :class="(errors.email || @js($errors->has('email', 'newsletter'))) ? 'border-error' : 'border-outline'">
                        <p class="mt-1 text-label-md text-error" x-show="errors.email" x-text="errors.email" x-cloak></p>
                        @error('email', 'newsletter') <p class="mt-1 text-label-md text-error" x-show="!errors.email">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="bg-primary text-white px-8 py-3 text-button hover:bg-primary-container transition-all h-fit">Subscribe</button>
                </form>
            </div>
        </section>
    </div>
@endsection
