<!DOCTYPE html>
<html lang="en" data-cart-count="{{ $cartItemCount ?? 0 }}" data-wishlist-count="{{ $wishlistCount ?? 0 }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Heritage Basket | Handcrafted Artisanal Treasures')</title>
    <meta name="description" content="@yield('description', 'Curated, handcrafted artisanal treasures from across Pakistan.')">

    <link rel="icon" href="{{ asset('images/logo-icon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@400;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface font-sans antialiased" x-data>
    <header class="w-full top-0 sticky z-50 bg-surface border-b border-outline-variant">
        <nav class="flex justify-between items-center px-5 lg:px-margin-desktop h-20 max-w-container-max mx-auto" x-data="{ mobileOpen: false, searchOpen: false, accountOpen: false }">
            <div class="flex items-center gap-12">
                <a href="{{ route('home') }}">
                    @include('partials.logo', ['iconSize' => 24, 'gap' => 2, 'textClass' => 'text-headline-sm lg:text-headline-md'])
                </a>
                <div class="hidden lg:flex gap-8 items-center">
                    <a href="{{ route('home') }}" class="text-body-md text-on-surface-variant hover:text-primary transition-colors duration-200 {{ request()->routeIs('home') ? 'text-primary font-semibold' : '' }}">Home</a>
                    <a href="{{ route('shop.index') }}" class="text-body-md text-on-surface-variant hover:text-primary transition-colors duration-200 {{ request()->routeIs('shop.*', 'products.*') ? 'text-primary font-semibold' : '' }}">Products</a>
                    <a href="{{ route('blog.index') }}" class="text-body-md text-on-surface-variant hover:text-primary transition-colors duration-200 {{ request()->routeIs('blog.*') ? 'text-primary font-semibold' : '' }}">Blog</a>
                    <a href="{{ route('home') }}#artisan-story" class="text-body-md text-on-surface-variant hover:text-primary transition-colors duration-200">About</a>
                    <a href="{{ route('home') }}#newsletter" class="text-body-md text-on-surface-variant hover:text-primary transition-colors duration-200">Contact</a>
                </div>
            </div>

            <div class="flex items-center gap-4 lg:gap-6">
                <button
                    class="p-2 text-on-surface-variant hover:text-primary transition-all active:scale-95 hidden sm:inline-flex"
                    @click="searchOpen = !searchOpen; $nextTick(() => searchOpen && $refs.searchInput.focus())"
                    aria-label="Search products"
                >
                    <span class="material-symbols-outlined" x-text="searchOpen ? 'close' : 'search'"></span>
                </button>
                @guest
                    <a href="{{ route('login') }}" class="p-2 text-on-surface-variant hover:text-primary transition-all active:scale-95">
                        <span class="material-symbols-outlined">person</span>
                    </a>
                @endguest
                @auth
                    <div class="relative">
                        <button
                            class="p-2 text-on-surface-variant hover:text-primary transition-all active:scale-95"
                            @click="accountOpen = !accountOpen"
                            aria-label="Your account"
                        >
                            <span class="material-symbols-outlined">person</span>
                        </button>
                        <div
                            x-show="accountOpen"
                            x-cloak
                            @click.outside="accountOpen = false"
                            class="absolute right-0 top-full mt-2 w-72 max-w-[90vw] bg-surface border border-outline-variant shadow-lg py-2 z-50"
                        >
                            <p class="px-4 py-2 text-label-md text-on-surface-variant truncate whitespace-nowrap">{{ auth()->user()->name }}</p>
                            <div class="border-t border-outline-variant"></div>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-body-md text-on-surface-variant whitespace-nowrap hover:bg-surface-container hover:text-primary transition-colors">Your Profile</a>
                            <a href="{{ route('orders.index') }}" class="block px-4 py-3 text-body-md text-on-surface-variant whitespace-nowrap hover:bg-surface-container hover:text-primary transition-colors">Order History</a>
                            <a href="{{ route('profile.password.edit') }}" class="block px-4 py-3 text-body-md text-on-surface-variant whitespace-nowrap hover:bg-surface-container hover:text-primary transition-colors">Reset Password</a>
                            <div class="border-t border-outline-variant"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-3 text-body-md text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors">Logout</button>
                            </form>
                        </div>
                    </div>
                @endauth
                <a href="{{ route('wishlist.index') }}" class="p-2 text-on-surface-variant hover:text-primary transition-all active:scale-95 relative" aria-label="Your wishlist">
                    <span class="material-symbols-outlined">favorite</span>
                    <span
                        x-show="$store.wishlist.count > 0"
                        x-text="$store.wishlist.count"
                        class="absolute top-1 right-1 w-4 h-4 bg-primary text-white text-[10px] flex items-center justify-center rounded-full"
                    ></span>
                </a>
                <a href="{{ route('cart.index') }}" class="p-2 text-on-surface-variant hover:text-primary transition-all active:scale-95 relative">
                    <span class="material-symbols-outlined">shopping_basket</span>
                    <span
                        x-show="$store.cart.count > 0"
                        x-text="$store.cart.count"
                        class="absolute top-1 right-1 w-4 h-4 bg-primary text-white text-[10px] flex items-center justify-center rounded-full"
                    ></span>
                </a>
                <button class="lg:hidden p-2 text-on-surface-variant" @click="mobileOpen = !mobileOpen">
                    <span class="material-symbols-outlined" x-text="mobileOpen ? 'close' : 'menu'"></span>
                </button>
            </div>

            <div x-show="mobileOpen" x-cloak @click.outside="mobileOpen = false" class="lg:hidden absolute top-20 left-0 right-0 bg-surface border-b border-outline-variant flex flex-col p-5 gap-4 shadow-lg">
                <a href="{{ route('home') }}" class="text-body-md text-on-surface-variant hover:text-primary">Home</a>
                <a href="{{ route('shop.index') }}" class="text-body-md text-on-surface-variant hover:text-primary">Products</a>
                <a href="{{ route('blog.index') }}" class="text-body-md text-on-surface-variant hover:text-primary">Blog</a>
                <a href="{{ route('wishlist.index') }}" class="text-body-md text-on-surface-variant hover:text-primary">Wishlist</a>
                <a href="{{ route('cart.index') }}" class="text-body-md text-on-surface-variant hover:text-primary">Basket</a>
                @guest
                    <a href="{{ route('login') }}" class="text-body-md text-on-surface-variant hover:text-primary">Sign In</a>
                @endguest
                @auth
                    <a href="{{ route('profile.edit') }}" class="text-body-md text-on-surface-variant hover:text-primary">Your Profile</a>
                    <a href="{{ route('orders.index') }}" class="text-body-md text-on-surface-variant hover:text-primary">Order History</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-body-md text-on-surface-variant hover:text-primary text-left w-full">Logout</button>
                    </form>
                @endauth
            </div>

            <div x-show="searchOpen" x-cloak @click.outside="searchOpen = false" class="absolute top-20 left-0 right-0 bg-surface border-b border-outline-variant px-5 lg:px-margin-desktop py-4 shadow-lg">
                <form action="{{ route('shop.index') }}" method="GET" class="max-w-container-max mx-auto flex items-center gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant">search</span>
                    <input
                        type="text"
                        name="q"
                        x-ref="searchInput"
                        value="{{ request('q') }}"
                        placeholder="Search products…"
                        class="flex-1 bg-transparent border-0 border-b border-outline-variant py-2 text-body-md text-on-surface focus:ring-0 focus:border-primary outline-none"
                    >
                    <button type="submit" class="text-primary font-semibold hover:underline text-body-md">Search</button>
                </form>
            </div>
        </nav>
    </header>

    @if (session('status'))
        <div class="bg-tertiary-container text-on-tertiary-fixed-variant text-center text-body-md py-3 px-5">
            {{ session('status') }}
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="w-full bg-surface-container-highest">
        <div class="flex flex-col md:flex-row justify-between py-section-padding px-5 lg:px-margin-desktop max-w-container-max mx-auto gap-12">
            <div class="max-w-xs">
                <h3 class="mb-6">@include('partials.logo', ['iconSize' => 22, 'textClass' => 'text-headline-sm'])</h3>
                <p class="text-body-md text-on-surface-variant mb-8">Curating the finest artisanal masterpieces from across Pakistan to bring a piece of digital heritage into your home.</p>
                <div class="flex gap-4">
                    <span class="w-10 h-10 flex items-center justify-center border border-outline-variant rounded-full text-on-surface-variant">
                        <span class="material-symbols-outlined text-[18px]">public</span>
                    </span>
                    <span class="w-10 h-10 flex items-center justify-center border border-outline-variant rounded-full text-on-surface-variant">
                        <span class="material-symbols-outlined text-[18px]">share</span>
                    </span>
                    <span class="w-10 h-10 flex items-center justify-center border border-outline-variant rounded-full text-on-surface-variant">
                        <span class="material-symbols-outlined text-[18px]">mail</span>
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-12 flex-grow md:ml-20">
                <div>
                    <h4 class="text-label-md uppercase tracking-widest text-on-surface mb-6">Discover</h4>
                    <ul class="space-y-4">
                        <li><a class="text-on-surface-variant hover:text-primary transition-all" href="{{ route('home') }}#artisan-story">Our Story</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-all" href="{{ route('shop.index') }}">Collections</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-all" href="{{ route('blog.index') }}">Heritage Journal</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-label-md uppercase tracking-widest text-on-surface mb-6">Support</h4>
                    <ul class="space-y-4">
                        <li><a class="text-on-surface-variant hover:text-primary transition-all" href="{{ route('pages.show', 'shipping-policy') }}">Shipping Policy</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-all" href="#">Contact Us</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-all" href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-span-2 md:col-span-1" id="newsletter">
                    <h4 class="text-label-md uppercase tracking-widest text-on-surface mb-6">Newsletter</h4>
                    <p class="text-on-surface-variant text-body-md mb-4">Stay updated with our latest findings.</p>
                    <form action="{{ route('newsletter.store') }}" method="POST" class="flex flex-col gap-2" novalidate
                          x-data="formValidator([
                              { name: 'email', id: 'newsletter_email', label: 'Email Address', rules: ['required', 'email'] },
                          ], { email: @js(old('email')) })"
                          @submit="onSubmit">
                        @csrf
                        <input type="email" id="newsletter_email" name="email" x-model="values.email"
                               @blur="validateField({ name: 'email', rules: ['required', 'email'] })"
                               placeholder="Your email address"
                               class="bg-white border px-3 py-2 text-body-md focus:ring-0 focus:border-primary outline-none"
                               :class="(errors.email || @js($errors->has('email', 'newsletter'))) ? 'border-error' : 'border-outline-variant'">
                        <p class="text-label-md text-error" x-show="errors.email" x-text="errors.email" x-cloak></p>
                        @error('email', 'newsletter') <p class="text-label-md text-error" x-show="!errors.email">{{ $message }}</p> @enderror
                        <button type="submit" class="text-primary font-bold hover:underline text-left">Sign up now</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="border-t border-outline-variant py-8 px-5 lg:px-margin-desktop max-w-container-max mx-auto flex flex-col md:flex-row justify-between items-center gap-4 text-on-surface-variant text-label-md">
            <p>&copy; {{ date('Y') }} Heritage Basket. Handcrafted with love in Pakistan.</p>
            <div class="flex gap-8">
                <a class="hover:text-primary" href="#">Privacy Policy</a>
                <a class="hover:text-primary" href="#">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html>
