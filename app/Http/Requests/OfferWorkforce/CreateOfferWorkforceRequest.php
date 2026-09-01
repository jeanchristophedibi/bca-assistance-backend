<?php

namespace App\Http\Requests\OfferWorkforce;

use Illuminate\Foundation\Http\FormRequest;

class CreateOfferWorkforceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'offer_shock_id' => 'required|exists:offer_shocks,id',
            'hourly_rate_id' => 'required|exists:hourly_rates,id',
            'with_tax' => 'required|boolean',
            'paint_type_id' => 'required|exists:paint_types,id',
            'workforces' => 'required|array',
            'workforces.*.workforce_type_id' => 'required',
            'workforces.*.nb_hours' => 'required|numeric',
            'workforces.*.discount' => 'required|numeric|min:0|max:100',
            'workforces.*.all_paint' => 'nullable|boolean',
        ];
    }
}
