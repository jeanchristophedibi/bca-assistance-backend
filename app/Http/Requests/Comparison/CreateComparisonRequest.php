<?php

namespace App\Http\Requests\Comparison;

use Illuminate\Foundation\Http\FormRequest;

class CreateComparisonRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'assignment_id' => 'required|exists:assignments,id',
            'starts_at' => 'nullable|date_format:Y-m-d',
            'ends_at' => 'nullable|date_format:Y-m-d',
        ];
    }
}
