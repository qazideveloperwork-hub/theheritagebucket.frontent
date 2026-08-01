@extends('layouts.app')

@section('title', $post->title.' | Heritage Basket')
@section('description', $post->excerpt)

@section('content')
    <article class="max-w-3xl mx-auto px-5 lg:px-0 py-16 lg:py-section-padding">
        <nav class="flex items-center gap-2 text-label-md text-on-surface-variant mb-6">
            <a href="{{ route('blog.index') }}" class="hover:text-primary">Journal</a>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <span class="text-primary font-semibold">{{ $post->category ?? 'Story' }}</span>
        </nav>

        <h1 class="font-serif text-headline-lg text-on-background mb-4">{{ $post->title }}</h1>
        <p class="text-body-md text-on-surface-variant mb-8">{{ $post->author_name }} &middot; {{ $post->published_at->format('M j, Y') }} &middot; {{ $post->read_minutes }} min read</p>

        <div class="aspect-[16/9] overflow-hidden rounded-xl border border-outline-variant mb-10">
            <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
        </div>

        <div class="prose max-w-none text-body-lg text-on-surface-variant leading-relaxed space-y-6">
            @foreach (explode("\n\n", $post->body) as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>

        <div class="mt-16 pt-8 border-t border-outline-variant">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-secondary transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
                Back to the Journal
            </a>
        </div>
    </article>
@endsection
