<?php

namespace App\Http\Requests\Assignment;

use Illuminate\Foundation\Http\FormRequest;

class PlanAssignmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'expertise_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'expertise_time' => 'required|date_format:H:i',
            'expertise_place' => 'required|string|max:255',
            'expert_id' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'expertise_date.required' => 'La date de l\'expertise est requise.',
            'expertise_date.after_or_equal' => 'La date de l\'expertise ne peut pas être passée.',
            'expertise_time.required' => 'L\'heure de l\'expertise est requise.',
            'expertise_time.date_format' => 'L\'heure doit être au format HH:MM.',
            'expertise_place.required' => 'Le lieu de l\'expertise est requis.',
            'expert_id.exists' => 'L\'expert choisi n\'existe pas.',
        ];
    }
}
