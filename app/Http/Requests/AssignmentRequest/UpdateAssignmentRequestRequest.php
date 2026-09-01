<?php

namespace App\Http\Requests\AssignmentRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'repairer_id' => 'nullable|exists:entities,id',
            'repairers' => 'nullable|array',
            'repairers.*.repairer_id' => 'required_with:repairers|exists:entities,id',
            'date' => 'nullable|date_format:Y-m-d',
            'assistance_place' => 'nullable|string',
            'observation' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.exists' => 'Le véhicule est invalide.',
            'repairer_id.exists' => 'Le réparateur est invalide.',
            'repairers.*.repairer_id.exists' => 'Le réparateur est invalide.',
        ];
    }
}
