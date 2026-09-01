<?php

namespace App\Http\Requests\OfferWorkforce;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferWorkforceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'workforce_type_id' => ['required', 'exists:workforce_types,id'],
            'hourly_rate_id' => ['required', 'exists:hourly_rates,id'],
            'paint_type_id' => ['required', 'exists:paint_types,id'],
            'with_tax' => ['required', 'boolean'],
            'nb_hours' => ['required', 'numeric'],
            'discount' => ['required', 'numeric', 'min:0', 'max:100'],
            'all_paint' => ['nullable', 'boolean'],
        ];
    }
}
