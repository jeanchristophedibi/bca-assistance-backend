<?php

namespace App\Builders\Assignment;

use App\Models\User;
use App\Models\Status;
use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Models\AssignmentType;
use App\Enums\AssignmentTypeEnum;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder;

class AssignmentBuilder extends Builder
{
    private static ?int $insurerAssignmentTypeId = null;

    public function isSuperAdmin(): bool
    {
        return $this->model->currentRole->name == RoleEnum::SYSTEM_ADMIN->value;
    }

    public function isAdmin(): bool
    {
        return $this->model->currentRole->name == RoleEnum::ADMIN->value;
    }

    public function isInsurerAdmin(): bool
    {
        return $this->model->currentRole->name == RoleEnum::INSURER_ADMIN->value;
    }

    public function isInsurerStandardUser(): bool
    {
        return $this->model->currentRole->name == RoleEnum::INSURER_STANDARD_USER->value;
    }

    public function isRepairerAdmin(): bool
    {
        return $this->model->currentRole->name == RoleEnum::REPAIRER_ADMIN->value;
    }

    public function isRepairerStandardUser(): bool
    {
        return $this->model->currentRole->name == RoleEnum::REPAIRER_STANDARD_USER->value;
    }

    public function isClient(): bool
    {
        return $this->model->currentRole->name == RoleEnum::CLIENT->value;
    }

    public function accessibleBy(?User $user)
    {
        if (empty($user)) {
            return $this;
        }

        if ($user->isSuperAdmin()) {
            return $this;
        }

        if ($user->isAdmin()) {
            return $this;
        }

        if (self::$insurerAssignmentTypeId === null) {
            self::$insurerAssignmentTypeId = AssignmentType::where('code', AssignmentTypeEnum::INSURER->value)->value('id');
        }

        if ($user->isInsurerAdmin()) {
            return $this->where([
                'insurer_id' => $user->entity_id,
                'assignment_type_id' => self::$insurerAssignmentTypeId,
            ]);
        }

        if ($user->isInsurerStandardUser()) {
            return $this->where([
                'insurer_id' => $user->entity_id,
                'assignment_type_id' => self::$insurerAssignmentTypeId,
            ]);
        }

        if ($user->isRepairerAdmin()) {
            return $this->whereHas('repairers', fn ($q) => $q->where('entities.id', $user->entity_id))
                ->whereNotNull('work_sheet_established_at');
        }

        if ($user->isRepairerStandardUser()) {
            return $this->whereHas('repairers', fn ($q) => $q->where('entities.id', $user->entity_id))
                ->whereNotNull('work_sheet_established_at');
        }

        if ($user->isClient()) {
            return $this->where('client_id', $user->client_id);
        }

        return $this;
    }
}
