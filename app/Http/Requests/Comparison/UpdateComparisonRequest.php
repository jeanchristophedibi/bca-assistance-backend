<?php

namespace App\Http\Requests\Comparison;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComparisonRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'starts_at' => 'nullable|date_format:Y-m-d',
            'ends_at' => 'nullable|date_format:Y-m-d',
        ];
    }
}
