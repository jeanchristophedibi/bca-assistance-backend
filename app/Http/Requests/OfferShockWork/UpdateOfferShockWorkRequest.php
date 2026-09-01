<?php

namespace App\Http\Requests\OfferShockWork;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferShockWorkRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'supply_id' => 'required|exists:supplies,id',
            'disassembly' => 'required|boolean',
            'replacement' => 'required|boolean',
            'repair' => 'required|boolean',
            'paint' => 'required|boolean',
            'obsolescence' => 'required|boolean',
            'control' => 'required|boolean',
            'in_order' => 'required|boolean',
            'comment' => 'nullable|string',
            'obsolescence_rate' => 'required|numeric',
            'recovery_amount' => 'required|numeric',
            'discount' => 'required|numeric|min:0|max:100',
            'amount' => 'required|numeric',
        ];
    }
}
