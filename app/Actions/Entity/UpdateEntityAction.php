<?php

namespace App\Actions\Entity;
use App\Models\Entity;

class UpdateEntityAction
{
    public function execute(Entity $entity, array $data)
    {
        $entity->update([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'telephone' => $data['telephone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return $entity->fresh();
    }
}
