@props(['product'])

@php
    $badge = null;

    if ($product->is_coming_soon) {
        $badge = ['label' => 'Coming Soon', 'class' => 'bg-secondary text-on-secondary'];
    } elseif ($product->stock_status === 'out_of_stock') {
        $badge = ['label' => 'Out of Stock', 'class' => 'bg-on-surface-variant text-surface'];
    } elseif ($product->sale_price) {
        $badge = ['label' => 'Sale', 'class' => 'bg-error text-on-error'];
    } elseif ($product->discount_price && $product->discount_percentage) {
        $badge = ['label' => number_format((float) $product->discount_percentage, 0).'% OFF', 'class' => 'bg-primary text-on-primary'];
    } elseif ($product->is_new_arrival) {
        $badge = ['label' => 'New', 'class' => 'bg-secondary text-on-secondary'];
    }
@endphp

@if ($badge)
    <span {{ $attributes->merge(['class' => "px-3 py-1 text-[12px] font-medium rounded-full uppercase tracking-wider {$badge['class']}"]) }}>
        {{ $badge['label'] }}
    </span>
@endif
