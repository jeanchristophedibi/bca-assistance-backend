<?php

namespace App\Http\Resources\Shock;

use App\Http\Resources\User\UserResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\Offer\OfferResource;
use App\Http\Resources\ShockWork\OfferShockWorkResource;
use App\Http\Resources\Workforce\OfferWorkforceResource;
use App\Http\Resources\ShockPoint\ShockPointResource;
use App\Http\Resources\PaintType\PaintTypeResource;
use App\Http\Resources\HourlyRate\HourlyRateResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferShockResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'position' => $this->position,
            'with_tax' => $this->with_tax,
            'is_before_quote' => (bool) $this->is_before_quote,
            'is_validated' => (bool) $this->is_validated,
            'quote_validated' => (bool) $this->quote_validated,
            'shock_work_in_order_amount_excluding_tax' => $this->shock_work_in_order_amount_excluding_tax,
            'shock_work_in_order_amount_tax' => $this->shock_work_in_order_amount_tax,
            'shock_work_in_order_amount' => $this->shock_work_in_order_amount,
            'shock_work_obsolescence_amount_excluding_tax' => $this->shock_work_obsolescence_amount_excluding_tax,
            'shock_work_obsolescence_amount_tax' => $this->shock_work_obsolescence_amount_tax,
            'shock_work_obsolescence_amount' => $this->shock_work_obsolescence_amount,
            'shock_work_recovery_amount_excluding_tax' => $this->shock_work_recovery_amount_excluding_tax,
            'shock_work_recovery_amount_tax' => $this->shock_work_recovery_amount_tax,
            'shock_work_recovery_amount' => $this->shock_work_recovery_amount,
            'shock_work_discount_amount_excluding_tax' => $this->shock_work_discount_amount_excluding_tax,
            'shock_work_discount_amount_tax' => $this->shock_work_discount_amount_tax,
            'shock_work_discount_amount' => $this->shock_work_discount_amount,
            'shock_work_new_amount_excluding_tax' => $this->shock_work_new_amount_excluding_tax,
            'shock_work_new_amount_tax' => $this->shock_work_new_amount_tax,
            'shock_work_new_amount' => $this->shock_work_new_amount,
            'workforce_amount_excluding_tax' => $this->workforce_amount_excluding_tax,
            'workforce_amount_tax' => $this->workforce_amount_tax,
            'workforce_amount' => $this->workforce_amount,
            'paint_product_amount_excluding_tax' => $this->paint_product_amount_excluding_tax,
            'paint_product_amount_tax' => $this->paint_product_amount_tax,
            'paint_product_amount' => $this->paint_product_amount,
            'small_supply_amount_excluding_tax' => $this->small_supply_amount_excluding_tax,
            'small_supply_amount_tax' => $this->small_supply_amount_tax,
            'small_supply_amount' => $this->small_supply_amount,
            'amount_excluding_tax' => $this->amount_excluding_tax,
            'amount_tax' => $this->amount_tax,
            'amount' => $this->amount,
            'offer' => new OfferResource($this->whenLoaded('offer')),
            'shock_point' => new ShockPointResource($this->whenLoaded('shockPoint')),
            'paint_type' => new PaintTypeResource($this->whenLoaded('paintType')),
            'hourly_rate' => new HourlyRateResource($this->whenLoaded('hourlyRate')),
            'offer_shock_works' => OfferShockWorkResource::collection($this->whenLoaded('offerShockWorks')),
            'offer_workforces' => OfferWorkforceResource::collection($this->whenLoaded('offerWorkforces')),
            'status' => new StatusResource($this->whenLoaded('status')),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'updated_by' => new UserResource($this->whenLoaded('updatedBy')),
            'deleted_by' => new UserResource($this->whenLoaded('deletedBy')),
            'deleted_at' => dateTimeFormat($this->deleted_at),
            'created_at' => dateTimeFormat($this->created_at),
            'updated_at' => dateTimeFormat($this->updated_at),
        ];
    }
}
