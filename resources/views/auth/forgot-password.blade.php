@extends('layouts.app')

@section('title', 'Reset Your Password | Heritage Basket')

@section('content')
    <div class="relative flex items-center justify-center px-5 py-16 lg:py-section-padding overflow-hidden min-h-[70vh]">
        <div class="heritage-pattern absolute inset-0 pointer-events-none"></div>

        <div class="w-full max-w-[480px] bg-white border border-outline-variant p-10 md:p-12 relative z-10">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-surface-container mb-6 text-primary">
                    <span class="material-symbols-outlined text-[32px]">lock_reset</span>
                </div>
                <h2 class="font-serif text-headline-sm text-on-surface mb-3">Reset Your Password</h2>
                <p class="text-body-md text-on-surface-variant max-w-[280px] mx-auto">Enter your email address to receive a reset link.</p>
            </div>

            @if ($errors->any())
                <div class="bg-error-container text-on-error-container text-body-md p-4 rounded mb-6">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-8" novalidate
                  x-data="formValidator([
                      { name: 'email', label: 'Email Address', rules: ['required', 'email'] },
                  ], { email: @js(old('email')) })"
                  @submit="onSubmit">
                @csrf
                <div>
                    <label class="text-label-md text-on-surface-variant block mb-2 uppercase tracking-widest" for="email">Email Address</label>
                    <input id="email" name="email" type="email" x-model="values.email" @blur="validateField({ name: 'email', rules: ['required', 'email'] })" placeholder="e.g., artisan@heritage.com"
                           class="w-full bg-surface-bright border-b py-3 px-3 text-body-lg text-on-surface focus:ring-0 focus:border-primary transition-all outline-none"
                           :class="(errors.email || @js($errors->has('email'))) ? 'border-error' : 'border-outline-variant'">
                    <p class="mt-1 text-label-md text-error" x-show="errors.email" x-text="errors.email" x-cloak></p>
                </div>
                <button type="submit" class="w-full bg-primary text-on-primary py-4 px-6 text-button uppercase tracking-widest active:scale-[0.98] hover:bg-primary-container transition-all flex items-center justify-center gap-2">
                    <span>Send Reset Link</span>
                    <span class="material-symbols-outlined">arrow_right_alt</span>
                </button>
            </form>

            <div class="relative flex items-center justify-center my-10">
                <div class="flex-grow border-t border-outline-variant"></div>
                <span class="mx-4 text-outline-variant">
                    <span class="material-symbols-outlined text-sm">eco</span>
                </span>
                <div class="flex-grow border-t border-outline-variant"></div>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-md">arrow_back</span>
                    <span class="text-label-md uppercase tracking-widest">Return to Login</span>
                </a>
            </div>
        </div>
    </div>
@endsection
