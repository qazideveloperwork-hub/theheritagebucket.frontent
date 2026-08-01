@extends('layouts.app')

@section('title', 'The Artisan Journal | Heritage Basket')

@section('content')
    <div class="relative overflow-hidden">
        <div class="heritage-pattern absolute inset-0 pointer-events-none"></div>

        <section class="max-w-container-max mx-auto px-5 lg:px-16 pt-16 pb-12 text-center relative z-10">
            <span class="text-label-md text-primary uppercase tracking-widest block mb-4">Journal</span>
            <h1 class="font-serif text-headline-lg text-on-background mb-6">The Artisan Journal</h1>
            <p class="text-body-lg text-on-surface-variant max-w-2xl mx-auto italic">A chronicle of the hands that craft, the heritage that inspires, and the traditions that bind us.</p>
        </section>

        @if ($featured)
            <section class="max-w-container-max mx-auto px-5 lg:px-16 py-12 relative z-10">
                <div class="grid md:grid-cols-12 gap-8 lg:gap-gutter items-center">
                    <div class="md:col-span-7">
                        <a href="{{ route('blog.show', $featured) }}" class="block relative aspect-[4/3] overflow-hidden rounded-xl border border-outline-variant group">
                            <img src="{{ $featured->cover_image_url }}" alt="{{ $featured->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        </a>
                    </div>
                    <div class="md:col-span-5 md:pl-8 mt-8 md:mt-0">
                        @if ($featured->category)
                            <div class="text-primary text-label-md px-3 py-1 rounded-full inline-block mb-4 bg-tertiary-container/30">{{ $featured->category }}</div>
                        @endif
                        <h2 class="font-serif text-headline-md text-on-background mb-4">{{ $featured->title }}</h2>
                        <p class="text-body-md text-on-surface-variant mb-6">{{ $featured->excerpt }}</p>
                        <div class="flex items-center gap-4 mb-8">
                            <div>
                                <p class="text-label-md text-on-background">{{ $featured->author_name }}</p>
                                <p class="text-[12px] text-on-surface-variant">{{ $featured->read_minutes }} min read</p>
                            </div>
                        </div>
                        <a href="{{ route('blog.show', $featured) }}" class="inline-block bg-primary text-white text-button px-8 py-3 rounded-lg hover:scale-[1.02] transition-transform">Read the Story</a>
                    </div>
                </div>
            </section>
        @endif

        <section class="max-w-container-max mx-auto px-5 lg:px-16 py-16 lg:py-section-padding relative z-10">
            <h3 class="font-serif text-headline-sm text-on-background mb-12">Latest Chronicles</h3>

            @if ($posts->isEmpty())
                <p class="text-body-md text-on-surface-variant">More stories are on the way.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                    @foreach ($posts as $post)
                        <article class="flex flex-col group bg-surface rounded-xl border border-outline-variant overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <a href="{{ route('blog.show', $post) }}" class="aspect-[4/5] overflow-hidden block">
                                <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </a>
                            <div class="p-6 flex flex-col flex-grow">
                                @if ($post->category)
                                    <span class="text-[12px] text-primary mb-2 uppercase tracking-wide">{{ $post->category }}</span>
                                @endif
                                <a href="{{ route('blog.show', $post) }}">
                                    <h4 class="font-serif text-[20px] text-on-background mb-3 group-hover:text-primary transition-colors">{{ $post->title }}</h4>
                                </a>
                                <p class="text-[14px] text-on-surface-variant line-clamp-3 mb-6">{{ $post->excerpt }}</p>
                                <a href="{{ route('blog.show', $post) }}" class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center">
                                    <span class="text-[12px] text-on-surface-variant">{{ $post->published_at->format('M j, Y') }}</span>
                                    <span class="material-symbols-outlined text-primary text-[20px]">arrow_forward</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-12 flex justify-center">
                    {{ $posts->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
