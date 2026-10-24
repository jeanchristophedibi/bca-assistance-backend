<?php

namespace Database\Seeders\Demo;

use App\Enums\AssignmentTypeEnum;
use App\Enums\EntityTypeEnum;
use App\Enums\ExpertiseTypeEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\PhotoTypeEnum;
use App\Enums\ReceiptTypeEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Enums\WorkforceTypeEnum;
use App\Models\Ascertainment;
use App\Models\AscertainmentType;
use App\Models\Assignment;
use App\Models\AssignmentExpert;
use App\Models\AssignmentMessage;
use App\Models\AssignmentRepairer;
use App\Models\AssignmentType;
use App\Models\Bank;
use App\Models\Check;
use App\Models\ClaimNature;
use App\Models\Client;
use App\Models\Comparison;
use App\Models\DocumentTransmitted;
use App\Models\Entity;
use App\Models\EntityType;
use App\Models\ExpertiseType;
use App\Models\GeneralState;
use App\Models\HourlyRate;
use App\Models\Invoice;
use App\Models\Offer;
use App\Models\OfferShock;
use App\Models\OfferShockWork;
use App\Models\OfferWorkforce;
use App\Models\OtherCost;
use App\Models\OtherCostType;
use App\Models\PaintType;
use App\Models\Payment;
use App\Models\PaymentHistoric;
use App\Models\PaymentMethod;
use App\Models\PaymentType;
use App\Models\Photo;
use App\Models\PhotoType;
use App\Models\Receipt;
use App\Models\ReceiptType;
use App\Models\Remark;
use App\Models\Shock;
use App\Models\ShockPoint;
use App\Models\ShockWork;
use App\Models\Supply;
use App\Models\TechnicalConclusion;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\WorkFee;
use App\Models\Workforce;
use App\Models\WorkforceType;
use App\Services\MarketValue\MarketValueService;
use App\Services\WorkDuration\WorkDurationService;
use Carbon\Carbon;
use Database\Seeders\Demo\Support\DemoContext;
use Database\Seeders\Demo\Support\DemoQuoteCalculator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Dossiers d'expertise répartis sur les 12 derniers mois et couvrant chaque
 * statut du parcours (réception → clôture), avec les données attendues à chaque étape :
 * experts, réparateurs, chocs/fournitures/main d'œuvre, constats, photos, honoraires,
 * factures, paiements, messages et mises en concurrence (comparaisons / offres).
 */
class DemoAssignmentSeeder extends Seeder
{
    /**
     * statut => [nombre de dossiers, ancienneté min (jours), ancienneté max (jours)].
     * Les dossiers récents sont en début de parcours, les plus anciens sont clôturés.
     */
    private const PLAN = [
        'pending_for_opening' => [6, 0, 3],
        'opened' => [7, 1, 10],
        'realized' => [4, 4, 14],
        'pending_for_repairer_quote' => [4, 5, 18],
        'pending_for_repairer_quote_validation' => [3, 7, 20],
        'in_editing' => [5, 9, 25],
        'for_correction' => [2, 12, 30],
        'in_first_correction' => [3, 12, 30],
        'in_second_correction' => [2, 14, 35],
        'pending_for_repairer_validation' => [2, 15, 35],
        'pending_for_expert_validation' => [2, 15, 35],
        'validated' => [7, 18, 75],
        'in_payment' => [4, 35, 100],
        'paid' => [12, 45, 250],
        'closed' => [16, 70, 360],
        'cancelled' => [4, 10, 220],
    ];

    /** Jalons du parcours et durée typique (jours) depuis le jalon précédent. */
    private const MILESTONES = [
        'created' => 0,
        'opened' => 1,
        'realized' => 3,
        'repairer_quote' => 3,
        'quote_validated' => 2,
        'edited' => 4,
        'first_correction' => 2,
        'second_correction' => 2,
        'invoiced' => 2,
        'paid' => 30,
        'closed' => 7,
    ];

    /** Nombre de jalons franchis pour chaque statut. */
    private const REACHED = [
        'pending_for_opening' => 1,
        'opened' => 2,
        'realized' => 3,
        'pending_for_repairer_quote' => 3,
        'pending_for_repairer_quote_validation' => 4,
        'in_editing' => 5,
        'for_correction' => 6,
        'in_first_correction' => 6,
        'in_second_correction' => 7,
        'pending_for_repairer_validation' => 8,
        'pending_for_expert_validation' => 8,
        'validated' => 8,
        'in_payment' => 9,
        'paid' => 10,
        'closed' => 11,
    ];

    /** Préfixes de référence par compagnie / courtier (cf. AssignmentController::store). */
    private const PREFIXES = [
        'NSIA' => 'G', 'AXA' => 'B', 'SANLAM_ALLIANZ' => 'K', 'GNA' => 'D', 'ATLANTIQUE' => 'E',
        'COMAR' => 'C', 'AFG' => 'AF', 'ATLANTA' => 'A', 'ACTIVA' => 'L', 'SIDAM' => 'F',
        'ASCOMA' => 'J', 'OLEA' => 'Q', 'WTW' => 'N',
    ];

    private const TYPE_PREFIXES = ['particular' => 'PC', 'taxi' => 'TA', 'evaluation' => 'EV'];

    private const INSURER_WEIGHTS = [
        'NSIA' => 22, 'AXA' => 18, 'SANLAM_ALLIANZ' => 14, 'ATLANTIQUE' => 10, 'GNA' => 8,
        'COMAR' => 7, 'ACTIVA' => 6, 'ATLANTA' => 5, 'AFG' => 5, 'SIDAM' => 5,
    ];

    private const CIRCUMSTANCES = [
        'accident_collision' => [
            ['front', 'Le véhicule assuré a percuté l\'arrière d\'un minicar (gbaka) qui a freiné brusquement sur le boulevard Valéry Giscard d\'Estaing.'],
            ['front', 'Collision frontale légère au carrefour de la Riviera 2 : le tiers n\'a pas respecté le feu rouge.'],
            ['rear', 'Le véhicule, à l\'arrêt au feu de l\'échangeur de la Riviera 3, a été heurté par l\'arrière par un camion de livraison.'],
            ['rear', 'Choc arrière lors d\'une manœuvre de stationnement d\'un tiers sur le parking d\'un centre commercial à Marcory.'],
            ['side', 'Accrochage latéral avec un taxi woro-woro qui a changé de file sans avertir sur l\'autoroute du Nord.'],
            ['side', 'Le tiers, sortant d\'une voie secondaire à Yopougon, a refusé la priorité et a percuté le flanc du véhicule.'],
            ['front', 'Sortie de route sous une forte pluie sur la Côtière, le véhicule a heurté un caniveau puis un poteau.'],
        ],
        'ice_break' => [
            ['roof', 'Projection de gravillons par un camion benne sur l\'autoroute de Bassam : pare-brise étoilé puis fissuré.'],
        ],
        'vandalism' => [
            ['side', 'Véhicule rayé et rétroviseurs arrachés pendant la nuit, stationné devant le domicile de l\'assuré.'],
        ],
        'fire' => [
            ['front', 'Départ de feu dans le compartiment moteur suite à un court-circuit, maîtrisé par les sapeurs-pompiers.'],
        ],
        'vehicle_stolen_found' => [
            ['side', 'Véhicule volé puis retrouvé deux jours plus tard à Abobo, portière forcée et faisceau électrique endommagé.'],
        ],
        'attempted_theft' => [
            ['side', 'Tentative d\'effraction : serrure de portière forcée et vitre latérale brisée sur un parking à Cocody.'],
        ],
        'flooding' => [
            ['front', 'Véhicule immergé jusqu\'au niveau du tableau de bord lors des fortes pluies de juin à Cocody Angré.'],
        ],
    ];

    private const MESSAGES = [
        'Expertise programmée avec le réparateur, l\'assuré a confirmé sa présence.',
        'Merci de transmettre la carte grise et le constat amiable avant la rédaction.',
        'Le réparateur signale une pièce supplémentaire découverte au démontage.',
        'Devis réparateur reçu, analyse en cours par l\'expert.',
        'Relance du réparateur pour le devis définitif.',
        'Photos complémentaires ajoutées après démontage.',
        'Rapport relu : merci de préciser la vétusté appliquée sur le pare-chocs.',
        'La compagnie demande un délai d\'immobilisation précis.',
        'Facture transmise à la compagnie pour règlement.',
        'Règlement reçu par virement, dossier à clôturer.',
    ];

    private const OBSERVATION_PLACES = [
        'Commissariat du 8e arrondissement, Cocody', 'Commissariat du 16e arrondissement, Yopougon',
        'Brigade de gendarmerie de Bingerville', 'Commissariat du 9e arrondissement, Marcory',
        'Constat amiable entre les parties', 'Commissariat de police de Port-Bouët',
    ];

    /** Pièces « peignables » exclues (optique, vitrage, mécanique, pneumatiques, sécurité). */
    private const NOT_PAINTABLE = ['S016', 'S017', 'S018', 'S019', 'S022', 'S023', 'S025', 'S026', 'S031', 'S032', 'S033', 'S034', 'S035'];

    private array $ref = [];

    private array $references = [];

    /** Spécifications de chocs par dossier, réutilisées pour les offres des comparaisons. */
    private array $shockSpecs = [];

    private ?string $photoSource = null;

    public function run(): void
    {
        $this->loadReferences();

        foreach (self::PLAN as $status => [$count, $minAge, $maxAge]) {
            for ($i = 0; $i < $count; $i++) {
                $age = DemoContext::int($minAge, $maxAge);
                $this->seedAssignment($status, $age, $i);
            }
        }

        $this->seedComparisons();
    }

    /* =====================================================================
     *  Un dossier
     * ===================================================================== */

    private function seedAssignment(string $status, int $ageDays, int $index): void
    {
        $cancelled = $status === 'cancelled';
        $reached = $cancelled ? DemoContext::int(1, 3) : self::REACHED[$status];
        $timeline = $this->timeline($ageDays, $reached);

        $type = DemoContext::weighted(['insurer' => 74, 'particular' => 12, 'taxi' => 8, 'evaluation' => 6]);
        [$client, $vehicle] = $this->clientAndVehicle($type);
        $insurer = $type === 'insurer' ? $this->ref['entities'][DemoContext::weighted(self::INSURER_WEIGHTS)] : null;
        $broker = $insurer && DemoContext::chance(18) ? DemoContext::pick($this->ref['brokers']) : null;
        $repairer = $type === 'evaluation' ? null : DemoContext::pick($this->ref['repairers']);
        $natureCode = $type === 'evaluation' ? null : DemoContext::weighted([
            'accident_collision' => 72, 'ice_break' => 8, 'vandalism' => 5, 'fire' => 3,
            'vehicle_stolen_found' => 4, 'attempted_theft' => 4, 'flooding' => 4,
        ]);
        [$zone, $circumstance] = $natureCode ? DemoContext::pick(self::CIRCUMSTANCES[$natureCode]) : ['front', null];

        $expertiseTypeCode = match (true) {
            $type === 'evaluation' => ExpertiseTypeEnum::EVALUATION->value,
            $type === 'insurer' => DemoContext::weighted(['standard' => 88, 'against_expertise' => 6, 'judicial' => 6]),
            default => ExpertiseTypeEnum::STANDARD->value,
        };

        $created = $timeline['created'];
        $claimDate = $created->copy()->subDays(DemoContext::int(1, 9));
        $expertiseDate = $reached >= 3
            ? $timeline['realized']->copy()
            : $created->copy()->addDays(DemoContext::int(2, 6));
        $expert = DemoContext::user(RoleEnum::EXPERT);
        $opener = DemoContext::user(RoleEnum::OPENER);
        $expertisePlace = $repairer ? $repairer->name.' – '.($repairer->address ?? 'Abidjan') : ($client->address ?? 'Abidjan');

        $attributes = [
            'reference' => $this->timestampedReference('D', $created),
            'policy_number' => $insurer ? sprintf('%s-AUTO-%s-%06d', substr($insurer->code, 0, 4), $claimDate->format('y'), DemoContext::int(1000, 999999)) : null,
            'claim_number' => $insurer ? sprintf('SIN-%s-%05d', $claimDate->format('y'), DemoContext::int(10000, 99999)) : null,
            'claim_starts_at' => $claimDate->toDateString(),
            'expertise_date' => $expertiseDate->toDateString(),
            'seen_before_work_date' => $expertiseDate->toDateString(),
            'expertise_place' => $expertisePlace,
            'received_at' => $created->toDateString(),
            'circumstance' => $circumstance,
            'damage_declared' => $natureCode ? $this->damageDeclared($zone) : null,
            'document_transmitted_id' => json_encode(DemoContext::pickMany($this->ref['documents'], DemoContext::int(1, count($this->ref['documents'])))),
            'assured_value' => $insurer && DemoContext::chance(40) ? DemoContext::price((int) ($vehicle->new_market_value * 0.5), (int) $vehicle->new_market_value, 100000) : null,
            'vehicle_mileage' => $vehicle->mileage,
            'insurer_id' => $insurer?->id,
            'broker_id' => $broker?->id,
            'client_id' => $client->id,
            'vehicle_id' => $vehicle->id,
            'assignment_type_id' => $this->ref['assignment_types'][$type],
            'expertise_type_id' => $this->ref['expertise_types'][$expertiseTypeCode],
            'claim_nature_id' => $natureCode ? $this->ref['claim_natures'][$natureCode] : null,
            'status_id' => DemoContext::status($cancelled ? StatusEnum::CANCELLED : $status),
            'created_by' => $opener->id,
            'updated_by' => $opener->id,
            'reference_updated_by' => $opener->id,
            'reference_updated_at' => $created,
            'created_at' => $created,
            'updated_at' => $timeline['last'],
        ];

        if ($insurer && $natureCode === 'accident_collision') {
            $attributes += [
                'observation_realizer_name' => 'Agent '.DemoContext::pick(['KOUAMÉ', 'GOHOU', 'SÉRI', 'TIA', 'BOLI']),
                'observation_realizer_phone' => DemoContext::phone(),
                'observation_place' => DemoContext::pick(self::OBSERVATION_PLACES),
                'observation_date' => $claimDate->toDateString(),
            ];
        }

        $assignment = Assignment::create($attributes);
        $this->attachExperts($assignment, $expert, $expertiseDate);
        if ($repairer) {
            AssignmentRepairer::create($this->audit([
                'assignment_id' => $assignment->id,
                'repairer_id' => $repairer->id,
            ], $opener, $created));
        }

        $update = [];
        $shockTotals = null;

        // 2. Ouverture : la vraie référence est attribuée.
        if ($reached >= 2) {
            $update += [
                'reference' => $this->openedReference($type, $insurer, $timeline['opened']),
                'reference_updated_at' => $timeline['opened'],
                'opened_by' => $opener->id,
                'opened_at' => $timeline['opened'],
            ];
        }

        // 3. Expertise réalisée : chocs chiffrés, constats, photos.
        if ($reached >= 3) {
            $realizer = $expert;
            $specs = $this->shockSpecs($vehicle, $natureCode, $zone, $type !== 'evaluation', $assignment->id);
            $shockTotals = $this->writeShocks($assignment, $specs, $realizer, $timeline['realized'], $type === 'evaluation', $reached);

            $update += [
                'directed_by' => DemoContext::user(RoleEnum::EXPERT_MANAGER, RoleEnum::EXPERT)->id,
                'realized_by' => $realizer->id,
                'realized_at' => $timeline['realized'],
                'work_sheet_established_by' => $realizer->id,
                'work_sheet_established_at' => $timeline['realized'],
                'point_noted' => $this->pointNoted($zone),
                'expert_work_sheet_remark' => 'Dommages constatés en présence du réparateur, cohérents avec les circonstances déclarées.',
                'shock_amount_excluding_tax' => $shockTotals['ht'],
                'shock_amount_tax' => $shockTotals['tax'],
                'shock_amount' => $shockTotals['ttc'],
            ];

            $this->writeAscertainments($assignment, $zone, $realizer, $timeline['realized']);
            $photoCount = $this->writePhotos($assignment->refresh()->forceFill($update), PhotoTypeEnum::BEFORE, DemoContext::int(2, 4), $realizer, $timeline['realized']);
        }

        // 4. Devis transmis par le réparateur.
        if ($reached >= 4) {
            $repairerUser = $repairer ? $this->repairerUser($repairer) : null;
            $update += [
                'quote_validated_by_repairer' => true,
                'quote_validated_by_repairer_by' => $repairerUser?->id,
                'quote_validated_by_repairer_at' => $timeline['repairer_quote'],
            ];
        }

        // 5. Devis validé par l'expert → rédaction.
        if ($reached >= 5) {
            $conditions = DemoContext::chance(15);
            $update += [
                'quote_validated' => true,
                'quote_validated_by_expert' => true,
                'quote_validated_by_expert_by' => $expert->id,
                'quote_validated_by_expert_at' => $timeline['quote_validated'],
                'agreement_for_work_subject_to_conditions' => $conditions,
                'agreement_for_work_subject_to_conditions_by' => $conditions ? $expert->id : null,
                'agreement_for_work_subject_to_conditions_at' => $conditions ? $timeline['quote_validated'] : null,
                'conditions' => $conditions ? 'Accord sous réserve de présentation des pièces remplacées lors de la visite après travaux.' : null,
            ];
            $update += $this->editionData($assignment, $vehicle, $client, $type, $shockTotals, $timeline['quote_validated'], $photoCount ?? 0);
        }

        // 6. Rédaction terminée (validée, ou renvoyée en correction).
        if ($reached >= 6) {
            $editor = DemoContext::user(RoleEnum::EDITOR, RoleEnum::EDITOR_MANAGER, RoleEnum::EXPERT_MANAGER);
            $reviewer = DemoContext::user(RoleEnum::VALIDATOR, RoleEnum::EXPERT_MANAGER);
            $update += ['edited_by' => $editor->id, 'edited_at' => $timeline['edited']];

            if ($status === 'for_correction') {
                $update += [
                    'edition_validated' => false,
                    'edition_unvalidated_by' => $reviewer->id,
                    'edition_unvalidated_at' => $timeline['edited'],
                    'edition_unvalidation_reason' => DemoContext::pick([
                        'Taux de vétusté à justifier sur les pièces remplacées.',
                        'Photos du compteur kilométrique manquantes.',
                        'Montant de la main d\'œuvre peinture à revoir.',
                    ]),
                ];
            } else {
                $update += [
                    'edition_validated' => true,
                    'edition_validated_by' => $reviewer->id,
                    'edition_validated_at' => $timeline['edited'],
                ];
            }
        }

        // 7. Première correction validée.
        if ($reached >= 7) {
            $update += [
                'first_correction_validated' => true,
                'first_correction_validated_by' => DemoContext::user(RoleEnum::VALIDATOR, RoleEnum::EXPERT_MANAGER)->id,
                'first_correction_validated_at' => $timeline['first_correction'],
            ];
        }

        // 8. Deuxième correction / validations réparateur & expert.
        if ($reached >= 8) {
            $validator = DemoContext::user(RoleEnum::VALIDATOR, RoleEnum::CEO);
            if (in_array($status, ['pending_for_repairer_validation', 'pending_for_expert_validation'], true)) {
                $update += ['required_for_repairer_validation' => true];
                if ($status === 'pending_for_expert_validation') {
                    $update += [
                        'is_validated_by_repairer' => true,
                        'validated_by_repairer_by' => $repairer ? $this->repairerUser($repairer)?->id : null,
                        'validated_by_repairer_at' => $timeline['second_correction'],
                    ];
                }
            } else {
                $update += [
                    'second_correction_validated' => true,
                    'second_correction_validated_by' => $validator->id,
                    'second_correction_validated_at' => $timeline['second_correction'],
                    'is_validated' => true,
                    'is_validated_by_expert' => true,
                    'validated_by_expert_by' => $expert->id,
                    'validated_by_expert_at' => $timeline['second_correction'],
                    'validated_by' => $validator->id,
                    'validated_at' => $timeline['second_correction'],
                    'generated_by' => $validator->id,
                    'generated_at' => $timeline['second_correction'],
                    'printed_at' => $timeline['second_correction'],
                    'seen_after_work_date' => $timeline['second_correction']->toDateString(),
                ];
                // Shocks validés avec le rapport
                Shock::where('assignment_id', $assignment->id)->update(['is_validated' => true]);
            }
        }

        // 9-11. Facturation, règlement, clôture.
        if ($reached >= 9) {
            $this->writeInvoice($assignment, $client, $insurer, $timeline['invoiced'], $status === 'closed' && $index === 0);
            $receiptAmount = (float) ($update['receipt_amount'] ?? 0);
            $this->writePayments($assignment, $type, $receiptAmount, $timeline, $status === 'in_payment');
        }

        if ($reached >= 10) {
            $update += ['paid_by' => DemoContext::user(RoleEnum::ACCOUNTANT_MANAGER, RoleEnum::ACCOUNTANT)->id, 'paid_at' => $timeline['paid']];
        }

        if ($reached >= 11) {
            $this->writePhotos($assignment->forceFill($update), PhotoTypeEnum::AFTER, DemoContext::int(1, 2), $expert, $timeline['closed']);
            $update += [
                'closed_by' => DemoContext::user(RoleEnum::ACCOUNTANT_MANAGER, RoleEnum::ADMIN)->id,
                'closed_at' => $timeline['closed'],
            ];
        }

        if ($cancelled) {
            $update += [
                'cancelled_by' => DemoContext::user(RoleEnum::ADMIN, RoleEnum::EXPERT_MANAGER)->id,
                'cancelled_at' => $timeline['last'],
                'cancellation_reason' => DemoContext::pick([
                    'Désistement de l\'assuré : règlement à l\'amiable avec le tiers.',
                    'Doublon d\'un dossier déjà ouvert pour le même sinistre.',
                    'Mission retirée par la compagnie (garantie non acquise).',
                    'Véhicule réparé avant l\'expertise, constat impossible.',
                ]),
            ];
        }

        $this->writeMessages($assignment, $timeline, $reached);

        $assignment->forceFill($update);
        $assignment->timestamps = false;
        $assignment->save();
    }

    /* =====================================================================
     *  Chronologie
     * ===================================================================== */

    /**
     * Date de chaque jalon franchi, mise à l'échelle pour que le dernier tombe
     * dans l'intervalle [réception, maintenant].
     */
    private function timeline(int $ageDays, int $reached): array
    {
        $now = now();
        $created = $now->copy()->subDays($ageDays)->setTime(DemoContext::int(7, 11), DemoContext::int(0, 59), DemoContext::int(0, 59));
        if ($created->greaterThan($now)) {
            $created = $now->copy()->subHours(DemoContext::int(1, 4));
        }

        $keys = array_slice(array_keys(self::MILESTONES), 0, $reached);
        $durations = array_map(fn ($key) => self::MILESTONES[$key] * (0.6 + DemoContext::int(0, 80) / 100), $keys);
        $total = array_sum($durations);
        $available = max(0.1, $created->diffInHours($now) / 24 - 0.05);
        $scale = $total > $available ? $available / $total : 1;

        $timeline = [];
        $cursor = $created->copy();
        foreach ($keys as $i => $key) {
            $cursor = $cursor->copy()->addMinutes((int) round($durations[$i] * $scale * 1440));
            if ($key !== 'created' && $cursor->hour < 8) {
                $cursor->setTime(DemoContext::int(8, 17), DemoContext::int(0, 59));
                if ($cursor->greaterThan($now)) {
                    $cursor = $now->copy()->subMinutes(DemoContext::int(5, 50));
                }
            }
            $timeline[$key] = $key === 'created' ? $created : $cursor;
        }
        $timeline['last'] = end($timeline);

        return $timeline;
    }

    /* =====================================================================
     *  Références
     * ===================================================================== */

    /**
     * Référence horodatée comme dans les contrôleurs ({préfixe}AAAA_MMJJHH_mmss), rendue unique à la seconde près.
     */
    private function timestampedReference(string $prefix, Carbon $at): string
    {
        $at = $at->copy();
        do {
            $reference = $prefix.$at->format('Y_mdH_is');
            $at->addSecond();
        } while (isset($this->references[$reference]));

        return $this->references[$reference] = $reference;
    }

    /**
     * Format métier : {préfixe}{numéro sur 5 chiffres}-{mm}-{aa}-BCA, numérotation par préfixe et par mois.
     */
    private function openedReference(string $type, ?Entity $insurer, Carbon $date): string
    {
        $prefix = $type === 'insurer' ? (self::PREFIXES[$insurer->code] ?? 'X') : self::TYPE_PREFIXES[$type];
        $suffix = $date->format('m').'-'.$date->format('y').'-BCA';

        $number = 1;
        foreach (array_keys($this->references) as $reference) {
            if (preg_match('/^'.$prefix.'(\d+)-'.preg_quote($suffix, '/').'$/', $reference, $matches)) {
                $number = max($number, (int) $matches[1] + 1);
            }
        }

        $reference = $prefix.str_pad((string) $number, 5, '0', STR_PAD_LEFT).'-'.$suffix;

        return $this->references[$reference] = $reference;
    }

    /* =====================================================================
     *  Intervenants
     * ===================================================================== */

    private function clientAndVehicle(string $type): array
    {
        $fleet = DemoClientVehicleSeeder::fleet();
        $taxiOwners = DemoClientVehicleSeeder::taxiOwners();

        $candidates = match ($type) {
            'taxi' => $taxiOwners,
            'particular' => array_values(array_filter(array_keys($fleet), fn ($id) => ! in_array($id, $taxiOwners, true) && ! in_array($id, $this->ref['company_clients'], true))),
            default => array_values(array_filter(array_keys($fleet), fn ($id) => ! in_array($id, $taxiOwners, true))),
        };
        $candidates = $candidates ?: array_keys($fleet);

        $clientId = DemoContext::pick($candidates);
        $client = $this->ref['clients'][$clientId] ??= Client::find($clientId);
        $vehicleId = DemoContext::pick($fleet[$clientId]);
        $vehicle = $this->ref['vehicles'][$vehicleId] ??= Vehicle::with('vehicleGenre', 'vehicleEnergy')->find($vehicleId);

        return [$client, $vehicle];
    }

    private function attachExperts(Assignment $assignment, User $expert, Carbon $date): void
    {
        $experts = [$expert];
        if (DemoContext::chance(15)) {
            $second = DemoContext::user(RoleEnum::EXPERT);
            if ($second->id !== $expert->id) {
                $experts[] = $second;
            }
        }

        foreach ($experts as $i => $user) {
            AssignmentExpert::create($this->audit([
                'assignment_id' => $assignment->id,
                'expert_id' => $user->id,
                'date' => $date->toDateString(),
                'observation' => $i === 0 ? 'Expert principal' : 'Expert en appui (contre-visite)',
            ], User::find($assignment->created_by), $assignment->created_at));
        }
    }

    private function repairerUser(Entity $repairer): ?User
    {
        return $this->ref['repairer_users'][$repairer->id] ??= User::where('entity_id', $repairer->id)->orderBy('id')->first();
    }

    /* =====================================================================
     *  Chocs, fournitures, main d'œuvre
     * ===================================================================== */

    private function shockSpecs(Vehicle $vehicle, ?string $natureCode, string $zone, bool $withTax, int $assignmentId): array
    {
        $vehicleAgeMonths = $vehicle->first_entry_into_circulation_date
            ? (int) Carbon::parse($vehicle->first_entry_into_circulation_date)->diffInMonths(now())
            : 36;

        $shockCount = match ($natureCode) {
            'ice_break', 'attempted_theft' => 1,
            'fire', 'flooding' => DemoContext::int(2, 3),
            default => (int) DemoContext::weighted(['1' => 50, '2' => 35, '3' => 15]),
        };

        $pointsByZone = collect(DemoReferentialSeeder::SHOCK_POINTS)->groupBy(fn ($point) => $point[1], true)->map->keys();
        $zones = array_merge([$zone], DemoContext::pickMany(array_values(array_diff(['front', 'rear', 'side'], [$zone])), $shockCount - 1));

        $specs = [];
        foreach ($zones as $shockZone) {
            $pointCode = DemoContext::pick($pointsByZone[$shockZone]->all());
            $candidates = array_keys(array_filter(DemoReferentialSeeder::SUPPLIES, fn ($supply) => in_array($shockZone, $supply[1], true)));
            $chosen = $natureCode === 'ice_break'
                ? ['S022']
                : DemoContext::pickMany($candidates, DemoContext::int(2, min(5, count($candidates))));

            $works = [];
            $hasPaint = $hasElectric = $hasSaddlery = false;
            foreach ($chosen as $code) {
                [, , $min, $max] = DemoReferentialSeeder::SUPPLIES[$code];
                $replacement = DemoContext::chance(in_array($code, self::NOT_PAINTABLE, true) ? 90 : 55);
                $paint = ! in_array($code, self::NOT_PAINTABLE, true) && DemoContext::chance(85);
                $obsolescence = $replacement && $vehicleAgeMonths > 36;

                $works[] = [
                    'supply_id' => $this->ref['supplies'][$code],
                    'amount' => $replacement ? DemoContext::price($min, $max) : 0,
                    'disassembly' => DemoContext::chance(70),
                    'replacement' => $replacement,
                    'repair' => ! $replacement,
                    'paint' => $paint,
                    'obsolescence' => $obsolescence,
                    'control' => DemoContext::chance(20),
                    'in_order' => DemoContext::chance(8),
                    'obsolescence_rate' => $obsolescence ? min(40, 5 * intdiv($vehicleAgeMonths, 12)) : 0,
                    'discount' => DemoContext::chance(20) ? DemoContext::pick([5, 10]) : 0,
                    'recovery_amount' => 0,
                    'comment' => $replacement ? null : DemoContext::pick(['Redressage et mastic', 'Réparation par débosselage', null]),
                ];

                $hasPaint = $hasPaint || $paint;
                $hasElectric = $hasElectric || in_array($code, ['S016', 'S017', 'S018', 'S019', 'S025', 'S026'], true);
                $hasSaddlery = $hasSaddlery || in_array($code, ['S033', 'S034'], true);
            }

            $workforces = [['workforce_type_id' => $this->ref['workforce_types']['metalworking'], 'nb_hours' => DemoContext::int(2, 9), 'discount' => 0, 'all_paint' => false]];
            if ($hasPaint) {
                $workforces[] = ['workforce_type_id' => $this->ref['workforce_types']['paint'], 'nb_hours' => DemoContext::int(2, 7), 'discount' => DemoContext::chance(10) ? 5 : 0, 'all_paint' => false];
            }
            if ($hasElectric || in_array($natureCode, ['fire', 'flooding', 'vehicle_stolen_found'], true)) {
                $workforces[] = ['workforce_type_id' => $this->ref['workforce_types']['electricity'], 'nb_hours' => DemoContext::int(1, 4), 'discount' => 0, 'all_paint' => false];
            }
            if ($hasSaddlery || $natureCode === 'flooding') {
                $workforces[] = ['workforce_type_id' => $this->ref['workforce_types']['saddlery'], 'nb_hours' => DemoContext::int(1, 5), 'discount' => 0, 'all_paint' => false];
            }

            $specs[] = [
                'shock_point_id' => $this->ref['shock_points'][$pointCode],
                'paint_type_id' => $this->ref['paint_types'][DemoContext::weighted(['ordinary' => 40, 'acrylic' => 25, 'vernissed' => 25, 'nacred' => 10])],
                'hourly_rate_id' => DemoContext::pick($this->ref['hourly_rates']),
                'with_tax' => $withTax,
                'works' => $works,
                'workforces' => $workforces,
            ];
        }

        return $this->shockSpecs[$assignmentId] = $specs;
    }

    /**
     * Enregistre les chocs et retourne les totaux du dossier.
     */
    private function writeShocks(Assignment $assignment, array $specs, User $by, Carbon $at, bool $evaluation, int $reached): array
    {
        $calculator = new DemoQuoteCalculator($evaluation);
        $quoteValidated = $reached >= 5;
        $totals = ['ht' => 0, 'tax' => 0, 'ttc' => 0, 'hours' => 0];

        foreach ($specs as $position => $spec) {
            $result = $calculator->shock($spec);

            $shock = Shock::create($this->audit($result['attributes'] + [
                'assignment_id' => $assignment->id,
                'position' => $position + 1,
                'quote_validated' => $quoteValidated,
            ], $by, $at));

            foreach ($result['works'] as $work) {
                ShockWork::create($this->audit($work + ['shock_id' => $shock->id, 'quote_validated' => $quoteValidated], $by, $at));
            }

            foreach ($result['workforces'] as $workforce) {
                Workforce::create($this->audit($workforce + ['shock_id' => $shock->id, 'quote_validated' => $quoteValidated], $by, $at));
            }

            $totals['ht'] += $result['attributes']['amount_excluding_tax'];
            $totals['tax'] += $result['attributes']['amount_tax'];
            $totals['ttc'] += $result['attributes']['amount'];
            $totals['hours'] += $result['nb_hours'];
        }

        return $totals;
    }

    /* =====================================================================
     *  Rédaction : valeurs, conclusion, frais annexes, honoraires
     * ===================================================================== */

    private function editionData(Assignment $assignment, Vehicle $vehicle, Client $client, string $type, array $shockTotals, Carbon $at, int $photoCount): array
    {
        $editor = DemoContext::user(RoleEnum::EDITOR, RoleEnum::EXPERT);

        // Frais annexes (remorquage / dépannage) sur une partie des dossiers.
        $other = ['ht' => 0, 'tax' => 0, 'ttc' => 0];
        if ($type !== 'evaluation' && DemoContext::chance(22)) {
            $amount = DemoContext::price(20000, 75000, 5000);
            OtherCost::create($this->audit([
                'assignment_id' => $assignment->id,
                'other_cost_type_id' => DemoContext::pick($this->ref['other_cost_types']),
                'amount_excluding_tax' => $amount,
                'amount_tax' => 0,
                'amount' => $amount,
            ], $editor, $at));
            $other = ['ht' => $amount, 'tax' => 0, 'ttc' => $amount];
        }

        $totalTtc = $shockTotals['ttc'] + $other['ttc'];
        [$marketValue, $depreciationRate] = $this->marketValue($vehicle, $at);

        $conclusion = $totalTtc > $marketValue * 0.8 ? 'TC003' : DemoContext::weighted(['TC001' => 94, 'TC003' => 4, 'TC002' => 2]);
        $repairable = $conclusion === 'TC001';
        $salvage = $repairable ? null : ceil($marketValue * DemoContext::int(12, 25) / 100 / 1000) * 1000;
        $ageMonths = $vehicle->first_entry_into_circulation_date ? Carbon::parse($vehicle->first_entry_into_circulation_date)->diffInMonths($at) : 60;

        $remark = match ($conclusion) {
            'TC003' => $this->ref['remarks'][5] ?? null,
            'TC002' => $this->ref['remarks'][7] ?? null,
            default => $ageMonths > 60 ? ($this->ref['remarks'][4] ?? null) : ($this->ref['remarks'][1] ?? null),
        };

        $receipts = $this->writeReceipts($assignment, $client, $type, $repairable ? $totalTtc : $marketValue, $editor, $at, $photoCount);

        return [
            'technical_conclusion_id' => $this->ref['technical_conclusions'][$conclusion],
            'general_state_id' => $this->ref['general_states'][$ageMonths <= 12 ? 'new' : 'used'],
            'new_market_value' => $vehicle->new_market_value,
            'market_value' => $marketValue,
            'depreciation_rate' => $depreciationRate,
            'salvage_value' => $salvage,
            'work_duration' => app(WorkDurationService::class)->calculateWorkDuration($shockTotals['hours']),
            'report_remark_id' => $remark,
            'work_sheet_remark_id' => $remark,
            'expert_report_remark' => $repairable
                ? 'Les réparations sont techniquement et économiquement justifiées. Pièces remplacées à présenter lors de la visite après travaux.'
                : 'Le coût des réparations dépasse la valeur de remplacement du véhicule : indemnisation sur la base de la valeur vénale, déduction faite de la valeur de l\'épave.',
            'instructions' => $repairable ? 'Bon pour travaux conformément au présent chiffrage.' : 'Véhicule à ne pas réparer. Épave à la disposition de la compagnie.',
            'other_cost_amount_excluding_tax' => $other['ht'],
            'other_cost_amount_tax' => $other['tax'],
            'other_cost_amount' => $other['ttc'],
            'total_amount_excluding_tax' => $shockTotals['ht'] + $other['ht'],
            'total_amount_tax' => $shockTotals['tax'] + $other['tax'],
            'total_amount' => $totalTtc,
            'receipt_amount_excluding_tax' => $receipts['ht'],
            'receipt_amount_tax' => $receipts['tax'],
            'receipt_amount' => $receipts['ttc'],
            'contact_date' => $at->toDateString(),
        ];
    }

    /**
     * Valeur vénale via le service de l'application, avec repli si la table de vétusté ne couvre pas l'âge du véhicule.
     */
    private function marketValue(Vehicle $vehicle, Carbon $at): array
    {
        $newValue = (float) $vehicle->new_market_value;

        try {
            if ($vehicle->vehicleGenre && $vehicle->vehicleEnergy && $vehicle->first_entry_into_circulation_date) {
                $result = (array) app(MarketValueService::class)->calculateTheoreticalMarketValue(
                    $vehicle->vehicleGenre->id,
                    $vehicle->vehicleEnergy->id,
                    $newValue,
                    $vehicle->mileage,
                    $vehicle->first_entry_into_circulation_date,
                    $at->toDateString(),
                );
                if (! empty($result['theorical_vehicle_market_value'])) {
                    return [(float) $result['theorical_vehicle_market_value'], (float) $result['theorical_depreciation_rate']];
                }
            }
        } catch (\Throwable) {
            // Repli ci-dessous.
        }

        $months = $vehicle->first_entry_into_circulation_date ? Carbon::parse($vehicle->first_entry_into_circulation_date)->diffInMonths($at) : 60;
        $rate = min(85, round($months * 0.9, 2));

        return [ceil($newValue * (100 - $rate) / 100), $rate];
    }

    /**
     * Honoraires (barème WorkFee, comme UpdateReceiptService), frais de dossier et photos.
     */
    private function writeReceipts(Assignment $assignment, Client $client, string $type, float $baseAmount, User $by, Carbon $at, int $photoCount): array
    {
        $taxRate = (float) config('services.settings.tax_rate', 18);
        $tax = fn (float $amount) => $client->is_exempt ? 0 : ceil($amount * $taxRate / 100);

        if ($type === 'evaluation') {
            $workFee = 60000;
        } else {
            $scale = $this->ref['work_fees']->first(fn ($fee) => $fee->param_1 < $baseAmount && $fee->param_2 >= $baseAmount)
                ?? $this->ref['work_fees']->sortByDesc('param_2')->first();
            $workFee = ceil((($baseAmount - $scale->param_1) * $scale->param_4 / 100) + $scale->param_3);
        }

        $lines = [ReceiptTypeEnum::WORK_FEE->value => $workFee, ReceiptTypeEnum::DOCUMENT_FEE->value => 10000];
        if ($photoCount > 0) {
            $lines[ReceiptTypeEnum::PHOTO->value] = 2000 * $photoCount;
        }

        $totals = ['ht' => 0, 'tax' => 0, 'ttc' => 0];
        foreach ($lines as $code => $amount) {
            $amountTax = $tax($amount);
            Receipt::create($this->audit([
                'assignment_id' => $assignment->id,
                'receipt_type_id' => $this->ref['receipt_types'][$code],
                'amount_excluding_tax' => $amount,
                'amount_tax' => $amountTax,
                'amount' => $amount + $amountTax,
            ], $by, $at));

            $totals['ht'] += $amount;
            $totals['tax'] += $amountTax;
            $totals['ttc'] += $amount + $amountTax;
        }

        return $totals;
    }

    /* =====================================================================
     *  Constats, photos, messages
     * ===================================================================== */

    private function writeAscertainments(Assignment $assignment, string $zone, User $by, Carbon $at): void
    {
        $grades = ['very_good', 'good', 'acceptable', 'less_good', 'bad', 'very_bad'];

        foreach ($this->ref['ascertainment_types'] as $code => $id) {
            $grade = match ($code) {
                '0001' => DemoContext::pick(['less_good', 'bad']),
                '0005' => DemoContext::pick(['acceptable', 'less_good']),
                default => DemoContext::weighted(['very_good' => 15, 'good' => 50, 'acceptable' => 30, 'less_good' => 5]),
            };

            Ascertainment::create($this->audit(array_fill_keys($grades, false) + [
                $grade => true,
                'assignment_id' => $assignment->id,
                'ascertainment_type_id' => $id,
                'comment' => $code === '0001' ? 'Déformations visibles sur la zone '.$this->zoneLabel($zone).'.' : null,
            ], $by, $at));
        }
    }

    /**
     * Copie les photos d'exemple sous public/storage/photos/report/{référence}/ (chemin lu par PhotoResource).
     */
    private function writePhotos(Assignment $assignment, PhotoTypeEnum $type, int $count, User $by, Carbon $at): int
    {
        $sources = File::glob(database_path('seeders/Demo/photos/*.jpg'));
        if (! $sources) {
            return 0;
        }

        $directory = public_path('storage/photos/report/'.$assignment->reference);
        File::ensureDirectoryExists($directory);
        $offset = Photo::where('assignment_id', $assignment->id)->count();

        for ($i = 1; $i <= $count; $i++) {
            $position = $offset + $i;
            $name = 'IMG_'.str_replace('-', '', $assignment->reference).'_'.$position.'.jpg';
            File::copy(DemoContext::pick($sources), $directory.'/'.$name);

            $isCover = $type === PhotoTypeEnum::BEFORE && $i === 1;
            Photo::create($this->audit([
                'name' => $name,
                'position' => $position,
                'is_cover' => $isCover,
                'made_as_cover_at' => $isCover ? $at : null,
                'assignment_id' => $assignment->id,
                'photo_type_id' => $this->ref['photo_types'][$type->value],
            ], $by, $at));
        }

        return $count;
    }

    private function writeMessages(Assignment $assignment, array $timeline, int $reached): void
    {
        if ($reached < 2) {
            return;
        }

        $count = DemoContext::int(0, min(4, $reached - 1));
        $moments = array_values(array_slice($timeline, 1, $reached - 1));
        foreach (DemoContext::pickMany(self::MESSAGES, $count) as $message) {
            $at = DemoContext::pick($moments)->copy()->addMinutes(DemoContext::int(10, 240));
            if ($at->greaterThan(now())) {
                $at = now()->subMinutes(DemoContext::int(5, 60));
            }

            AssignmentMessage::create($this->audit([
                'assignment_id' => $assignment->id,
                'message' => $message,
            ], DemoContext::user(RoleEnum::EXPERT, RoleEnum::OPENER, RoleEnum::EXPERT_MANAGER, RoleEnum::EDITOR), $at));
        }
    }

    /* =====================================================================
     *  Facturation et paiements
     * ===================================================================== */

    private function writeInvoice(Assignment $assignment, Client $client, ?Entity $insurer, Carbon $at, bool $withCreditBill): void
    {
        $accountant = DemoContext::user(RoleEnum::ACCOUNTANT, RoleEnum::ACCOUNTANT_MANAGER);
        $template = $insurer || $client->taxpayer_account_number ? 'B2B' : 'B2C';
        $base = [
            'date' => $at->toDateString(),
            'object' => 'Honoraires d\'expertise automobile – dossier '.$assignment->reference,
            'assignment_id' => $assignment->id,
            'type' => 'sale',
            'payment_method' => $insurer ? DemoContext::pick(['transfer', 'check']) : DemoContext::pick(['cash', 'mobile-money']),
            'template' => $template,
            'is_fne' => DemoContext::chance(65),
        ];

        if ($withCreditBill) {
            // Facture erronée annulée par un avoir, puis refacturée.
            $wrong = Invoice::create($this->audit($base + [
                'reference' => $this->timestampedReference('F', $at),
                'status_id' => DemoContext::status(StatusEnum::CANCELLED),
                'cancelled_by' => $accountant->id,
                'cancelled_at' => $at->copy()->addHours(3),
            ], $accountant, $at, withStatus: false));

            Invoice::create($this->audit(array_merge($base, [
                'reference' => $this->timestampedReference('F', $at->copy()->addHours(3)),
                'type' => 'credit_bill',
                'invoice_reference' => $wrong->reference,
                'object' => 'Avoir sur facture '.$wrong->reference.' (erreur de destinataire)',
            ]), $accountant, $at->copy()->addHours(3)));

            $at = $at->copy()->addHours(4);
        }

        Invoice::create($this->audit($base + ['reference' => $this->timestampedReference('F', $at)], $accountant, $at));
    }

    private function writePayments(Assignment $assignment, string $type, float $due, array $timeline, bool $partial): void
    {
        if ($due <= 0) {
            return;
        }

        $accountant = DemoContext::user(RoleEnum::ACCOUNTANT, RoleEnum::ACCOUNTANT_MANAGER);
        $paymentType = $type === 'insurer'
            ? DemoContext::weighted([PaymentTypeEnum::BANK_TRANSFER->value => 60, PaymentTypeEnum::CHECK->value => 40])
            : DemoContext::weighted([PaymentTypeEnum::CASH->value => 45, PaymentTypeEnum::MOBILE_MONEY->value => 40, PaymentTypeEnum::CHECK->value => 15]);

        if ($partial) {
            $amounts = [ceil($due * DemoContext::int(30, 70) / 100 / 1000) * 1000];
            $first = $timeline['invoiced']->copy()->addDays(DemoContext::int(3, 20));
            $dates = [$first->greaterThan(now()) ? now()->subHours(DemoContext::int(2, 30)) : $first];
        } elseif (DemoContext::chance(25)) {
            $part = ceil($due * 0.5 / 1000) * 1000;
            $amounts = [$part, $due - $part];
            $dates = [$timeline['invoiced']->copy()->addDays(DemoContext::int(2, 10)), $timeline['paid']];
        } else {
            $amounts = [$due];
            $dates = [$timeline['paid']];
        }

        foreach ($amounts as $i => $amount) {
            $at = $dates[$i]->min($timeline['paid'] ?? now());
            $payment = Payment::create($this->audit([
                'reference' => $this->timestampedReference('P', $at),
                'date' => $at->toDateString(),
                'amount' => $amount,
                'assignment_id' => $assignment->id,
                'payment_type_id' => $this->ref['payment_types'][$paymentType],
                'payment_method_id' => $paymentType === PaymentTypeEnum::MOBILE_MONEY->value ? DemoContext::pick($this->ref['payment_methods']) : null,
            ], $accountant, $at));

            if ($paymentType === PaymentTypeEnum::CHECK->value) {
                Check::create($this->audit([
                    'reference' => sprintf('CHQ %07d', DemoContext::int(1000, 9999999)),
                    'date' => $at->copy()->subDays(DemoContext::int(0, 3))->toDateString(),
                    'amount' => $amount,
                    'payment_id' => $payment->id,
                    'bank_id' => DemoContext::pick($this->ref['banks']),
                ], $accountant, $at));
            }

            PaymentHistoric::create($this->audit(['payment_id' => $payment->id], $accountant, $at));
        }
    }

    /* =====================================================================
     *  Mises en concurrence (comparaisons / offres des réparateurs)
     * ===================================================================== */

    private function seedComparisons(): void
    {
        $targets = Assignment::query()
            ->whereIn('id', array_keys($this->shockSpecs))
            ->whereIn('status_id', [
                DemoContext::status(StatusEnum::PENDING_FOR_REPAIRER_QUOTE),
                DemoContext::status(StatusEnum::PENDING_FOR_REPAIRER_QUOTE_VALIDATION),
                DemoContext::status(StatusEnum::VALIDATED),
            ])
            ->whereNotNull('insurer_id')
            ->orderBy('id')
            ->get()
            ->groupBy('status_id')
            ->flatMap(fn ($group) => $group->take(2));

        foreach ($targets as $assignment) {
            $closed = $assignment->status_id === DemoContext::status(StatusEnum::VALIDATED);
            $startsAt = Carbon::parse($assignment->realized_at ?? $assignment->created_at)->addHours(2);
            $endsAt = $startsAt->copy()->addDays(5);
            $by = User::find($assignment->directed_by) ?? DemoContext::user(RoleEnum::EXPERT_MANAGER);

            $comparison = Comparison::create($this->audit([
                'reference' => $this->timestampedReference('COMP_', $startsAt),
                'assignment_id' => $assignment->id,
                'starts_at' => $startsAt->toDateString(),
                'ends_at' => $endsAt->toDateString(),
                'status_id' => DemoContext::status($closed ? StatusEnum::CLOSED : StatusEnum::IN_PROGRESS),
            ], $by, $startsAt, withStatus: false));

            $assignment->timestamps = false;
            $assignment->forceFill(['is_in_comparison' => true])->save();

            $repairers = DemoContext::pickMany($this->ref['repairers'], DemoContext::int(2, 3));
            $winner = $closed ? 0 : null;
            foreach ($repairers as $i => $repairer) {
                $offerStatus = match (true) {
                    $closed => $i === $winner ? StatusEnum::ACCEPTED : StatusEnum::REJECTED,
                    default => DemoContext::pick([StatusEnum::DRAFT, StatusEnum::PENDING, StatusEnum::PENDING]),
                };
                $this->writeOffer($comparison, $repairer, $this->shockSpecs[$assignment->id], $offerStatus, $startsAt->copy()->addHours(DemoContext::int(4, 72)));
            }
        }
    }

    private function writeOffer(Comparison $comparison, Entity $repairer, array $specs, StatusEnum $status, Carbon $at): void
    {
        $by = $this->repairerUser($repairer) ?? DemoContext::user(RoleEnum::EXPERT_MANAGER);
        $at = $at->min(now());

        $offer = Offer::create($this->audit([
            'reference' => $this->timestampedReference('OFF_', $at),
            'comparison_id' => $comparison->id,
            'repairer_id' => $repairer->id,
            'status_id' => DemoContext::status($status),
        ], $by, $at, withStatus: false));

        // Chaque garage chiffre les mêmes chocs avec ses propres prix et son taux horaire.
        $calculator = new DemoQuoteCalculator();
        $totals = ['ht' => 0, 'tax' => 0, 'ttc' => 0, 'hours' => 0];
        foreach ($specs as $position => $spec) {
            $spec['hourly_rate_id'] = DemoContext::pick($this->ref['hourly_rates']);
            foreach ($spec['works'] as &$work) {
                $work['amount'] = $work['amount'] > 0 ? (int) (round($work['amount'] * DemoContext::int(85, 120) / 100 / 500) * 500) : 0;
            }
            unset($work);
            foreach ($spec['workforces'] as &$workforce) {
                $workforce['nb_hours'] = max(1, $workforce['nb_hours'] + DemoContext::int(-1, 2));
            }
            unset($workforce);

            $result = $calculator->shock($spec);
            $offerShock = OfferShock::create($this->audit($result['attributes'] + [
                'offer_id' => $offer->id,
                'position' => $position + 1,
            ], $by, $at));

            foreach ($result['works'] as $work) {
                OfferShockWork::create($this->audit($work + ['offer_shock_id' => $offerShock->id], $by, $at));
            }
            foreach ($result['workforces'] as $workforce) {
                OfferWorkforce::create($this->audit($workforce + ['offer_shock_id' => $offerShock->id], $by, $at));
            }

            $totals['ht'] += $result['attributes']['amount_excluding_tax'];
            $totals['tax'] += $result['attributes']['amount_tax'];
            $totals['ttc'] += $result['attributes']['amount'];
            $totals['hours'] += $result['nb_hours'];
        }

        $offer->timestamps = false;
        $offer->forceFill([
            'shock_amount_excluding_tax' => $totals['ht'],
            'shock_amount_tax' => $totals['tax'],
            'shock_amount' => $totals['ttc'],
            'work_duration' => max(1, (int) ceil($totals['hours'] / 8)),
        ])->save();
    }

    /* =====================================================================
     *  Outils
     * ===================================================================== */

    private function audit(array $attributes, ?User $by, Carbon $at, bool $withStatus = true): array
    {
        return $attributes
            + ($withStatus ? ['status_id' => DemoContext::status(StatusEnum::ACTIVE)] : [])
            + ['created_by' => $by?->id, 'updated_by' => $by?->id, 'created_at' => $at, 'updated_at' => $at];
    }

    private function zoneLabel(string $zone): string
    {
        return ['front' => 'avant', 'rear' => 'arrière', 'side' => 'latérale', 'roof' => 'pavillon / vitrage'][$zone] ?? $zone;
    }

    private function damageDeclared(string $zone): string
    {
        return [
            'front' => 'Pare-chocs avant enfoncé, optique avant cassée, capot déformé.',
            'rear' => 'Pare-chocs arrière arraché, feu arrière cassé, hayon enfoncé.',
            'side' => 'Portières rayées et enfoncées, rétroviseur arraché.',
            'roof' => 'Pare-brise fissuré sur toute la largeur.',
        ][$zone];
    }

    private function pointNoted(string $zone): string
    {
        return 'Choc principal en partie '.$this->zoneLabel($zone).'. Pas de dommage structurel apparent sur les longerons. Kilométrage relevé au tableau de bord.';
    }

    private function loadReferences(): void
    {
        $entityTypes = EntityType::pluck('id', 'code');

        $this->ref = [
            'entities' => Entity::whereIn('code', array_keys(self::INSURER_WEIGHTS))->get()->keyBy('code')->all(),
            'brokers' => Entity::where('entity_type_id', $entityTypes[EntityTypeEnum::BROKER->value])->get()->all(),
            'repairers' => Entity::where('entity_type_id', $entityTypes[EntityTypeEnum::REPAIRER->value])->get()->all(),
            'assignment_types' => AssignmentType::pluck('id', 'code')->all(),
            'expertise_types' => ExpertiseType::pluck('id', 'code')->all(),
            'claim_natures' => ClaimNature::pluck('id', 'code')->all(),
            'documents' => DocumentTransmitted::pluck('id')->all(),
            'supplies' => Supply::pluck('id', 'code')->all(),
            'shock_points' => ShockPoint::pluck('id', 'code')->all(),
            'paint_types' => PaintType::pluck('id', 'code')->all(),
            'hourly_rates' => HourlyRate::whereIn('value', [2600, 3000, 4000, 4500])->pluck('id')->all() ?: HourlyRate::pluck('id')->all(),
            'workforce_types' => WorkforceType::pluck('id', 'code')->all(),
            'technical_conclusions' => TechnicalConclusion::pluck('id', 'code')->all(),
            'general_states' => GeneralState::pluck('id', 'code')->all(),
            'remarks' => Remark::pluck('id', 'id')->all(),
            'other_cost_types' => OtherCostType::pluck('id')->all(),
            'receipt_types' => ReceiptType::pluck('id', 'code')->all(),
            'photo_types' => PhotoType::pluck('id', 'code')->all(),
            'ascertainment_types' => AscertainmentType::orderBy('id')->pluck('id', 'code')->all(),
            'payment_types' => PaymentType::pluck('id', 'code')->all(),
            'payment_methods' => PaymentMethod::pluck('id')->all(),
            'banks' => Bank::pluck('id')->all(),
            'work_fees' => WorkFee::where('status_id', DemoContext::status(StatusEnum::ACTIVE))->get(),
            'company_clients' => Client::whereNotNull('taxpayer_account_number')->orWhere('is_exempt', true)->pluck('id')->all(),
            'clients' => [],
            'vehicles' => [],
            'repairer_users' => [],
        ];

        // Références déjà présentes (dossiers saisis à la main, seed précédent…) pour ne jamais créer de doublon.
        foreach ([Assignment::withTrashed()->pluck('reference'), Invoice::pluck('reference'), Payment::pluck('reference')] as $existing) {
            foreach ($existing as $reference) {
                $this->references[$reference] = true;
            }
        }
    }
}
