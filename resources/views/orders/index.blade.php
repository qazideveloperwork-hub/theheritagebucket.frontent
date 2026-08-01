@extends('layouts.app')

@section('title', 'Order History | Heritage Basket')

@section('content')
    <div class="py-16 lg:py-section-padding px-5 lg:px-margin-desktop max-w-container-max mx-auto">
        <header class="mb-10">
            <h1 class="font-serif text-headline-lg text-on-surface mb-2">Order History</h1>
            <p class="text-on-surface-variant text-body-md">Track and review everything you've ordered from Heritage Basket.</p>
        </header>

        @if ($orders->isEmpty())
            <div class="bg-surface-container-low border border-outline-variant p-12 text-center">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-4 block">inventory_2</span>
                <p class="text-body-lg text-on-surface-variant mb-6">You haven't placed any orders yet.</p>
                <a href="{{ route('shop.index') }}" class="inline-block bg-primary text-white px-8 py-3 text-button hover:bg-primary-container transition-all">Start Shopping</a>
            </div>
        @else
            <div class="border border-outline-variant divide-y divide-outline-variant">
                @foreach ($orders as $order)
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6">
                        <div>
                            <p class="text-body-md font-bold text-on-surface">{{ $order->order_number }}</p>
                            <p class="text-label-md text-on-surface-variant">{{ $order->created_at->format('d M Y') }} &middot; Rs. {{ number_format((float) $order->total) }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="px-3 py-1 text-[12px] font-medium rounded-full uppercase tracking-wider {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span>
                            <a href="{{ route('orders.show', $order) }}" class="text-label-md text-primary border-b border-primary pb-1 hover:text-primary-container transition-colors">View</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
