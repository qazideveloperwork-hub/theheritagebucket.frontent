<?php

namespace App\Mail;

use App\Http\Controllers\HomeController;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $resetUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Heritage Basket Password',
            bcc: array_filter([Setting::first()?->contact_email]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.forgot-password',
            with: [
                'user' => $this->user,
                'resetUrl' => $this->resetUrl,
                ...HomeController::homeData(),
            ],
        );
    }
}
