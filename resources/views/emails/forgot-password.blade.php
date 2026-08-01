@component('emails.layout', ['subject' => 'Reset Your Password'])
    <p style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#a5360d; margin:0 0 8px;">Password Reset</p>
    <h1 style="font-family: Georgia, serif; font-size:24px; color:#1b1b18; margin:0 0 16px;">Hello, {{ $user->name }}</h1>
    <p style="margin:0 0 16px;">We received a request to reset the password for your Heritage Basket account. Click the button below to choose a new password.</p>
    <p style="text-align:center; margin:0 0 24px;">
        <a href="{{ $resetUrl }}" style="display:inline-block; background-color:#a5360d; color:#ffffff; text-decoration:none; font-size:14px; font-weight:bold; padding:14px 32px;">Reset Password</a>
    </p>
    <p style="font-size:13px; color:#8a6c56; margin:0 0 8px;">This link will expire in 60 minutes. If you didn't request a password reset, you can safely ignore this email.</p>
    <p style="font-size:13px; color:#8a6c56; margin:0;">If the button doesn't work, copy and paste this link into your browser:<br><a href="{{ $resetUrl }}" style="color:#a5360d;">{{ $resetUrl }}</a></p>

    @slot('showcase')
        @include('emails.partials.product-showcase')
    @endslot
@endcomponent
