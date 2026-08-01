<?php

namespace App\Http\Requests;

use App\Rules\DeliverableEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public const PHONE_REGEX = '/^(?:\+92[-\s]?|0)3[0-9]{2}[-\s]?[0-9]{7}$/';

    public const POSTAL_REGEX = '/^[0-9]{4,5}$/';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => is_string($this->email) ? trim(strtolower($this->email)) : $this->email,
        ]);
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email', new DeliverableEmail()],
            'phone' => ['required', 'string', 'regex:'.self::PHONE_REGEX],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'street' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'in:punjab,sindh,kpk,balochistan,ict'],
            'postal' => ['required', 'string', 'regex:'.self::POSTAL_REGEX],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'An account with this email address already exists.',
            'phone.regex' => 'Please enter a valid Pakistani phone number, e.g. 0300 1234567.',
            'postal.regex' => 'Please enter a valid postal code.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
