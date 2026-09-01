<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $statuses = [
            ['code' => StatusEnum::ACTIVE, 'label' => "Actif(ve)", 'description' => "Actif(ve)"],
            ['code' => StatusEnum::INACTIVE, 'label' => "Inactif(ve)", 'description' => "Inactif(ve)"],
            ['code' => StatusEnum::OPENED, 'label' => "Ouvert", 'description' => "Ouvert"],
            ['code' => StatusEnum::REALIZED, 'label' => "Réalisé", 'description' => "Réalisé"],
            ['code' => StatusEnum::EDITED, 'label' => "Rédigé", 'description' => "Rédigé"],
            ['code' => StatusEnum::VALIDATED, 'label' => "Validé", 'description' => "Validé"],
            ['code' => StatusEnum::CLOSED, 'label' => "Clôturé", 'description' => "Clôturé"],
            ['code' => StatusEnum::CANCELLED, 'label' => "Annulé", 'description' => "Annulé"],
            ['code' => StatusEnum::ARCHIVED, 'label' => "Archivé", 'description' => "Archivé"],
            ['code' => StatusEnum::DELETED, 'label' => "Supprimé", 'description' => "Supprimé"],
            ['code' => StatusEnum::IN_PAYMENT, 'label' => "En cours de paiement", 'description' => "En cours de paiement"],
            ['code' => StatusEnum::PAID, 'label' => "Payé", 'description' => "Payé"],
            ['code' => StatusEnum::PENDING_FOR_OPENING, 'label' => "En attente d'ouverture", 'description' => "En attente d'ouverture"],
            ['code' => StatusEnum::PENDING_FOR_REPAIRER_QUOTE, 'label' => "En attente de devis du réparateur", 'description' => "En attente de devis du réparateur"],
            ['code' => StatusEnum::PENDING_FOR_REPAIRER_QUOTE_VALIDATION, 'label' => "En attente de validation du devis du réparateur", 'description' => "En attente de validation du devis du réparateur"],
            ['code' => StatusEnum::IN_EDITING, 'label' => "En rédaction", 'description' => "En rédaction"],
            ['code' => StatusEnum::IN_FIRST_CORRECTION, 'label' => "En première correction", 'description' => "En première correction"],
            ['code' => StatusEnum::IN_SECOND_CORRECTION, 'label' => "En deuxième correction", 'description' => "En deuxième correction"],
            ['code' => StatusEnum::FOR_CORRECTION, 'label' => "En correction", 'description' => "En correction"],
            ['code' => StatusEnum::PENDING_FOR_REPAIRER_VALIDATION, 'label' => "En attente de validation du dossier par le réparateur", 'description' => "En attente de validation du dossier par le réparateur"],
            ['code' => StatusEnum::PENDING_FOR_EXPERT_VALIDATION, 'label' => "En attente de validation du dossier par l'expert", 'description' => "En attente de validation du dossier par l'expert"],
            ['code' => StatusEnum::PENDING, 'label' => "En attente", 'description' => "En attente"],
            ['code' => StatusEnum::ACCEPTED, 'label' => "Accepté", 'description' => "Accepté"],
            ['code' => StatusEnum::REJECTED, 'label' => "Rejeté", 'description' => "Rejeté"],
            ['code' => StatusEnum::DRAFT, 'label' => "Brouillon", 'description' => "Brouillon"],
            ['code' => StatusEnum::IN_PROGRESS, 'label' => "En cours", 'description' => "En cours"],
        ];

        Status::upsert(
            $statuses,
            ['code'],
            ['label'],
            ['description'],
            []
        );
        
    }
}
