@extends('layouts.app')

@section('title', $page->title.' | Heritage Basket')

@section('content')
    <article class="max-w-3xl mx-auto px-5 lg:px-0 py-16 lg:py-section-padding">
        <h1 class="font-serif text-headline-lg text-on-background mb-10">{{ $page->title }}</h1>

        <div class="prose max-w-none text-body-lg text-on-surface-variant leading-relaxed">
            {!! $page->content !!}
        </div>
    </article>
@endsection
