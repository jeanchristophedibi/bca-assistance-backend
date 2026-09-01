<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

class CreateEntityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'code' => ['nullable', 'string', 'max:255', 'unique:entities,code'],
            // 'name' => ['required', 'string', 'max:255', 'unique:entities,name'],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'regex:' . self::emailRegex(), 'string', 'max:255', 'unique:entities,email'],
            'telephone' => ['nullable', 'string', 'max:255'],
            'entity_type_code' => ['required', 'exists:entity_types,code'],
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
