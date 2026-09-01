<?php

namespace App\Http\Resources\Photo;

use App\Models\PhotoType;
use App\Enums\PhotoTypeEnum;
use Carbon\Carbon;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Status\StatusResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PhotoType\PhotoTypeResource;

class PhotoResource extends JsonResource
{
    public function toArray($request): array
    {
        $workSheetTypeId = PhotoType::where('code', PhotoTypeEnum::WORK_SHEET)->first()?->id;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'is_cover' => (bool) $this->is_cover,
            'photo' => $this->resolvePrimaryPhotoUrl($workSheetTypeId),
            'work_sheet_photo' => $this->resolveWorkSheetPhotoUrl(),
            'assignment_request_photo' => $this->resolveAssignmentRequestPhotoUrl(),
            'assignment' => $this->assignment ? [
                'id' => $this->assignment->id,
                'reference' => $this->assignment->reference,
            ] : null,
            'photo_type' => new PhotoTypeResource($this->whenLoaded('photoType')),
            'status' => new StatusResource($this->whenLoaded('status')),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'updated_by' => new UserResource($this->whenLoaded('updatedBy')),
            'deleted_by' => new UserResource($this->whenLoaded('deletedBy')),
            'made_as_cover_at' => dateTimeFormat($this->made_as_cover_at),
            'deleted_at' => dateTimeFormat($this->deleted_at),
            'created_at' => dateTimeFormat($this->created_at),
            'updated_at' => dateTimeFormat($this->updated_at),
        ];
    }

    /**
     * Si updated_at > 2026-05-11 et PHOTO_PATH est défini : URL sous config services.settings.photo_path,
     * avec segments Y/m/d puis le même chemin relatif que storage (photos/report|work-sheet|assignment_request/…).
     */
    private function urlForStorageRelativePath(string $relativePath): string
    {
        $relativePath = ltrim($relativePath, '/');

        if ($this->shouldUseConfiguredPhotoPath()) {
            return rtrim((string) config('services.settings.photo_path'), '/')
                .'/storage'
                .'/'
                .$relativePath;
        }

        return url('storage/'.$relativePath);
    }

    private function shouldUseConfiguredPhotoPath(): bool
    {
        $date = "2026-05-11";
        $base = config('services.settings.photo_path');

        if ($base === null || $base === '') {
            return false;
        }

        return $this->updated_at
            && $this->updated_at->lessThanOrEqualTo(Carbon::parse($date));
    }

    private function resolvePrimaryPhotoUrl(?int $workSheetTypeId): string
    {
        $reference = $this->assignment?->reference;

        $folder = ($workSheetTypeId !== null && (int) $this->photo_type_id === (int) $workSheetTypeId)
            ? 'photos/work-sheet'
            : 'photos/report';

        return $this->urlForStorageRelativePath($folder.'/'.$reference.'/'.$this->name);
    }

    private function resolveWorkSheetPhotoUrl(): string
    {
        $reference = $this->assignment?->reference;

        return $this->urlForStorageRelativePath('photos/work-sheet/'.$reference.'/'.$this->name);
    }

    private function resolveAssignmentRequestPhotoUrl(): ?string
    {
        if (! $this->assignmentRequest) {
            return null;
        }

        return $this->urlForStorageRelativePath(
            'photos/assignment_request/'.$this->assignmentRequest->reference.'/'.$this->name
        );
    }
}
