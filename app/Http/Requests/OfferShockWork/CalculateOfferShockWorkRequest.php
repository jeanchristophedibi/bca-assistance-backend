<?php

namespace App\Http\Requests\OfferShockWork;

use Illuminate\Foundation\Http\FormRequest;

class CalculateOfferShockWorkRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'offer_shock_id' => 'required|exists:offer_shocks,id',
            'shock_works' => 'required|array',
            'shock_works.*.obsolescence_rate' => 'required|numeric',
            'shock_works.*.recovery_amount' => 'required|numeric',
            'shock_works.*.discount' => 'required|numeric|min:0|max:100',
            'shock_works.*.amount' => 'required|numeric',
        ];
    }
}
