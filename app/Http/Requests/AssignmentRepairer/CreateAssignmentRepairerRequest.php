<?php

namespace App\Http\Requests\AssignmentRepairer;

use Illuminate\Foundation\Http\FormRequest;

class CreateAssignmentRepairerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'assignment_id' => 'required|exists:assignments,id',
            'repairer_id' => 'required|exists:entities,id',
        ];
    }

    public function messages(): array
    {
        return [
            'assignment_id.required' => 'Le dossier est requis.',
            'assignment_id.exists' => 'Le dossier est invalide.',
            'repairer_id.required' => 'Le réparateur est requis.',
            'repairer_id.exists' => 'Le réparateur est invalide.',
        ];
    }
}
