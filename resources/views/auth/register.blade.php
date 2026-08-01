@extends('layouts.app')

@section('title', 'Create Your Account | Heritage Basket')

@section('content')
    <div class="relative overflow-hidden flex items-center justify-center py-16 lg:py-section-padding">
        <div class="heritage-pattern absolute inset-0 pointer-events-none"></div>
        <div class="max-w-[1000px] mx-auto px-5 lg:px-margin-desktop relative z-10 w-full">
            <div class="bg-surface border border-outline-variant rounded-lg overflow-hidden flex flex-col md:flex-row shadow-sm">
                <div class="hidden md:block w-2/5 relative bg-primary-container overflow-hidden">
                    <div class="absolute inset-0 bg-cover bg-center opacity-80 mix-blend-multiply" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuARL94elIN6olwLt-QWK2Cf2oMr52QkA4tIiXRG1zVadEpwSI00U4NVUtbI47Z7cRd710GfnaW0b6zWvbj6uMER5pLNJGfXnZ3H2QEizfjc0BTl0uNn8bYg6bIEXwMFeOA2c3tLTFZ8tpMFBt6W3wkSemLpAwwKGH7pLiJboYpYhH7Qg767oyjhwQfytGN-5NdSuVKPqxgdRM4mWGWQg6A6AyyojzmXj3oAqlvftVWJn4fnZyI3zdp_HQxfXeoAiHz_GUd9jBrvYAa2');"></div>
                    <div class="relative z-10 h-full flex flex-col justify-end p-10 text-white">
                        <h2 class="font-serif text-headline-md mb-4 leading-tight">Preserving Craft, Celebrating Culture.</h2>
                        <p class="text-body-md opacity-90">Join our community of artisanal enthusiasts and gain access to limited-edition collections.</p>
                    </div>
                </div>

                <div class="flex-grow p-8 md:p-12 lg:p-16">
                    <div class="max-w-md mx-auto">
                        <header class="mb-8">
                            <h1 class="font-serif text-headline-md text-on-surface mb-2">Create Your Account</h1>
                            <p class="text-on-surface-variant text-body-md">Begin your journey through Pakistan's rich artisanal heritage.</p>
                        </header>

                        @if ($errors->any())
                            <div class="bg-error-container text-on-error-container text-body-md p-4 rounded mb-6">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register.attempt') }}" class="space-y-6" novalidate
                              x-data="formValidator([
                                  { name: 'full_name', label: 'Full Name', rules: ['required'] },
                                  { name: 'email', label: 'Email Address', rules: ['required', 'email'] },
                                  { name: 'phone', label: 'Phone Number', rules: ['required', 'phone'] },
                                  { name: 'password', label: 'Password', rules: ['required', 'password'] },
                                  { name: 'password_confirmation', label: 'Confirm Password', rules: ['required'], matches: 'password' },
                                  { name: 'street', label: 'Street Address', rules: ['required'] },
                                  { name: 'city', label: 'City', rules: ['required'] },
                                  { name: 'province', label: 'Province', rules: ['required'] },
                                  { name: 'postal', label: 'Postal Code', rules: ['required', 'postal'] },
                              ], { full_name: @js(old('full_name')), email: @js(old('email')), phone: @js(old('phone')), street: @js(old('street')), city: @js(old('city')), province: @js(old('province')), postal: @js(old('postal')) })"
                              @submit="onSubmit">
                            @csrf
                            <div class="space-y-4">
                                <h3 class="text-label-md uppercase tracking-widest text-primary border-b border-outline-variant pb-2 mb-4">Personal Details</h3>
                                <div>
                                    <label class="text-label-md text-on-surface-variant block mb-1" for="full_name">Full Name <span class="text-error">*</span></label>
                                    <input id="full_name" name="full_name" type="text" x-model="values.full_name" @blur="validateField({ name: 'full_name', rules: ['required'] })" placeholder="e.g. Zainab Malik"
                                           class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                           :class="(errors.full_name || @js($errors->has('full_name'))) ? 'border-error' : 'border-outline-variant'">
                                    <p class="mt-1 text-label-md text-error" x-show="errors.full_name" x-text="errors.full_name" x-cloak></p>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-label-md text-on-surface-variant block mb-1" for="email">Email Address <span class="text-error">*</span></label>
                                        <input id="email" name="email" type="email" x-model="values.email" @blur="validateField({ name: 'email', rules: ['required', 'email'] })" placeholder="zainab@example.com"
                                               class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                               :class="(errors.email || @js($errors->has('email'))) ? 'border-error' : 'border-outline-variant'">
                                        <p class="mt-1 text-label-md text-error" x-show="errors.email" x-text="errors.email" x-cloak></p>
                                    </div>
                                    <div>
                                        <label class="text-label-md text-on-surface-variant block mb-1" for="phone">Phone Number <span class="text-error">*</span></label>
                                        <input id="phone" name="phone" type="tel" x-model="values.phone" @blur="validateField({ name: 'phone', rules: ['required', 'phone'] })" placeholder="+92 3XX XXXXXXX"
                                               class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                               :class="(errors.phone || @js($errors->has('phone'))) ? 'border-error' : 'border-outline-variant'">
                                        <p class="mt-1 text-label-md text-error" x-show="errors.phone" x-text="errors.phone" x-cloak></p>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-label-md text-on-surface-variant block mb-1" for="password">Create Password <span class="text-error">*</span></label>
                                    <input id="password" name="password" type="password" x-model="values.password"
                                           @input="validateField({ name: 'password', rules: ['required', 'password'] })"
                                           @blur="validateField({ name: 'password', rules: ['required', 'password'] }); validateField({ name: 'password_confirmation', rules: ['required'], matches: 'password' })"
                                           placeholder="••••••••"
                                           class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                           :class="(errors.password || @js($errors->has('password'))) ? 'border-error' : 'border-outline-variant'">
                                    <ul class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-label-md" x-show="values.password" x-cloak>
                                        <template x-for="check in passwordChecklist('password')" :key="check.label">
                                            <li class="flex items-center gap-1" :class="check.met ? 'text-primary' : 'text-on-surface-variant'">
                                                <span class="material-symbols-outlined text-[14px]" x-text="check.met ? 'check_circle' : 'radio_button_unchecked'"></span>
                                                <span x-text="check.label"></span>
                                            </li>
                                        </template>
                                    </ul>
                                    <p class="mt-1 text-label-md text-error" x-show="errors.password" x-text="errors.password" x-cloak></p>
                                </div>
                                <div>
                                    <label class="text-label-md text-on-surface-variant block mb-1" for="password_confirmation">Confirm Password <span class="text-error">*</span></label>
                                    <input id="password_confirmation" name="password_confirmation" type="password" x-model="values.password_confirmation"
                                           @blur="validateField({ name: 'password_confirmation', rules: ['required'], matches: 'password' })"
                                           placeholder="••••••••"
                                           class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                           :class="errors.password_confirmation ? 'border-error' : 'border-outline-variant'">
                                    <p class="mt-1 text-label-md text-error" x-show="errors.password_confirmation" x-text="errors.password_confirmation" x-cloak></p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="text-label-md text-on-surface-variant block mb-1" for="street">Street Address <span class="text-error">*</span></label>
                                    <input id="street" name="street" type="text" x-model="values.street" @blur="validateField({ name: 'street', rules: ['required'] })" placeholder="House No, Street, Area"
                                           class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                           :class="(errors.street || @js($errors->has('street'))) ? 'border-error' : 'border-outline-variant'">
                                    <p class="mt-1 text-label-md text-error" x-show="errors.street" x-text="errors.street" x-cloak></p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-label-md text-on-surface-variant block mb-1" for="city">City <span class="text-error">*</span></label>
                                        <input id="city" name="city" type="text" x-model="values.city" @blur="validateField({ name: 'city', rules: ['required'] })" placeholder="Lahore"
                                               class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                               :class="(errors.city || @js($errors->has('city'))) ? 'border-error' : 'border-outline-variant'">
                                        <p class="mt-1 text-label-md text-error" x-show="errors.city" x-text="errors.city" x-cloak></p>
                                    </div>
                                    <div>
                                        <label class="text-label-md text-on-surface-variant block mb-1" for="province">Province <span class="text-error">*</span></label>
                                        <select id="province" name="province" x-model="values.province" @change="validateField({ name: 'province', rules: ['required'] })"
                                                class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                                :class="(errors.province || @js($errors->has('province'))) ? 'border-error' : 'border-outline-variant'">
                                            <option value="" disabled selected>Select</option>
                                            <option value="punjab">Punjab</option>
                                            <option value="sindh">Sindh</option>
                                            <option value="kpk">KPK</option>
                                            <option value="balochistan">Balochistan</option>
                                            <option value="ict">Islamabad Capital Territory</option>
                                        </select>
                                        <p class="mt-1 text-label-md text-error" x-show="errors.province" x-text="errors.province" x-cloak></p>
                                    </div>
                                </div>
                                <div class="w-1/2">
                                    <label class="text-label-md text-on-surface-variant block mb-1" for="postal">Postal Code <span class="text-error">*</span></label>
                                    <input id="postal" name="postal" type="text" x-model="values.postal" @blur="validateField({ name: 'postal', rules: ['required', 'postal'] })" placeholder="54000"
                                           class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                           :class="(errors.postal || @js($errors->has('postal'))) ? 'border-error' : 'border-outline-variant'">
                                    <p class="mt-1 text-label-md text-error" x-show="errors.postal" x-text="errors.postal" x-cloak></p>
                                </div>
                            </div>

                            <div class="flex flex-col gap-4 pt-4">
                                <button type="submit" class="w-full bg-primary text-on-primary py-4 rounded text-button hover:bg-primary-container active:scale-[0.98] transition-all">
                                    Create Account
                                </button>
                                <p class="text-center text-label-md text-on-surface-variant">
                                    Already have an account? <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Log In</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
