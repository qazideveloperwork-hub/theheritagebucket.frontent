@component('emails.layout', ['subject' => "You're on the list"])
    <p style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#a5360d; margin:0 0 8px;">Heritage Circle</p>
    <h1 style="font-family: Georgia, serif; font-size:24px; color:#1b1b18; margin:0 0 16px;">You're on the list!</h1>
    <p style="margin:0 0 16px;">Thank you for joining the Heritage Circle. You'll be the first to hear about new collections, artisan stories, and private showcases.</p>

    @slot('showcase')
        @include('emails.partials.product-showcase')
    @endslot
@endcomponent
