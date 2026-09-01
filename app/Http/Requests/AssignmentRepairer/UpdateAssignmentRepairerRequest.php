<?php

namespace App\Http\Requests\AssignmentRepairer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRepairerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'repairer_id' => 'required|exists:entities,id',
        ];
    }

    public function messages(): array
    {
        return [
            'repairer_id.required' => 'Le réparateur est requis.',
            'repairer_id.exists' => 'Le réparateur est invalide.',
        ];
    }
}
