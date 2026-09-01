<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Enums\StatusEnum;
use App\Models\EntityType;
use Illuminate\Database\Seeder;

class EntityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        EntityType::create([
            'code' => \App\Enums\EntityTypeEnum::MAIN_ORG,
            'label' => "Entité principale",
            'description' => "Entité principale",
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        EntityType::create([
            'code' => \App\Enums\EntityTypeEnum::INSURER,
            'label' => "Compagnie d'assurance",
            'description' => "Compagnie d'assurance",
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        EntityType::create([
            'code' => \App\Enums\EntityTypeEnum::BROKER,
            'label' => "Courtier en assurance",
            'description' => "Courtier en assurance",
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        EntityType::create([
            'code' => \App\Enums\EntityTypeEnum::REPAIRER,
            'label' => "Réparateur",
            'description' => "Réparateur",
            'status_id' => Status::firstWhere('code', StatusEnum::ACTIVE)->id,
        ]);

    }
}
