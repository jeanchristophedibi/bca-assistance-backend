<?php

namespace Database\Seeders\Demo;

use App\Enums\EntityTypeEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Models\AssignmentRequest;
use App\Models\AssignmentRequestRepairer;
use App\Models\Entity;
use App\Models\EntityType;
use App\Models\Photo;
use App\Models\User;
use Database\Seeders\Demo\Support\DemoContext;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Demandes d'assistance déposées par les clients depuis leur espace
 * (en attente, acceptées, rejetées, annulées), avec photos et garages souhaités.
 */
class DemoAssignmentRequestSeeder extends Seeder
{
    private const REQUESTS = [
        ['pending', 0, 'Pare-chocs avant enfoncé après un accrochage dans un embouteillage à Adjamé.', 'Abidjan Adjamé, gare routière'],
        ['pending', 1, 'Rétroviseur gauche arraché et portière rayée sur le parking du bureau.', 'Abidjan Plateau, parking de la Cité administrative'],
        ['pending', 1, 'Choc arrière par un gbaka au feu, feu arrière droit cassé.', 'Abidjan Yopougon, carrefour Siporex'],
        ['pending', 2, 'Pare-brise fissuré par une projection de pierre.', 'Autoroute du Nord, péage d\'Attinguié'],
        ['pending', 3, 'Aile avant droite déformée, phare ne s\'allume plus.', 'Abidjan Cocody, Riviera Palmeraie'],
        ['accepted', 6, 'Collision latérale à un carrefour, portières avant et arrière enfoncées.', 'Abidjan Marcory, Bd VGE'],
        ['accepted', 12, 'Capot et calandre endommagés après avoir heurté un muret.', 'Bingerville, entrée de la ville'],
        ['accepted', 20, 'Hayon enfoncé suite à une marche arrière d\'un tiers.', 'Abidjan Cocody, Angré 8e tranche'],
        ['rejected', 9, 'Rayures légères sur le pare-chocs arrière.', 'Abidjan Treichville, avenue 16'],
        ['rejected', 25, 'Usure des pneumatiques et freins à vérifier.', 'Abidjan Koumassi, Grand marché'],
        ['cancelled', 4, 'Accrochage avec un taxi, finalement réglé à l\'amiable.', 'Abidjan Port-Bouët, aéroport'],
        ['cancelled', 15, 'Choc sur le bas de caisse en sortant d\'un parking.', 'Grand-Bassam, quartier France'],
    ];

    public function run(): void
    {
        $clients = User::whereNotNull('client_id')
            ->where('email', 'like', '%@'.DemoContext::EMAIL_DOMAIN)
            ->orderBy('id')
            ->get();
        if ($clients->isEmpty()) {
            return;
        }

        $fleet = DemoClientVehicleSeeder::fleet();
        $repairers = Entity::where('entity_type_id', EntityType::where('code', EntityTypeEnum::REPAIRER)->value('id'))->pluck('id')->all();
        $staff = DemoContext::users(RoleEnum::OPENER, RoleEnum::ADMIN);
        $photos = File::glob(database_path('seeders/Demo/photos/*.jpg'));
        $used = AssignmentRequest::pluck('reference')->flip()->all();

        foreach (self::REQUESTS as $i => [$status, $ageDays, $observation, $place]) {
            $user = $clients[$i % $clients->count()];
            $vehicleId = DemoContext::pick($fleet[$user->client_id] ?? [null]);
            $createdAt = now()->subDays($ageDays)->subHours(DemoContext::int(1, 9))->subMinutes(DemoContext::int(0, 59));

            do {
                $reference = 'REQ'.$createdAt->format('Y_mdH_is');
                $createdAt->addSecond();
            } while (isset($used[$reference]));
            $used[$reference] = true;

            $attributes = [
                'reference' => $reference,
                'client_id' => $user->client_id,
                'vehicle_id' => $vehicleId,
                'date' => $createdAt->copy()->addDays(DemoContext::int(1, 3))->toDateString(),
                'assistance_place' => $place,
                'observation' => $observation,
                'status_id' => DemoContext::status($status),
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            $decidedAt = $createdAt->copy()->addHours(DemoContext::int(2, 30))->min(now());
            $decider = $staff->isNotEmpty() ? DemoContext::pick($staff->all()) : $user;

            $attributes += match ($status) {
                'rejected' => [
                    'rejected_by' => $decider->id,
                    'rejected_at' => $decidedAt,
                    'rejection_reason' => $i % 2 ? 'Entretien courant non couvert par une expertise.' : 'Dommages esthétiques mineurs : déclaration de sinistre non recevable.',
                    'updated_by' => $decider->id,
                    'updated_at' => $decidedAt,
                ],
                'cancelled' => [
                    'cancelled_by' => $user->id,
                    'cancelled_at' => $decidedAt,
                    'cancellation_reason' => $i % 2 ? 'Sinistre réglé à l\'amiable avec le tiers.' : 'Le client a fait réparer le véhicule par ses propres moyens.',
                    'updated_at' => $decidedAt,
                ],
                'accepted' => ['updated_by' => $decider->id, 'updated_at' => $decidedAt],
                default => [],
            };

            $request = AssignmentRequest::create($attributes);

            foreach (DemoContext::pickMany($repairers, DemoContext::int(1, 2)) as $repairerId) {
                AssignmentRequestRepairer::create([
                    'assignment_request_id' => $request->id,
                    'repairer_id' => $repairerId,
                    'status_id' => DemoContext::status(StatusEnum::ACTIVE),
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }

            // Même dossier de stockage que AssignmentRequestController::store.
            if ($photos) {
                $directory = public_path('storage/photos/assignment_request/'.$reference);
                File::ensureDirectoryExists($directory);
                foreach (range(1, DemoContext::int(1, 2)) as $position) {
                    $name = 'IMG_B'.substr($reference, 2).'_'.$position.'.jpg';
                    File::copy(DemoContext::pick($photos), $directory.'/'.$name);
                    Photo::create([
                        'name' => $name,
                        'position' => $position,
                        'assignment_request_id' => $request->id,
                        'status_id' => DemoContext::status(StatusEnum::ACTIVE),
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }
            }
        }
    }
}
