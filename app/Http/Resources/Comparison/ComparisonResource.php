<?php

namespace App\Http\Resources\Comparison;

use App\Http\Resources\User\UserResource;
use App\Http\Resources\Offer\OfferResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\Assignment\AssignmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ComparisonResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'assignment' => new AssignmentResource($this->whenLoaded('assignment')),
            'offers' => OfferResource::collection($this->whenLoaded('offers')),
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
