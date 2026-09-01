<?php

namespace App\Http\Requests\OfferShock;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferShockRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'shock_point_id' => 'nullable|exists:shock_points,id',
        ];
    }
}
