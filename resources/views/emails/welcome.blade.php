@component('emails.layout', ['subject' => 'Welcome to Heritage Basket'])
    <p style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#a5360d; margin:0 0 8px;">Welcome</p>
    <h1 style="font-family: Georgia, serif; font-size:24px; color:#1b1b18; margin:0 0 16px;">Hello, {{ $user->name }}!</h1>
    <p style="margin:0 0 16px;">Thank you for creating an account with Heritage Basket. We're delighted to have you join our community of artisanal enthusiasts.</p>
    <p style="margin:0 0 24px;">Please confirm your email address to make sure we can always reach you about your orders.</p>
    <p style="text-align:center; margin:0 0 24px;">
        <a href="{{ $verificationUrl }}" style="display:inline-block; background-color:#a5360d; color:#ffffff; text-decoration:none; font-size:14px; font-weight:bold; padding:14px 32px;">Verify Email Address</a>
    </p>
    <p style="font-size:13px; color:#8a6c56; margin:0;">If the button doesn't work, copy and paste this link into your browser:<br><a href="{{ $verificationUrl }}" style="color:#a5360d;">{{ $verificationUrl }}</a></p>

    @slot('showcase')
        @include('emails.partials.product-showcase')
    @endslot
@endcomponent
