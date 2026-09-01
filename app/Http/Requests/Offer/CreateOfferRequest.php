<?php

namespace App\Http\Requests\Offer;

use Illuminate\Foundation\Http\FormRequest;

class CreateOfferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comparison_id' => 'required|exists:comparisons,id',
        ];
    }
}
