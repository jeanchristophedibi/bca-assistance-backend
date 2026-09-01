<?php

namespace App\Http\Resources\Entity;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\EntityType\EntityTypeResource;
use App\Http\Resources\Country\CountryResource;

class EntityResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'address' => $this->address,
            'taxpayer_account_number' => $this->taxpayer_account_number,
            'status' => new StatusResource($this->whenLoaded('status')),
            'entity_type' => new EntityTypeResource($this->whenLoaded('entityType')),
            'created_at' => dateTimeFormat($this->created_at),
            'updated_at' => dateTimeFormat($this->updated_at),
        ];
    }
}
