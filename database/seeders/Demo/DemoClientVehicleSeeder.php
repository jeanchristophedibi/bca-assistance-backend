<?php

namespace Database\Seeders\Demo;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Models\Bodywork;
use App\Models\Client;
use App\Models\Color;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleEnergy;
use App\Models\VehicleGenre;
use App\Models\VehicleModel;
use Database\Seeders\Demo\Support\DemoContext;
use Illuminate\Database\Seeder;

/**
 * Clients (particuliers, sociétés, propriétaires de taxis) et leurs véhicules.
 *
 * Le schéma ne relie pas un véhicule à son propriétaire : la correspondance
 * client => véhicules est exposée via `fleet()` pour les seeders suivants.
 */
class DemoClientVehicleSeeder extends Seeder
{
    private const FIRST_NAMES = [
        'Kouadio', 'Aya', 'Yao', 'Adjoua', 'Konan', 'Affoué', 'Koffi', 'Amenan', 'Moussa', 'Fatoumata',
        'Seydou', 'Mariam', 'Brice', 'Christelle', 'Didier', 'Estelle', 'Franck', 'Grâce', 'Hamed', 'Inès',
        'Jérôme', 'Karidja', 'Lassina', 'Mireille', 'Narcisse', 'Odile', 'Patrice', 'Rokia', 'Salif', 'Tatiana',
    ];

    private const LAST_NAMES = [
        'KOUASSI', 'KONÉ', 'TRAORÉ', 'YAO', 'BAMBA', 'OUATTARA', 'COULIBALY', 'N\'GUESSAN', 'DIABATÉ', 'TOURÉ',
        'KOUADIO', 'AKA', 'DIOMANDÉ', 'DOUMBIA', 'KOFFI', 'BROU', 'GNAGNE', 'SANOGO', 'AHOUSSOU', 'ZADI',
        'TANOH', 'LOUKOU', 'DJÉ', 'SORO', 'KRA',
    ];

    /** Sociétés fictives : [raison sociale, n° compte contribuable, exonérée de TVA, taille de flotte]. */
    private const COMPANIES = [
        ['Transports Ivoire Express SARL', 'CC 1502347 K', false, 4],
        ['Société Lagunaire de Distribution SA', 'CC 0923411 B', false, 3],
        ['Akwaba Logistique SARL', 'CC 1714589 R', false, 3],
        ['Cabinet Médical Les Palmiers', 'CC 1209873 F', false, 1],
        ['Ambassade du Royaume de Lorvenie', null, true, 2],
        ['ONG Santé Pour Tous Côte d\'Ivoire', 'CC 1830044 T', true, 2],
        ['Agro-Export de la Mé SA', 'CC 0617750 M', false, 3],
        ['BTP Constructions du Sud SARL', 'CC 1345602 D', false, 2],
        ['Hôtel Résidence La Lagune', 'CC 1120458 H', false, 1],
        ['Pharmacie de la Riviera', 'CC 1467320 P', false, 1],
        ['Groupe Scolaire Les Lauréats', 'CC 1588214 G', false, 2],
        ['Sécurité Plus Services SARL', 'CC 1655019 S', false, 2],
    ];

    private const QUARTERS = [
        'Abidjan Cocody Angré', 'Abidjan Cocody Riviera 2', 'Abidjan Marcory Zone 4', 'Abidjan Plateau',
        'Abidjan Yopougon Selmer', 'Abidjan Koumassi Remblais', 'Abidjan Treichville', 'Abidjan Port-Bouët',
        'Abidjan Abobo Baoulé', 'Abidjan Adjamé 220 Logements', 'Bingerville', 'Grand-Bassam', 'Bouaké Commerce',
        'Yamoussoukro Habitat', 'San Pedro Bardot', 'Daloa Tazibouo', 'Abidjan Bietry', 'Abidjan Deux Plateaux Vallon',
    ];

    /** @var array<int, int[]> client_id => vehicle_ids */
    private static array $fleet = [];

    /** @var int[] clients propriétaires de taxis */
    private static array $taxiOwners = [];

    public static function fleet(): array
    {
        return self::$fleet;
    }

    public static function taxiOwners(): array
    {
        return self::$taxiOwners;
    }

    public function run(): void
    {
        $active = DemoContext::status(StatusEnum::ACTIVE);
        $creators = DemoContext::users(RoleEnum::OPENER, RoleEnum::ADMIN)->pluck('id')->all();
        $audit = fn () => ['status_id' => $active, 'created_by' => $by = DemoContext::pick($creators), 'updated_by' => $by];

        $usedNames = [];

        // Particuliers
        for ($i = 0; $i < 34; $i++) {
            do {
                $name = DemoContext::pick(self::FIRST_NAMES).' '.DemoContext::pick(self::LAST_NAMES);
            } while (isset($usedNames[$name]));
            $usedNames[$name] = true;

            $client = Client::create([
                'name' => $name,
                'email' => DemoContext::chance(70) ? $this->mailbox($name).'@'.DemoContext::EMAIL_DOMAIN : null,
                'address' => DemoContext::pick(self::QUARTERS),
                'phone_1' => DemoContext::phone(),
                'phone_2' => DemoContext::chance(30) ? DemoContext::phone() : null,
                'is_exempt' => false,
            ] + $audit());

            $this->vehiclesFor($client, DemoContext::chance(15) ? 2 : 1, 'particular', $audit);
        }

        // Propriétaires de taxis compteur
        for ($i = 0; $i < 5; $i++) {
            do {
                $name = DemoContext::pick(self::FIRST_NAMES).' '.DemoContext::pick(self::LAST_NAMES);
            } while (isset($usedNames[$name]));
            $usedNames[$name] = true;

            $client = Client::create([
                'name' => $name,
                'address' => DemoContext::pick(self::QUARTERS),
                'phone_1' => DemoContext::phone(),
                'is_exempt' => false,
            ] + $audit());

            self::$taxiOwners[] = $client->id;
            $this->vehiclesFor($client, DemoContext::int(1, 3), 'taxi', $audit);
        }

        // Sociétés
        foreach (self::COMPANIES as [$name, $taxpayer, $exempt, $fleetSize]) {
            $client = Client::create([
                'name' => $name,
                'email' => 'parc-auto.'.$this->mailbox($name).'@'.DemoContext::EMAIL_DOMAIN,
                'address' => DemoContext::pick(self::QUARTERS),
                'phone_1' => '+22527'.sprintf('%08d', DemoContext::int(20000000, 35999999)),
                'phone_2' => DemoContext::phone(),
                'taxpayer_account_number' => $taxpayer,
                'is_exempt' => $exempt,
            ] + $audit());

            $this->vehiclesFor($client, $fleetSize, 'company', $audit);
        }

        $this->clientAccounts();
    }

    /**
     * Comptes « client » (espace particulier) pour la démo des demandes d'assistance.
     */
    private function clientAccounts(): void
    {
        $clients = Client::whereIn('id', array_keys(self::$fleet))
            ->whereNotNull('email')
            ->whereNull('taxpayer_account_number')
            ->orderBy('id')
            ->limit(4)
            ->get();

        foreach ($clients->values() as $index => $client) {
            [$firstName, $lastName] = explode(' ', $client->name, 2);
            $user = DemoPartnerSeeder::account(
                sprintf('DEMO-CLI-%02d', $index + 1),
                $firstName,
                $lastName,
                explode('@', $client->email)[0],
                null,
                RoleEnum::CLIENT,
                $client->id,
            );

            if ($user && $user->email !== $client->email) {
                $client->update(['email' => $user->email]);
            }
        }
    }

    private function vehiclesFor(Client $client, int $count, string $profile, \Closure $audit): void
    {
        $genres = VehicleGenre::pluck('id', 'code');
        $energies = VehicleEnergy::pluck('id', 'code');
        $bodyworks = Bodywork::pluck('id', 'code');
        $colors = Color::pluck('id', 'code');

        for ($i = 0; $i < $count; $i++) {
            [$model, $genre, $newValue] = match ($profile) {
                'taxi' => [$this->model(['toyota' => ['Corolla', 'Yaris'], 'hyundai' => ['Accent', 'Elantra'], 'kia' => ['Rio'], 'nissan' => ['Sunny']]), 'VG02', [9500000, 14500000]],
                'company' => DemoContext::chance(55)
                    ? [$this->model(['toyota' => ['Hilux', 'Hiace', 'Land Cruiser Prado'], 'mitsubishi' => ['L200'], 'nissan' => ['Navara'], 'ford' => ['Ranger'], 'renault' => ['Kangoo'], 'mercedes' => ['Sprinter'], 'peugeot' => ['Partner']]), DemoContext::pick(['VG05', 'VG05', 'VG06']), [16000000, 48000000]]
                    : [$this->model(), 'VG04', [12000000, 42000000]],
                default => [$this->model(), 'VG04', [7500000, 38000000]],
            };

            $ageMonths = DemoContext::int(8, 110);
            $firstEntry = now()->subMonths($ageMonths)->subDays(DemoContext::int(0, 27));
            $energy = $genre === 'VG04' ? DemoContext::weighted(['VE01' => 55, 'VE02' => 35, 'VE04' => 10]) : 'VE02';
            $yearlyKm = match ($genre) {
                'VG02' => DemoContext::int(45000, 70000),
                'VG05', 'VG06' => DemoContext::int(30000, 60000),
                default => DemoContext::int(9000, 26000),
            };

            $vehicle = Vehicle::create([
                'license_plate' => $this->plate(),
                'type' => $model->label,
                'option' => DemoContext::pick(['Standard', 'Confort', 'Business', 'Premium', null]),
                'mileage' => (int) round($yearlyKm * $ageMonths / 12, -2),
                'serial_number' => $this->vin(),
                'first_entry_into_circulation_date' => $firstEntry->toDateString(),
                'technical_visit_date' => now()->addMonths(DemoContext::int(-2, 11))->toDateString(),
                'fiscal_power' => DemoContext::int(5, 14),
                'payload' => in_array($genre, ['VG05', 'VG06']) ? DemoContext::int(800, 3500) : 0,
                'nb_seats' => match ($genre) {
                    'VG05', 'VG06' => DemoContext::pick([2, 3, 5]),
                    default => 5,
                },
                'new_market_value' => DemoContext::price($newValue[0], $newValue[1], 100000),
                'brand_id' => $model->brand_id,
                'vehicle_model_id' => $model->id,
                'color_id' => $profile === 'taxi' ? $colors['orange'] : $colors[DemoContext::pick(['white', 'white', 'black', 'grey', 'silver', 'blue', 'red', 'beige'])],
                'bodywork_id' => $bodyworks[DemoContext::weighted(['sedan' => 70, 'break' => 20, 'cut' => 7, 'convertible' => 3])],
                'vehicle_genre_id' => $genres[$genre],
                'vehicle_energy_id' => $energies[$energy],
            ] + $audit());

            self::$fleet[$client->id][] = $vehicle->id;
        }
    }

    /**
     * Modèle tiré au hasard, éventuellement restreint : ['toyota' => ['Hilux'], ...].
     */
    private function model(?array $only = null): VehicleModel
    {
        static $all = null;
        $all ??= VehicleModel::with('brand:id,code')->get();

        $candidates = $only === null
            ? $all
            : $all->filter(fn (VehicleModel $model) => in_array($model->label, $only[$model->brand?->code] ?? [], true));

        return DemoContext::pick(($candidates->isNotEmpty() ? $candidates : $all)->all());
    }

    /** Immatriculation ivoirienne : « 1234 AB 01 ». */
    private function plate(): string
    {
        static $used = [];
        $letters = 'ABCDEFGHJKLMNPRSTUVWXYZ';

        do {
            $plate = sprintf('%04d %s%s %02d', DemoContext::int(1, 9999), $letters[DemoContext::int(0, 22)], $letters[DemoContext::int(0, 22)], DemoContext::pick([1, 1, 1, 1, 2, 3, 5]));
        } while (isset($used[$plate]) || Vehicle::where('license_plate', $plate)->exists());

        return $used[$plate] = $plate;
    }

    private function vin(): string
    {
        $chars = 'ABCDEFGHJKLMNPRSTUVWXYZ0123456789';

        return implode('', array_map(fn () => $chars[DemoContext::int(0, strlen($chars) - 1)], range(1, 17)));
    }

    private function mailbox(string $name): string
    {
        return str($name)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', '.')->trim('.')->limit(40, '')->toString();
    }
}
