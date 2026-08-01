@component('emails.layout', ['subject' => 'Order Confirmed'])
    <p style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#a5360d; margin:0 0 8px;">Order Confirmed</p>
    <h1 style="font-family: Georgia, serif; font-size:24px; color:#1b1b18; margin:0 0 16px;">Thank you, {{ $order->customer_name }}!</h1>
    <p style="margin:0 0 4px;">Your order has been placed successfully.</p>
    <p style="margin:0 0 24px;">Order Number: <strong style="color:#a5360d;">{{ $order->order_number }}</strong></p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#faf4ec; border:1px solid #e6d9c5;">
        <tr>
            <td style="padding:20px;">
                <p style="font-family: Georgia, serif; font-size:16px; color:#1b1b18; margin:0 0 12px;">Order Summary</p>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;">
                    @foreach ($order->items as $item)
                        <tr>
                            <td style="padding:4px 0; color:#5a4538;">{{ $item->product_name }}{{ $item->variation_label ? ' ('.$item->variation_label.')' : '' }} &times; {{ $item->quantity }}</td>
                            <td style="padding:4px 0; color:#1b1b18; text-align:right;">Rs. {{ number_format((float) $item->line_total) }}</td>
                        </tr>
                    @endforeach
                </table>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px; border-top:1px solid #e6d9c5; margin-top:12px;">
                    <tr>
                        <td style="padding-top:12px; color:#5a4538;">Subtotal</td>
                        <td style="padding-top:12px; color:#1b1b18; text-align:right;">Rs. {{ number_format((float) $order->subtotal) }}</td>
                    </tr>
                    <tr>
                        <td style="color:#5a4538;">Shipping cost</td>
                        <td style="color:#1b1b18; text-align:right;">Rs. {{ number_format((float) $order->shipping_fee) }}</td>
                    </tr>
                    <tr>
                        <td style="padding-top:8px; font-family: Georgia, serif; font-size:16px; color:#1b1b18;">Total</td>
                        <td style="padding-top:8px; font-family: Georgia, serif; font-size:16px; color:#1b1b18; text-align:right;">Rs. {{ number_format((float) $order->total) }}</td>
                    </tr>
                </table>
                @php
                    $paymentLabels = ['cod' => 'Cash on Delivery', 'jazzcash' => 'JazzCash', 'bank_transfer' => 'Bank Transfer'];
                @endphp
                <p style="font-size:12px; color:#8a6c56; margin:16px 0 0;">
                    Payment method: {{ $paymentLabels[$order->payment_method] ?? ucfirst($order->payment_method) }} &middot;
                    Shipping to {{ $order->shipping_address }}{{ $order->address_line_2 ? ', '.$order->address_line_2 : '' }}, {{ $order->city }}
                </p>
            </td>
        </tr>
    </table>

    @slot('showcase')
        @include('emails.partials.product-showcase')
    @endslot
@endcomponent
