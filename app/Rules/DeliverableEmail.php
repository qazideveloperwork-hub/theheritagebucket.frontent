<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Checks an email address against the mailboxlayer API (format + MX record
 * check). Fails open on any API/network problem — a third-party outage or
 * an exhausted quota should never block registration, checkout, or the
 * newsletter form; it should just skip the extra check for that request.
 */
class DeliverableEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $apiKey = config('services.mailboxlayer.key');

        if (! $apiKey || ! is_string($value)) {
            return;
        }

        $result = Cache::remember(
            'mailboxlayer:'.md5(strtolower($value)),
            now()->addDay(),
            function () use ($apiKey, $value) {
                try {
                    $response = Http::timeout(5)->get('https://apilayer.net/api/check', [
                        'access_key' => $apiKey,
                        'email' => $value,
                        'smtp' => 1,
                        'format' => 1,
                    ]);

                    return $response->successful() ? $response->json() : null;
                } catch (Throwable $e) {
                    Log::warning('mailboxlayer check failed: '.$e->getMessage());

                    return null;
                }
            }
        );

        if (! is_array($result) || ! array_key_exists('format_valid', $result)) {
            return;
        }

        if ($result['format_valid'] === false || $result['mx_found'] === false) {
            $fail('The :attribute address does not appear to be a valid, deliverable email address.');
        }
    }
}
