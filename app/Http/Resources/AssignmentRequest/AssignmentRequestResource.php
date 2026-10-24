<?php

namespace App\Http\Resources\AssignmentRequest;

use App\Http\Resources\User\UserResource;
use App\Http\Resources\Photo\PhotoResource;
use App\Http\Resources\Client\ClientResource;
use App\Http\Resources\Entity\EntityResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\Vehicle\VehicleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference ?? null,
            'date' => dateTimeFormat($this->date),
            'assistance_place' => $this->assistance_place,
            'observation' => $this->observation,
            'new_market_value' => $this->new_market_value,
            'expert_firm' => new EntityResource($this->whenLoaded('expertFirm')),
            'repairers' => EntityResource::collection($this->whenLoaded('repairers')),
            'client' => new ClientResource($this->whenLoaded('client')),
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'photos' => PhotoResource::collection($this->whenLoaded('photos')),
            'photos_count' => $this->whenCounted('photos'),
            'status' => new StatusResource($this->whenLoaded('status')),
            'assignment' => $this->whenLoaded('assignment', fn () => $this->assignment ? [
                'id' => $this->assignment->id,
                'reference' => $this->assignment->reference,
                'status' => new StatusResource($this->assignment->status),
            ] : null),
            'accepted_by' => new UserResource($this->whenLoaded('acceptedBy')),
            'accepted_at' => dateTimeFormat($this->accepted_at),
            'rejection_reason' => $this->rejection_reason,
            'rejected_at' => dateTimeFormat($this->rejected_at),
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => dateTimeFormat($this->cancelled_at),
            'cancelled_by' => new UserResource($this->whenLoaded('cancelledBy')),
            'rejected_by' => new UserResource($this->whenLoaded('rejectedBy')),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'updated_by' => new UserResource($this->whenLoaded('updatedBy')),
            'deleted_by' => new UserResource($this->whenLoaded('deletedBy')),
            'deleted_at' => dateTimeFormat($this->deleted_at),
            'created_at' => dateTimeFormat($this->created_at),
            'updated_at' => dateTimeFormat($this->updated_at),
        ];
    }
}
