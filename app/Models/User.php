<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\ForgotPasswordEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

#[Fillable(['name', 'email', 'password', 'phone', 'street', 'address_line_2', 'city', 'province', 'postal_code'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Attach any guest-checkout orders placed under this account's email
     * address (order confirmation only reaches an inbox this user controls).
     */
    public function attachGuestOrders(): void
    {
        Order::whereNull('user_id')->where('email', $this->email)->update(['user_id' => $this->id]);
    }

    /**
     * Overrides the default framework notification so the reset email uses
     * our branded template instead of Laravel's plain default styling.
     */
    public function sendPasswordResetNotification($token): void
    {
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $this->email]);

        Mail::to($this->email)->send(new ForgotPasswordEmail($this, $resetUrl));
    }
}
