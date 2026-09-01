<?php

namespace App\Http\Requests\AssignmentRequestRepairer;

use Illuminate\Foundation\Http\FormRequest;

class CreateAssignmentRequestRepairerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'assignment_request_id' => 'required|exists:assignment_requests,id',
            'repairer_id' => 'required|exists:entities,id',
        ];
    }

    public function messages(): array
    {
        return [
            'assignment_request_id.required' => 'La demande d\'expertise est requise.',
            'assignment_request_id.exists' => 'La demande d\'expertise est invalide.',
            'repairer_id.required' => 'Le réparateur est requis.',
            'repairer_id.exists' => 'Le réparateur est invalide.',
        ];
    }
}
