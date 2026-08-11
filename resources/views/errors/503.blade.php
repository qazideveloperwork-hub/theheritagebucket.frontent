<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon | Heritage Basket</title>
    <meta name="description" content="Heritage Basket is putting the finishing touches on something beautiful. We'll be live shortly.">
    <meta name="robots" content="noindex">

    <link rel="icon" href="{{ asset('images/logo-icon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@400;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center px-5">
        <div class="text-center max-w-xl">
            @include('partials.logo', ['iconSize' => 28, 'gap' => 10, 'textClass' => 'text-headline-md lg:text-headline-lg'])

            <div class="flex items-center justify-center py-8">
                <div class="h-px bg-outline-variant w-16"></div>
                <div class="mx-6 text-primary flex gap-2">
                    <span class="material-symbols-outlined text-[16px]">filter_vintage</span>
                </div>
                <div class="h-px bg-outline-variant w-16"></div>
            </div>

            <span class="text-label-md uppercase tracking-[0.2em] text-primary mb-4 block">Coming Soon</span>
            <h1 class="font-serif text-headline-lg text-on-surface mb-6">Something Beautiful is Being Woven</h1>
            <p class="text-body-lg text-on-surface-variant leading-relaxed">
                We're putting the finishing touches on our collection of handcrafted, artisanal treasures. Heritage Basket will be open shortly — thank you for your patience.
            </p>
        </div>
    </div>
</body>
</html>
