@extends('layouts.app')

@section('title', 'Change Password | Heritage Basket')

@section('content')
    <div class="relative overflow-hidden py-16 lg:py-section-padding">
        <div class="heritage-pattern absolute inset-0 pointer-events-none"></div>
        <div class="max-w-[800px] mx-auto px-5 lg:px-margin-desktop relative z-10 w-full space-y-10">
            <header class="text-center lg:text-left">
                <h1 class="font-serif text-headline-lg text-on-surface mb-2">Change Password</h1>
                <p class="text-on-surface-variant text-body-md">Update the password you use to sign in.</p>
            </header>

            @if ($errors->any())
                <div class="bg-error-container text-on-error-container text-body-md p-4 rounded">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="bg-surface border border-outline-variant rounded-lg p-8 md:p-12">
                <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-6" novalidate
                      x-data="formValidator([
                          { name: 'current_password', label: 'Current Password', rules: ['required'] },
                          { name: 'password', label: 'New Password', rules: ['required', 'password'] },
                          { name: 'password_confirmation', label: 'Confirm New Password', rules: ['required'], matches: 'password' },
                      ])"
                      @submit="onSubmit">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="text-label-md text-on-surface-variant block mb-1" for="current_password">Current Password</label>
                        <input id="current_password" name="current_password" type="password" x-model="values.current_password"
                               @blur="validateField({ name: 'current_password', rules: ['required'] })"
                               class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                               :class="(errors.current_password || @js($errors->has('current_password'))) ? 'border-error' : 'border-outline-variant'">
                        <p class="mt-1 text-label-md text-error" x-show="errors.current_password" x-text="errors.current_password" x-cloak></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-label-md text-on-surface-variant block mb-1" for="password">New Password</label>
                            <input id="password" name="password" type="password" x-model="values.password"
                                   @input="validateField({ name: 'password', rules: ['required', 'password'] })"
                                   @blur="validateField({ name: 'password', rules: ['required', 'password'] }); validateField({ name: 'password_confirmation', rules: ['required'], matches: 'password' })"
                                   class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                   :class="(errors.password || @js($errors->has('password'))) ? 'border-error' : 'border-outline-variant'">
                            <p class="mt-1 text-label-md text-error" x-show="errors.password" x-text="errors.password" x-cloak></p>
                        </div>
                        <div>
                            <label class="text-label-md text-on-surface-variant block mb-1" for="password_confirmation">Confirm New Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" x-model="values.password_confirmation"
                                   @blur="validateField({ name: 'password_confirmation', rules: ['required'], matches: 'password' })"
                                   class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                   :class="errors.password_confirmation ? 'border-error' : 'border-outline-variant'">
                            <p class="mt-1 text-label-md text-error" x-show="errors.password_confirmation" x-text="errors.password_confirmation" x-cloak></p>
                        </div>
                    </div>
                    <ul class="grid grid-cols-2 gap-x-4 gap-y-1 text-label-md" x-show="values.password" x-cloak>
                        <template x-for="check in passwordChecklist('password')" :key="check.label">
                            <li class="flex items-center gap-1" :class="check.met ? 'text-primary' : 'text-on-surface-variant'">
                                <span class="material-symbols-outlined text-[14px]" x-text="check.met ? 'check_circle' : 'radio_button_unchecked'"></span>
                                <span x-text="check.label"></span>
                            </li>
                        </template>
                    </ul>
                    <button type="submit" class="bg-primary text-on-primary px-8 py-3 text-button hover:bg-primary-container active:scale-[0.98] transition-all">
                        Update Password
                    </button>
                </form>
            </div>

            <p class="text-body-md text-on-surface-variant">
                <a href="{{ route('profile.edit') }}" class="text-primary font-semibold hover:underline">&larr; Back to Your Profile</a>
            </p>
        </div>
    </div>
@endsection
