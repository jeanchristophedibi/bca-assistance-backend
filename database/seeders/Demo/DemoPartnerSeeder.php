<?php

namespace Database\Seeders\Demo;

use App\Enums\EntityTypeEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Models\Entity;
use App\Models\EntityEmail;
use App\Models\EntityType;
use App\Models\User;
use Database\Seeders\Demo\Support\DemoContext;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

/**
 * Réparateurs (garages partenaires), adresses de notification des compagnies
 * et comptes de démonstration pour les profils externes (assureur, réparateur).
 */
class DemoPartnerSeeder extends Seeder
{
    /** Garages fictifs : code => [nom, adresse, téléphone]. CFAO existe déjà. */
    public const REPAIRERS = [
        'DEMO_GAR_PLATEAU' => ['Garage Central du Plateau', 'Abidjan Plateau, Bd de la République', '+2252720214455'],
        'DEMO_GAR_MARCORY' => ['Auto Élite Marcory', 'Abidjan Marcory Zone 4, rue du Docteur Blanchard', '+2252721356677'],
        'DEMO_GAR_YOP' => ['Carrosserie Moderne de Yopougon', 'Abidjan Yopougon, zone industrielle', '+2252723458899'],
        'DEMO_GAR_COCODY' => ['Cocody Auto Services', 'Abidjan Cocody Riviera 3, carrefour Faya', '+2252722471122'],
        'DEMO_GAR_KOUMASSI' => ['Garage Lagunaire de Koumassi', 'Abidjan Koumassi, Bd du Gabon', '+2252721283344'],
        'DEMO_GAR_BOUAKE' => ['Garage du Centre Bouaké', 'Bouaké, quartier Commerce', '+2252731635566'],
        'DEMO_GAR_SANPEDRO' => ['San Pedro Mécanique Générale', 'San Pedro, zone portuaire', '+2252734717788'],
    ];

    public function run(): void
    {
        $active = DemoContext::status(StatusEnum::ACTIVE);
        $admin = User::orderBy('id')->value('id');
        $repairerType = EntityType::where('code', EntityTypeEnum::REPAIRER)->value('id');

        foreach (self::REPAIRERS as $code => [$name, $address, $phone]) {
            Entity::firstOrCreate(['code' => $code], [
                'name' => $name,
                'email' => 'contact.'.strtolower(str_replace('DEMO_GAR_', '', $code)).'@'.DemoContext::EMAIL_DOMAIN,
                'telephone' => $phone,
                'address' => $address,
                'entity_type_id' => $repairerType,
                'status_id' => $active,
                'created_by' => $admin,
                'updated_by' => $admin,
            ]);
        }

        // Adresses de notification « sinistres » des compagnies et courtiers.
        $partnerTypes = EntityType::whereIn('code', [EntityTypeEnum::INSURER, EntityTypeEnum::BROKER])->pluck('id');
        Entity::whereIn('entity_type_id', $partnerTypes)->get()->each(function (Entity $entity) use ($active, $admin) {
            // Requis par les factures B2B (CreateInvoiceRequest).
            if (! $entity->taxpayer_account_number) {
                $entity->update(['taxpayer_account_number' => sprintf('CC %07d %s', DemoContext::int(100000, 9999999), chr(DemoContext::int(65, 90)))]);
            }

            $slug = strtolower(str_replace('_', '-', $entity->code));
            foreach (['sinistres', 'gestion-auto'] as $mailbox) {
                EntityEmail::firstOrCreate(
                    ['entity_id' => $entity->id, 'email' => "{$mailbox}.{$slug}@".DemoContext::EMAIL_DOMAIN],
                    ['status_id' => $active, 'created_by' => $admin, 'updated_by' => $admin],
                );
            }
        });

        DemoContext::forget();

        // Comptes externes : un par profil pour pouvoir faire la démo de chaque parcours.
        $this->account('DEMO-ASS-01', 'Aïcha', 'KONÉ', 'nsia.admin', 'NSIA', RoleEnum::INSURER_ADMIN);
        $this->account('DEMO-ASS-02', 'Serge', 'N\'GUESSAN', 'nsia.gestionnaire', 'NSIA', RoleEnum::INSURER_STANDARD_USER);
        $this->account('DEMO-ASS-03', 'Mariam', 'DIABATÉ', 'axa.gestionnaire', 'AXA', RoleEnum::INSURER_STANDARD_USER);
        $this->account('DEMO-ASS-04', 'Hervé', 'ASSIÉ', 'ascoma.gestionnaire', 'ASCOMA', RoleEnum::INSURER_STANDARD_USER);
        $this->account('DEMO-REP-01', 'Jean-Marc', 'TANOH', 'cfao.admin', 'CFAO', RoleEnum::REPAIRER_ADMIN);
        $this->account('DEMO-REP-02', 'Ibrahim', 'OUATTARA', 'plateau.atelier', 'DEMO_GAR_PLATEAU', RoleEnum::REPAIRER_ADMIN);
        $this->account('DEMO-REP-03', 'Rose', 'AKA', 'marcory.atelier', 'DEMO_GAR_MARCORY', RoleEnum::REPAIRER_STANDARD_USER);
    }

    /**
     * Les comptes clients n'ont pas d'entité (cf. AuthController::register) : passer `$entityCode = null`.
     */
    public static function account(string $code, string $firstName, string $lastName, string $mailbox, ?string $entityCode, RoleEnum $role, ?int $clientId = null): ?User
    {
        $entity = $entityCode ? DemoContext::entity($entityCode) : null;
        if ($entityCode && ! $entity) {
            return null;
        }

        $email = "{$mailbox}@".DemoContext::EMAIL_DOMAIN;
        $roleModel = DemoContext::role($role);

        $user = User::firstOrCreate(['email' => $email], [
            'username' => $code,
            'code' => $code,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'telephone' => DemoContext::phone(),
            'password' => DemoContext::PASSWORD,
            'entity_id' => $entity?->id,
            'client_id' => $clientId,
            'current_role_id' => $roleModel->id,
            'status_id' => DemoContext::status(StatusEnum::ACTIVE),
        ]);

        // Même logique que UserSeeder : les rôles sont rattachés à l'équipe = rôle courant.
        app(PermissionRegistrar::class)->setPermissionsTeamId($roleModel->id);
        if (! $user->hasRole($roleModel->name)) {
            $user->assignRole($roleModel->name);
        }

        return $user;
    }
}
