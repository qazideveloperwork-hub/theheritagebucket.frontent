<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Services\WishlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;

class AuthPageController extends Controller
{
    public function __construct(private readonly WishlistService $wishlistService)
    {
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ])->onlyInput('email');
        }

        if (Auth::user()->status !== 'active') {
            $message = Auth::user()->status === 'deleted'
                ? 'This account no longer exists.'
                : 'This account has been blocked. Please contact support.';

            Auth::logout();

            return back()->withErrors(['email' => $message])->onlyInput('email');
        }

        $request->session()->regenerate();

        Auth::user()->attachGuestOrders();
        $this->wishlistService->mergeGuestIntoUser(Auth::user());

        return redirect()->intended(route('home'))->with('status', 'Welcome back!');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['full_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'],
            'street' => $data['street'],
            'city' => $data['city'],
            'province' => $data['province'],
            'postal_code' => $data['postal'],
        ]);

        Auth::login($user);

        $user->attachGuestOrders();
        $this->wishlistService->mergeGuestIntoUser($user);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addDays(2),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        Mail::to($user->email)->send(new WelcomeEmail($user, $verificationUrl));

        return redirect()->route('home')->with('status', 'Welcome to Heritage Basket, '.$user->name.'!');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function forgotPassword(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'A password reset link has been sent to your email address.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill(['password' => $password])->save();
            }
        );

        // If already logged in, redirecting to the (guest-only) login route
        // would just bounce straight to home anyway — go there directly so
        // the flash message actually survives to be shown.
        return $status === Password::PASSWORD_RESET
            ? redirect()->route(Auth::check() ? 'home' : 'login')->with('status', 'Your password has been reset. You can sign in now.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'You have been signed out.');
    }

    /**
     * Informational only — nothing in the app is gated on verification, this
     * just records that the address is reachable. The route itself carries
     * the 'signed' middleware, so an invalid/expired/tampered link 403s
     * before this method ever runs.
     */
    public function verifyEmail(int $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        abort_unless(hash_equals(sha1($user->email), $hash), 403);

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return redirect()->route('home')->with('status', 'Your email address has been verified.');
    }
}
