<?php

namespace App\Mail;

use App\Http\Controllers\HomeController;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $subscriberEmail,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're on the list — Heritage Basket",
            bcc: array_filter([Setting::first()?->contact_email]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter-welcome',
            with: HomeController::homeData(),
        );
    }
}
