@extends('layouts.app')

@section('title', 'Checkout | Heritage Basket')

@section('content')
    @php
        $subtotal = $cart->subtotal();
        $total = $subtotal + $shippingFee;
        $billing = auth()->user();
    @endphp

    <div class="max-w-container-max mx-auto px-5 lg:px-margin-desktop py-16 lg:py-section-padding grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-10 gap-4">
                <h1 class="font-serif text-headline-lg text-on-surface">Checkout</h1>
                @if ($siteSettings?->whatsapp_number)
                    <a href="https://wa.me/{{ $siteSettings->whatsapp_number }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-body-md text-on-surface-variant hover:text-primary transition-colors">
                        <x-whatsapp-icon class="w-8 h-8" />
                        Need help? Chat on WhatsApp
                    </a>
                @endif
            </div>

            <form method="POST" action="{{ route('checkout.store') }}" class="space-y-8" novalidate
                  x-data="formValidator([
                      { name: 'customer_name', label: 'Full Name', rules: ['required'] },
                      { name: 'email', label: 'Email Address', rules: ['required', 'email'] },
                      { name: 'phone', label: 'Phone Number', rules: ['required', 'phone'] },
                      { name: 'shipping_address', label: 'Street Address', rules: ['required'] },
                      { name: 'address_line_2', label: 'Shipping Address', rules: ['required'] },
                      { name: 'city', label: 'City', rules: ['required'] },
                      { name: 'province', label: 'Province', rules: ['required'] },
                      { name: 'postal_code', label: 'Postal Code', rules: ['required', 'postal'] },
                  ], {
                      customer_name: @js(old('customer_name', $billing?->name)),
                      email: @js(old('email', $billing?->email)),
                      phone: @js(old('phone', $billing?->phone)),
                      shipping_address: @js(old('shipping_address', $billing?->street)),
                      address_line_2: @js(old('address_line_2', $billing?->address_line_2)),
                      city: @js(old('city', $billing?->city)),
                      province: @js(old('province', $billing?->province)),
                      postal_code: @js(old('postal_code', $billing?->postal_code)),
                  })"
                  @submit="onSubmit">
                @csrf
                <div class="space-y-4">
                    <h3 class="text-label-md uppercase tracking-widest text-primary border-b border-outline-variant pb-2">Contact &amp; Shipping</h3>
                    <div>
                        <label class="text-label-md text-on-surface-variant block mb-1" for="customer_name">Full Name <span class="text-error">*</span></label>
                        <input id="customer_name" name="customer_name" type="text" x-model="values.customer_name" @blur="validateField({ name: 'customer_name', rules: ['required'] })"
                               class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                               :class="(errors.customer_name || @js($errors->has('customer_name'))) ? 'border-error' : 'border-outline-variant'">
                        <p class="mt-1 text-label-md text-error" x-show="errors.customer_name" x-text="errors.customer_name" x-cloak></p>
                        @error('customer_name') <p class="mt-1 text-label-md text-error" x-show="!errors.customer_name">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-label-md text-on-surface-variant block mb-1" for="email">Email Address <span class="text-error">*</span></label>
                            <input id="email" name="email" type="email" x-model="values.email" @blur="validateField({ name: 'email', rules: ['required', 'email'] })"
                                   class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                   :class="(errors.email || @js($errors->has('email'))) ? 'border-error' : 'border-outline-variant'">
                            <p class="mt-1 text-label-md text-error" x-show="errors.email" x-text="errors.email" x-cloak></p>
                            @error('email') <p class="mt-1 text-label-md text-error" x-show="!errors.email">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-label-md text-on-surface-variant block mb-1" for="phone">Phone Number <span class="text-error">*</span></label>
                            <input id="phone" name="phone" type="tel" x-model="values.phone" @blur="validateField({ name: 'phone', rules: ['required', 'phone'] })"
                                   class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                   :class="(errors.phone || @js($errors->has('phone'))) ? 'border-error' : 'border-outline-variant'">
                            <p class="mt-1 text-label-md text-error" x-show="errors.phone" x-text="errors.phone" x-cloak></p>
                            @error('phone') <p class="mt-1 text-label-md text-error" x-show="!errors.phone">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="text-label-md text-on-surface-variant block mb-1" for="shipping_address">Street Address <span class="text-error">*</span></label>
                        <input id="shipping_address" name="shipping_address" type="text" x-model="values.shipping_address" @blur="validateField({ name: 'shipping_address', rules: ['required'] })"
                               class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                               :class="(errors.shipping_address || @js($errors->has('shipping_address'))) ? 'border-error' : 'border-outline-variant'">
                        <p class="mt-1 text-label-md text-error" x-show="errors.shipping_address" x-text="errors.shipping_address" x-cloak></p>
                        @error('shipping_address') <p class="mt-1 text-label-md text-error" x-show="!errors.shipping_address">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-label-md text-on-surface-variant block mb-1" for="address_line_2">Shipping Address <span class="text-error">*</span></label>
                        <input id="address_line_2" name="address_line_2" type="text" x-model="values.address_line_2" @blur="validateField({ name: 'address_line_2', rules: ['required'] })"
                               class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                               :class="(errors.address_line_2 || @js($errors->has('address_line_2'))) ? 'border-error' : 'border-outline-variant'">
                        <p class="mt-1 text-label-md text-error" x-show="errors.address_line_2" x-text="errors.address_line_2" x-cloak></p>
                        @error('address_line_2') <p class="mt-1 text-label-md text-error" x-show="!errors.address_line_2">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-label-md text-on-surface-variant block mb-1" for="city">City <span class="text-error">*</span></label>
                            <input id="city" name="city" type="text" x-model="values.city" @blur="validateField({ name: 'city', rules: ['required'] })"
                                   class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                   :class="(errors.city || @js($errors->has('city'))) ? 'border-error' : 'border-outline-variant'">
                            <p class="mt-1 text-label-md text-error" x-show="errors.city" x-text="errors.city" x-cloak></p>
                            @error('city') <p class="mt-1 text-label-md text-error" x-show="!errors.city">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-label-md text-on-surface-variant block mb-1" for="province">Province <span class="text-error">*</span></label>
                            <select id="province" name="province" x-model="values.province" @change="validateField({ name: 'province', rules: ['required'] })"
                                    class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                                    :class="(errors.province || @js($errors->has('province'))) ? 'border-error' : 'border-outline-variant'">
                                <option value="" disabled {{ old('province', $billing?->province) ? '' : 'selected' }}>Select</option>
                                <option value="punjab" {{ old('province', $billing?->province) === 'punjab' ? 'selected' : '' }}>Punjab</option>
                                <option value="sindh" {{ old('province', $billing?->province) === 'sindh' ? 'selected' : '' }}>Sindh</option>
                                <option value="kpk" {{ old('province', $billing?->province) === 'kpk' ? 'selected' : '' }}>KPK</option>
                                <option value="balochistan" {{ old('province', $billing?->province) === 'balochistan' ? 'selected' : '' }}>Balochistan</option>
                                <option value="ict" {{ old('province', $billing?->province) === 'ict' ? 'selected' : '' }}>Islamabad Capital Territory</option>
                            </select>
                            <p class="mt-1 text-label-md text-error" x-show="errors.province" x-text="errors.province" x-cloak></p>
                            @error('province') <p class="mt-1 text-label-md text-error" x-show="!errors.province">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="w-1/2">
                        <label class="text-label-md text-on-surface-variant block mb-1" for="postal_code">Postal Code <span class="text-error">*</span></label>
                        <input id="postal_code" name="postal_code" type="text" x-model="values.postal_code" @blur="validateField({ name: 'postal_code', rules: ['required', 'postal'] })"
                               class="w-full bg-transparent border-0 border-b py-2 px-3 text-body-md focus:ring-0 focus:border-primary outline-none"
                               :class="(errors.postal_code || @js($errors->has('postal_code'))) ? 'border-error' : 'border-outline-variant'">
                        <p class="mt-1 text-label-md text-error" x-show="errors.postal_code" x-text="errors.postal_code" x-cloak></p>
                        @error('postal_code') <p class="mt-1 text-label-md text-error" x-show="!errors.postal_code">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-4" x-data="{ method: '{{ old('payment_method', 'cod') }}' }">
                    <h3 class="text-label-md uppercase tracking-widest text-primary border-b border-outline-variant pb-2">Payment Method</h3>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="payment_method" value="cod" x-model="method" class="w-4 h-4 text-primary">
                        <span class="text-body-md text-on-surface">Cash on Delivery</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="payment_method" value="jazzcash" x-model="method" class="w-4 h-4 text-primary">
                        <span class="text-body-md text-on-surface">JazzCash</span>
                    </label>
                    <div x-show="method === 'jazzcash'" x-cloak class="ml-7 bg-surface-container-low border border-outline-variant p-4 text-body-md text-on-surface-variant space-y-1">
                        @if ($siteSettings?->jazzcash_number)
                            <p>Send payment to JazzCash: <span class="font-semibold text-on-surface">{{ $siteSettings->jazzcash_number }}</span></p>
                            <p>Account title: <span class="font-semibold text-on-surface">{{ $siteSettings->jazzcash_account_title }}</span></p>
                        @else
                            <p>We'll share JazzCash account details after you place the order.</p>
                        @endif
                    </div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="payment_method" value="bank_transfer" x-model="method" class="w-4 h-4 text-primary">
                        <span class="text-body-md text-on-surface">Bank Transfer</span>
                    </label>
                    <div x-show="method === 'bank_transfer'" x-cloak class="ml-7 bg-surface-container-low border border-outline-variant p-4 text-body-md text-on-surface-variant space-y-1">
                        @if ($siteSettings?->bank_account_number)
                            <p>Bank: <span class="font-semibold text-on-surface">{{ $siteSettings->bank_name }}</span></p>
                            <p>Account number: <span class="font-semibold text-on-surface">{{ $siteSettings->bank_account_number }}</span></p>
                            <p>Account title: <span class="font-semibold text-on-surface">{{ $siteSettings->bank_account_title }}</span></p>
                        @else
                            <p>We'll share bank account details after you place the order.</p>
                        @endif
                    </div>
                    @error('payment_method') <p class="text-label-md text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-label-md text-on-surface-variant block mb-1" for="notes">Order Notes (optional)</label>
                    <textarea id="notes" name="notes" rows="3" class="w-full bg-transparent border p-3 text-body-md focus:ring-0 focus:border-primary @error('notes') border-error @else border-outline-variant @enderror">{{ old('notes') }}</textarea>
                    @error('notes') <p class="mt-1 text-label-md text-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full bg-primary text-white py-4 text-button hover:bg-primary-container transition-all active:scale-[0.98]">
                    Place Order
                </button>
            </form>
        </div>

        <div class="bg-surface-container-low border border-outline-variant p-6 h-fit">
            <h3 class="font-serif text-headline-sm text-on-surface mb-6">Order Summary</h3>
            <div class="space-y-4 mb-6">
                @foreach ($cart->items as $item)
                    <div class="flex justify-between items-start gap-4 text-body-md">
                        <span class="text-on-surface-variant">{{ $item->product->name }}{{ $item->variation?->size ? ' ('.$item->variation->size->name.')' : '' }} &times; {{ $item->quantity }}</span>
                        <span class="text-on-surface shrink-0 whitespace-nowrap">Rs. {{ number_format($item->lineTotal()) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-outline-variant pt-4 space-y-2">
                <div class="flex justify-between text-body-md text-on-surface-variant">
                    <span>Subtotal</span>
                    <span>Rs. {{ number_format($subtotal) }}</span>
                </div>
                <div class="flex justify-between text-body-md text-on-surface-variant">
                    <span>Shipping cost</span>
                    <span>Rs. {{ number_format($shippingFee) }}</span>
                </div>
                <div class="flex justify-between text-headline-sm font-serif text-on-surface pt-2">
                    <span>Total</span>
                    <span>Rs. {{ number_format($total) }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
