<?php

namespace App\Http\Requests\EntityEmail;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEntityEmailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "email" => "required|email|unique:entity_emails,email," . $this->email,
            "entity_id" => "required|exists:entities,id|integer",
        ];
    }

    public function messages(): array
    {
        return [
            "email.required" => "L'email est requis.",
            "email.email" => "L'email doit être valide.",
            "email.unique" => "L'email doit être unique.",
            "entity_id.required" => "L'entité est requise.",
            "entity_id.exists" => "L'entité n'existe pas.",
            "entity_id.integer" => "L'entité doit être un entier.",
        ];
    }
}
