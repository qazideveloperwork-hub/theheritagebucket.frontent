@extends('layouts.app')

@section('title', 'Your Profile | Heritage Basket')

@section('content')
    <div class="relative overflow-hidden py-16 lg:py-section-padding">
        <div class="heritage-pattern absolute inset-0 pointer-events-none"></div>
        <div class="max-w-[800px] mx-auto px-5 lg:px-margin-desktop relative z-10 w-full space-y-10">
            <header class="text-center lg:text-left">
                <h1 class="font-serif text-headline-lg text-on-surface mb-2">Your Profile</h1>
                <p class="text-on-surface-variant text-body-md">Manage your personal details.</p>
            </header>

            @if ($errors->any())
                <div class="bg-error-container text-on-error-container text-body-md p-4 rounded">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="bg-surface border border-outline-variant rounded-lg p-8 md:p-12">
                <h2 class="text-label-md uppercase tracking-widest text-primary border-b border-outline-variant pb-2 mb-6">Personal Details</h2>
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6" novalidate
                      x-data="formValidator([
                          { name: 'name', label: 'Full Name', rules: ['required'] },
                          { name: 'email', label: 'Email Address', rules: ['required', 'email'] },
                          { name: 'phone', label: 'Phone Number', rules: ['required', 'phone'] },
                          { name: 'street', label: 'Street Address', rules: ['required'] },
                          { name: 'address_line_2', label: 'Shipping Address', rules: ['required'] },
                          { name: 'city', label: 'City', rules: ['required'] },
                          { name: 'province', label: 'Province', rules: ['required'] },
                          { name: 'postal_code', label: 'Postal Code', rules: ['required', 'postal'] },
                      ], {
                          name: @js(old('name', $user->name)),
                          email: @js(old('email', $user->email)),
                          phone: @js(old('phone', $user->phone)),
                          street: @js(old('street', $user->street)),
                          address_line_2: @js(old('address_line_2', $user->address_line_2)),
                          city: @js(old('city', $user->city)),
                          province: @js(old('province', $user->province)),
                          postal_code: @js(old('postal_code', $user->postal_code)),
                      })"
                      @submit="onSubmit">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="text-label-md text-on-surface-variant block mb-1" for="name">Full Name <span class="text-error">*</span></label>
                        <input id="name" name="name" type="text" x-model="values.name" @blur="validateField({ name: 'name', rules: ['required'] })"
                               class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                               :class="(errors.name || @js($errors->has('name'))) ? 'border-error' : 'border-outline-variant'">
                        <p class="mt-1 text-label-md text-error" x-show="errors.name" x-text="errors.name" x-cloak></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-label-md text-on-surface-variant block mb-1" for="email">Email Address <span class="text-error">*</span></label>
                            <input id="email" name="email" type="email" x-model="values.email" @blur="validateField({ name: 'email', rules: ['required', 'email'] })"
                                   class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                   :class="(errors.email || @js($errors->has('email'))) ? 'border-error' : 'border-outline-variant'">
                            <p class="mt-1 text-label-md text-error" x-show="errors.email" x-text="errors.email" x-cloak></p>
                        </div>
                        <div>
                            <label class="text-label-md text-on-surface-variant block mb-1" for="phone">Phone Number <span class="text-error">*</span></label>
                            <input id="phone" name="phone" type="tel" x-model="values.phone" @blur="validateField({ name: 'phone', rules: ['required', 'phone'] })"
                                   class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                   :class="(errors.phone || @js($errors->has('phone'))) ? 'border-error' : 'border-outline-variant'">
                            <p class="mt-1 text-label-md text-error" x-show="errors.phone" x-text="errors.phone" x-cloak></p>
                        </div>
                    </div>
                    <div>
                        <label class="text-label-md text-on-surface-variant block mb-1" for="street">Street Address <span class="text-error">*</span></label>
                        <input id="street" name="street" type="text" x-model="values.street" @blur="validateField({ name: 'street', rules: ['required'] })"
                               class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                               :class="(errors.street || @js($errors->has('street'))) ? 'border-error' : 'border-outline-variant'">
                        <p class="mt-1 text-label-md text-error" x-show="errors.street" x-text="errors.street" x-cloak></p>
                    </div>
                    <div>
                        <label class="text-label-md text-on-surface-variant block mb-1" for="address_line_2">Shipping Address <span class="text-error">*</span></label>
                        <input id="address_line_2" name="address_line_2" type="text" x-model="values.address_line_2" @blur="validateField({ name: 'address_line_2', rules: ['required'] })"
                               class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                               :class="(errors.address_line_2 || @js($errors->has('address_line_2'))) ? 'border-error' : 'border-outline-variant'">
                        <p class="mt-1 text-label-md text-error" x-show="errors.address_line_2" x-text="errors.address_line_2" x-cloak></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-label-md text-on-surface-variant block mb-1" for="city">City <span class="text-error">*</span></label>
                            <input id="city" name="city" type="text" x-model="values.city" @blur="validateField({ name: 'city', rules: ['required'] })"
                                   class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                   :class="(errors.city || @js($errors->has('city'))) ? 'border-error' : 'border-outline-variant'">
                            <p class="mt-1 text-label-md text-error" x-show="errors.city" x-text="errors.city" x-cloak></p>
                        </div>
                        <div>
                            <label class="text-label-md text-on-surface-variant block mb-1" for="province">Province <span class="text-error">*</span></label>
                            <select id="province" name="province" x-model="values.province" @change="validateField({ name: 'province', rules: ['required'] })"
                                    class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                    :class="(errors.province || @js($errors->has('province'))) ? 'border-error' : 'border-outline-variant'">
                                <option value="" disabled {{ old('province', $user->province) ? '' : 'selected' }}>Select</option>
                                @foreach (['punjab' => 'Punjab', 'sindh' => 'Sindh', 'kpk' => 'KPK', 'balochistan' => 'Balochistan', 'ict' => 'Islamabad Capital Territory'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('province', $user->province) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-label-md text-error" x-show="errors.province" x-text="errors.province" x-cloak></p>
                        </div>
                    </div>
                    <div class="w-1/2">
                        <label class="text-label-md text-on-surface-variant block mb-1" for="postal_code">Postal Code <span class="text-error">*</span></label>
                        <input id="postal_code" name="postal_code" type="text" x-model="values.postal_code" @blur="validateField({ name: 'postal_code', rules: ['required', 'postal'] })"
                               class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                               :class="(errors.postal_code || @js($errors->has('postal_code'))) ? 'border-error' : 'border-outline-variant'">
                        <p class="mt-1 text-label-md text-error" x-show="errors.postal_code" x-text="errors.postal_code" x-cloak></p>
                    </div>
                    <button type="submit" class="bg-primary text-on-primary px-8 py-3 text-button hover:bg-primary-container active:scale-[0.98] transition-all">
                        Save Changes
                    </button>
                </form>
            </div>

            <p class="text-body-md text-on-surface-variant">
                Want to change your password? <a href="{{ route('profile.password.edit') }}" class="text-primary font-semibold hover:underline">Go to Change Password</a>
            </p>
        </div>
    </div>
@endsection
