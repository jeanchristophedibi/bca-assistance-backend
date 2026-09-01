<?php

namespace App\Http\Resources\AssignmentRepairer;

use App\Http\Resources\Entity\EntityResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentRepairerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'assignment_id' => $this->assignment_id,
            'repairer_id' => $this->repairer_id,
            'repairer' => new EntityResource($this->whenLoaded('repairer')),
            'status' => new StatusResource($this->whenLoaded('status')),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'updated_by' => new UserResource($this->whenLoaded('updatedBy')),
            'deleted_by' => new UserResource($this->whenLoaded('deletedBy')),
            'created_at' => dateTimeFormat($this->created_at),
            'updated_at' => dateTimeFormat($this->updated_at),
            'deleted_at' => dateTimeFormat($this->deleted_at),
        ];
    }
}
