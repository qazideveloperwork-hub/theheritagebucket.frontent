@extends('layouts.app')

@section('title', 'Order '.$order->order_number.' | Heritage Basket')

@section('content')
    <div class="max-w-2xl mx-auto px-5 lg:px-0 py-16 lg:py-section-padding">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors mb-8">
            <span class="material-symbols-outlined text-md">arrow_back</span>
            <span class="text-label-md uppercase tracking-widest">Back to Order History</span>
        </a>

        <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
            <div>
                <h1 class="font-serif text-headline-lg text-on-surface mb-1">{{ $order->order_number }}</h1>
                <p class="text-body-md text-on-surface-variant">Placed on {{ $order->created_at->format('d M Y') }}</p>
            </div>
            <span class="px-4 py-2 text-label-md font-medium rounded-full uppercase tracking-wider {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span>
        </div>

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
    </div>
@endsection
