<?php

namespace App\Http\Requests\Assignment;

use App\Models\AssignmentType;
use App\Enums\AssignmentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssignmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'vehicle_mileage' => 'nullable|numeric',
            'insurer_id' => 'nullable|exists:entities,id',
            // 'insurer_id' => 'nullable|required_if:assignment_type_id,'.AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()->id.'|exists:entities,id',
            'broker_id' => 'nullable|exists:entities,id',
            'additional_insurer_id' => 'nullable|exists:entities,id',
            'repairer_id' => 'nullable|exists:entities,id',
            'repairers' => 'nullable|array',
            'repairers.*.repairer_id' => 'required_with:repairers|exists:entities,id',
            'assignment_type_id' => 'required|exists:assignment_types,id',
            'expertise_type_id' => 'required|exists:expertise_types,id',
            'document_transmitted_id' => 'nullable|array',
            'document_transmitted_id.*' => 'required|exists:document_transmitteds,id',

            'policy_number' => 'nullable|string',
            // 'weproov_reference' => [
            //     'nullable',
            //     'string',
            //     'max:255',
            //     Rule::unique('assignments', 'weproov_reference')->ignore($this->route('assignment')),
            // ],            
            // 'claim_number' => 'nullable|string|unique:assignments,claim_number',
            'claim_starts_at' => 'nullable|date_format:Y-m-d',
            'claim_ends_at' => 'nullable|date_format:Y-m-d',
            'expertise_date' => 'nullable|date_format:Y-m-d',
            'expertise_place' => 'nullable|string',
            'received_at' => 'nullable|date_format:Y-m-d',

            'administrator' => 'nullable|string',
            'circumstance' => 'nullable|string',
            'damage_declared' => 'nullable|string',
            'observation' => 'nullable|string',

            'experts' => 'nullable|array',
            'experts.*.expert_id' => 'nullable|exists:users,id',
            'experts.*.date' => 'nullable|date_format:Y-m-d',
            'experts.*.observation' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'insurer_id.required' => 'L\'assureur est requis',
            'claim_starts_at.date' => 'La date est invalide.',
            'claim_ends_at.date' => 'La date est invalide.',
            'expertise_date.date' => 'La date est invalide.',
            'received_at.date' => 'La date est invalide.',
            'claim_starts_at.date_format' => 'Le format de la date est invalide.',
            'claim_ends_at.date_format' => 'Le format de la date est invalide.',
            'expertise_date.date_format' => 'Le format de la date est invalide.',
            'received_at.date_format' => 'Le format de la date est invalide.',
            'experts.*.date.date' => 'La date est invalide.',
            'experts.*.date.date_format' => 'Le format de la date est invalide.',
            'weproov_reference.unique' => 'La référence WEPROOV existe déjà pour un dossier ouvert, réalisé, rédigé ou validé',
        ];
    }
}
