<?php

namespace App\Builders\Role;

use App\Models\Role;
use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Builder;

class RoleBuilder extends Builder
{
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

    public function isRepairerAdmin(): bool
    {
        return $this->model->currentRole->name == RoleEnum::REPAIRER_ADMIN->value;
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
            return $this->whereIn('name', [RoleEnum::EXPERT_ADMIN->value, RoleEnum::CEO->value, RoleEnum::EXPERT->value, RoleEnum::OPENER->value, RoleEnum::EDITOR->value, RoleEnum::VALIDATOR->value, RoleEnum::ACCOUNTANT_MANAGER->value, RoleEnum::ACCOUNTANT->value, RoleEnum::BUSINESS_DEVELOPER->value, RoleEnum::INSURER_ADMIN->value, RoleEnum::INSURER_STANDARD_USER->value, RoleEnum::REPAIRER_ADMIN->value, RoleEnum::REPAIRER_STANDARD_USER->value, RoleEnum::UNASSIGNED->value]);
        }

        if ($user->isInsurerAdmin()) {
            return $this->whereIn('name', [RoleEnum::INSURER_ADMIN->value, RoleEnum::INSURER_STANDARD_USER->value])->pluck('id');
        }

        if ($user->isRepairerAdmin()) {
            return $this->whereIn('name', [RoleEnum::REPAIRER_ADMIN->value, RoleEnum::REPAIRER_STANDARD_USER->value]);
        }

        return $this;
    }
}
