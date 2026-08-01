<?php

namespace App\Http\Requests;

use App\Rules\DeliverableEmail;
use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
{
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
            'customer_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', new DeliverableEmail()],
            'phone' => ['required', 'string', 'regex:'.RegisterRequest::PHONE_REGEX],
            'shipping_address' => ['required', 'string', 'max:255'],
            'address_line_2' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'in:punjab,sindh,kpk,balochistan,ict'],
            'postal_code' => ['required', 'string', 'regex:'.RegisterRequest::POSTAL_REGEX],
            'payment_method' => ['required', 'in:cod,jazzcash,bank_transfer'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Please enter a valid email address.',
            'phone.regex' => 'Please enter a valid Pakistani phone number, e.g. 0300 1234567.',
            'postal_code.regex' => 'Please enter a valid postal code.',
        ];
    }
}
