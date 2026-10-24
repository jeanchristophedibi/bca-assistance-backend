<?php

namespace App\Http\Requests\AssignmentRequest;

use Illuminate\Foundation\Http\FormRequest;

class AcceptAssignmentRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'assignment_id' => 'required|exists:assignments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'assignment_id.required' => 'Le dossier créé à partir de la demande est requis.',
            'assignment_id.exists' => 'Le dossier indiqué n\'existe pas.',
        ];
    }
}
