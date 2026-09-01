<?php

namespace App\Http\Requests\OfferShock;

use Illuminate\Foundation\Http\FormRequest;

class CreateOfferShockRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'offer_id' => 'required|exists:offers,id',
            'shocks' => 'required|array',
            'shocks.*.shock_point_id' => 'required|exists:shock_points,id|unique:offer_shocks,shock_point_id,NULL,id,offer_id,' . $this->input('offer_id'),
            'shocks.*.shock_works' => 'nullable|array',
            'shocks.*.shock_works.*.supply_id' => 'required|exists:supplies,id',
            'shocks.*.shock_works.*.disassembly' => 'required|boolean',
            'shocks.*.shock_works.*.replacement' => 'required|boolean',
            'shocks.*.shock_works.*.repair' => 'required|boolean',
            'shocks.*.shock_works.*.paint' => 'required|boolean',
            'shocks.*.shock_works.*.control' => 'required|boolean',
            'shocks.*.shock_works.*.in_order' => 'required|boolean',
            'shocks.*.shock_works.*.obsolescence' => 'required|boolean',
            'shocks.*.shock_works.*.comment' => 'nullable|string',
            'shocks.*.shock_works.*.obsolescence_rate' => 'required|numeric',
            'shocks.*.shock_works.*.recovery_amount' => 'required|numeric',
            'shocks.*.shock_works.*.amount' => 'required|numeric',
            'shocks.*.shock_works.*.discount' => 'required|numeric',

            'shocks.*.paint_type_id' => 'nullable|exists:paint_types,id',
            'shocks.*.hourly_rate_id' => 'nullable|exists:hourly_rates,id',
            'shocks.*.with_tax' => 'nullable|boolean',

            'shocks.*.workforces' => 'nullable|array',
            'shocks.*.workforces.*.workforce_type_id' => 'required|exists:workforce_types,id',
            'shocks.*.workforces.*.nb_hours' => 'required|numeric',
            'shocks.*.workforces.*.discount' => 'required|numeric',
            'shocks.*.workforces.*.all_paint' => 'nullable|boolean',
        ];
    }
}
