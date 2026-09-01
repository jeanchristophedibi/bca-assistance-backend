<?php

namespace App\Builders\Offer;

use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Builder;

class OfferBuilder extends Builder
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

        if ($user->isInsurerAdmin()) {
            return $this->whereHas('comparison.assignment', fn ($q) => $q->where('insurer_id', $user->entity_id));
        }

        if ($user->isInsurerStandardUser()) {
            return $this->whereHas('comparison.assignment', fn ($q) => $q->where('insurer_id', $user->entity_id));
        }

        if ($user->isRepairerAdmin()) {
            return $this->where('offers.repairer_id', $user->entity_id);
        }

        if ($user->isRepairerStandardUser()) {
            return $this->where('offers.repairer_id', $user->entity_id);
        }

        if ($user->isClient()) {
            return $this->whereHas('comparison.assignment', fn ($q) => $q->where('client_id', $user->client_id));
        }

        return $this;
    }
}
