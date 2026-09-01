<?php

namespace App\Http\Requests\Client;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'regex:' . self::emailRegex(), 'max:255', Rule::unique('clients', 'email')->ignore($this->email, 'email')],
            'phone_1' => ['nullable', 'string', 'max:20'],
            'phone_2' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_exempt' => ['nullable', 'boolean'],
        ];
    }

    // Regex for email address (RFC 5322 Official Standard, simplified for most use cases)
    public function messages(): array
    {
        return [
            'email.regex' => 'L\'adresse email doit être valide.',
        ];
    }

    public static function emailRegex(): string
    {
        return '/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/';
    }
}
