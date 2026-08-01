@extends('layouts.app')

@section('title', 'Login | Heritage Basket')

@section('content')
    <div class="min-h-[80vh] flex items-stretch">
        <section class="hidden lg:flex lg:w-1/2 relative bg-surface-container overflow-hidden">
            <div class="absolute inset-0 heritage-pattern"></div>
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDgZ8rMLsL-EUdsOatz7XTaVNc-wO4-FgldgmB3F_vdsAtlwmUatCHugt82RGqWYCtBKSLCNN1y-Oshh9h7OGGeoG2K9ubPHleYaVLSVaWh7RztpW0jXWzkQwe2W4fovYd1Xrp1Rn6JKnZPZn5-YYcBlMtJ4zwlZi-9s6D2eBlzgeFF0QNdtOzOUeFhyJHxbLAy3a2qdH2zpaIWUkKQPRsMxv0wSXY3Is3acnILofcgFMJd4FyYOMMmjJYoZzV-DQmSjf8ShJETaAyL');"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
            <div class="absolute bottom-16 left-16 right-16 z-10 max-w-md">
                <p class="font-serif text-headline-sm text-white italic drop-shadow-lg mb-4">"Every thread tells a story of generations, preserved for the modern world."</p>
            </div>
        </section>

        <section class="w-full lg:w-1/2 flex flex-col justify-center items-center px-5 lg:px-margin-desktop relative bg-surface py-16">
            <div class="w-full max-w-[440px] space-y-8">
                <header class="space-y-2 text-center lg:text-left">
                    <h1 class="font-serif text-headline-lg text-on-surface">Welcome Back</h1>
                    <p class="text-body-md text-on-surface-variant">Sign in to access your curated collection of handcrafted treasures.</p>
                </header>

                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-6" novalidate
                      x-data="formValidator([
                          { name: 'email', label: 'Email Address', rules: ['required', 'email'] },
                          { name: 'password', label: 'Password', rules: ['required'] },
                      ], { email: @js(old('email')) })"
                      @submit="onSubmit">
                    @csrf
                    <div class="flex flex-col gap-1">
                        <label class="text-label-md text-on-surface-variant" for="email">Email Address</label>
                        <input id="email" name="email" type="email" placeholder="your@email.com" value="{{ old('email') }}"
                               x-model="values.email" @blur="validateField({ name: 'email', rules: ['required', 'email'] })"
                               class="bg-surface border-0 border-b py-3 px-3 text-body-md text-on-surface focus:ring-0 focus:border-primary transition-all outline-none"
                               :class="(errors.email || @js($errors->has('email'))) ? 'border-error' : 'border-outline-variant'">
                        @error('email') <p class="text-label-md text-error" x-show="!errors.email">{{ $message }}</p> @enderror
                        <p class="text-label-md text-error" x-show="errors.email" x-text="errors.email" x-cloak></p>
                    </div>
                    <div class="flex flex-col gap-1" x-data="{ show: false }">
                        <label class="text-label-md text-on-surface-variant" for="password">Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" id="password" name="password" placeholder="••••••••"
                                   x-model="values.password" @blur="validateField({ name: 'password', rules: ['required'] })"
                                   class="w-full bg-surface border-0 border-b py-3 px-3 text-body-md text-on-surface focus:ring-0 focus:border-primary transition-all outline-none"
                                   :class="(errors.password || @js($errors->has('password'))) ? 'border-error' : 'border-outline-variant'">
                            <button type="button" @click="show = !show" class="absolute right-0 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary">
                                <span class="material-symbols-outlined text-[20px]" x-text="show ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        @error('password') <p class="text-label-md text-error" x-show="!errors.password">{{ $message }}</p> @enderror
                        <p class="text-label-md text-error" x-show="errors.password" x-text="errors.password" x-cloak></p>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 border-outline-variant rounded">
                            <span class="text-label-md text-on-surface-variant">Remember Me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-label-md text-primary hover:underline">Forgot Password?</a>
                    </div>

                    <button type="submit" class="w-full py-4 bg-primary text-white text-button rounded-lg hover:bg-primary-container active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        Sign In
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </form>

                <footer class="text-center pt-4">
                    <p class="text-body-md text-on-surface-variant">
                        New to Heritage Basket?
                        <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline ml-1">Create an Account</a>
                    </p>
                </footer>
            </div>
        </section>
    </div>
@endsection
