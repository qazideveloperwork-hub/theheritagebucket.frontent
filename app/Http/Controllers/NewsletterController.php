<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsletterRequest;
use App\Mail\NewsletterWelcomeEmail;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function store(StoreNewsletterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        NewsletterSubscriber::updateOrCreate(
            ['email' => $data['email']],
            ['subscribed_at' => now()]
        );

        Mail::to($data['email'])->send(new NewsletterWelcomeEmail($data['email']));

        return back()->with('status', "You're on the list — thank you for joining the Heritage Circle.");
    }
}
