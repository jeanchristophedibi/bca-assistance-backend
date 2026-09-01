<?php

namespace Database\Seeders;

use App\Models\Entity;
use App\Models\Status;
use App\Models\EntityType;
use App\Enums\EntityCodeEnum;
use Illuminate\Database\Seeder;
use App\Actions\Entity\CreateEntityAction;

class EntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::BCA_CI->value,
            'name' => 'BCA-CI',
            'email' => 'support@bca-ci.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::MAIN_ORG)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::NSIA->value,
            'name' => 'NSIA',
            'email' => 'support@nsia.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::INSURER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::AXA->value,
            'name' => 'AXA',
            'email' => 'support@axa.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::INSURER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::SANLAM_ALLIANZ->value,
            'name' => 'SANLAM-ALLIANZ',
            'email' => 'support@sanlamallianz.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::INSURER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::GNA->value,
            'name' => 'GNA',
            'email' => 'support@gna.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::INSURER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::ATLANTIQUE->value,
            'name' => 'ATLANTIQUE',
            'email' => 'support@atlantique.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::INSURER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::COMAR->value,
            'name' => 'COMAR',
            'email' => 'support@comar.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::INSURER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::AFG->value,
            'name' => 'AFG',
            'email' => 'support@afg.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::INSURER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::ATLANTA->value,
            'name' => 'ATLANTA',
            'email' => 'support@atlanta.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::INSURER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::ACTIVA->value,
            'name' => 'ACTIVA',
            'email' => 'support@activa.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::INSURER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::SIDAM->value,
            'name' => 'SIDAM',
            'email' => 'support@sidam.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::INSURER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::ASCOMA->value,
            'name' => 'ASCOMA',
            'email' => 'support@ascoma.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::BROKER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::OLEA->value,
            'name' => 'OLEA',
            'email' => 'support@olea.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::BROKER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::WTW->value,
            'name' => 'Willis Towers Watson',
            'email' => 'support@wtw.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::BROKER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);

        app(CreateEntityAction::class)->execute([
            'code' => EntityCodeEnum::CFAO->value,
            'name' => 'CFAO',
            'email' => 'support@cfao.com',
            'address' => null,
            'telephone' => null,
            'entity_type_code' => EntityType::firstWhere('code', \App\Enums\EntityTypeEnum::REPAIRER)->code,
            'status_id' => Status::firstWhere('code', \App\Enums\StatusEnum::ACTIVE)->id,
        ]);
    }
}
