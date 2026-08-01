@extends('layouts.app')

@section('title', 'Order Confirmed | Heritage Basket')

@section('content')
    <div class="max-w-2xl mx-auto px-5 lg:px-0 py-16 lg:py-section-padding text-center">
        <span class="material-symbols-outlined text-6xl text-primary mb-6 block">check_circle</span>
        <h1 class="font-serif text-headline-lg text-on-surface mb-4">Thank You, {{ $order->customer_name }}!</h1>
        <p class="text-body-lg text-on-surface-variant mb-2">Your order has been placed successfully.</p>
        <p class="text-body-md text-on-surface-variant mb-10">Order Number: <span class="font-bold text-primary">{{ $order->order_number }}</span></p>

        <div class="bg-surface-container-low border border-outline-variant p-8 text-left">
            <h3 class="font-serif text-headline-sm text-on-surface mb-6">Order Summary</h3>
            <div class="space-y-3 mb-6">
                @foreach ($order->items as $item)
                    <div class="flex justify-between items-start gap-4 text-body-md">
                        <span class="text-on-surface-variant">{{ $item->product_name }}{{ $item->variation_label ? ' ('.$item->variation_label.')' : '' }} &times; {{ $item->quantity }}</span>
                        <span class="text-on-surface shrink-0 whitespace-nowrap">Rs. {{ number_format((float) $item->line_total) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-outline-variant pt-4 space-y-2">
                <div class="flex justify-between text-body-md text-on-surface-variant">
                    <span>Subtotal</span>
                    <span>Rs. {{ number_format((float) $order->subtotal) }}</span>
                </div>
                <div class="flex justify-between text-body-md text-on-surface-variant">
                    <span>Shipping cost</span>
                    <span>Rs. {{ number_format((float) $order->shipping_fee) }}</span>
                </div>
                <div class="flex justify-between text-headline-sm font-serif text-on-surface pt-2">
                    <span>Total</span>
                    <span>Rs. {{ number_format((float) $order->total) }}</span>
                </div>
            </div>
            @php
                $paymentLabels = [
                    'cod' => 'Cash on Delivery',
                    'jazzcash' => 'JazzCash',
                    'bank_transfer' => 'Bank Transfer',
                ];
            @endphp
            <p class="text-label-md text-on-surface-variant mt-6">
                Payment method: {{ $paymentLabels[$order->payment_method] ?? ucfirst($order->payment_method) }} &middot;
                Shipping to {{ $order->shipping_address }}{{ $order->address_line_2 ? ', '.$order->address_line_2 : '' }}, {{ $order->city }}
            </p>
        </div>

        <a href="{{ route('shop.index') }}" class="inline-block bg-primary text-white px-10 py-4 text-button hover:bg-primary-container transition-all mt-10">Continue Shopping</a>
    </div>
@endsection
