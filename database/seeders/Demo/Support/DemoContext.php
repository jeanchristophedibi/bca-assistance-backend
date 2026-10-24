<?php

namespace Database\Seeders\Demo\Support;

use App\Models\Entity;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

/**
 * Accès mis en cache aux référentiels utilisés par les seeders de démo
 * (statuts, utilisateurs par rôle, entités) et générateur aléatoire déterministe.
 */
class DemoContext
{
    /** Domaine réservé (RFC 2606) : aucun e-mail de démo ne peut partir vers une vraie boîte. */
    public const EMAIL_DOMAIN = 'demo-bca.test';

    public const PASSWORD = '12345678';

    private static array $statuses = [];

    private static array $usersByRole = [];

    private static array $entities = [];

    public static function seed(int $seed = 2026): void
    {
        mt_srand($seed);
        fake('fr_FR')->seed($seed);
    }

    public static function status(\BackedEnum|string $code): int
    {
        $code = $code instanceof \BackedEnum ? $code->value : $code;

        return self::$statuses[$code] ??= Status::where('code', $code)->firstOrFail()->id;
    }

    public static function role(\BackedEnum|string $name): Role
    {
        $name = $name instanceof \BackedEnum ? $name->value : $name;

        return Role::where('name', $name)->firstOrFail();
    }

    /**
     * Utilisateurs BCA ayant le rôle donné (rôle courant).
     */
    public static function users(\BackedEnum|string ...$roles): Collection
    {
        $names = array_map(fn ($role) => $role instanceof \BackedEnum ? $role->value : $role, $roles);
        $key = implode('|', $names);

        return self::$usersByRole[$key] ??= User::query()
            ->whereIn('current_role_id', Role::whereIn('name', $names)->pluck('id'))
            ->orderBy('id')
            ->get();
    }

    public static function user(\BackedEnum|string ...$roles): User
    {
        $users = self::users(...$roles);

        return $users->isNotEmpty() ? self::pick($users->all()) : User::orderBy('id')->firstOrFail();
    }

    public static function entity(string $code): ?Entity
    {
        return self::$entities[$code] ??= Entity::where('code', $code)->first();
    }

    public static function forget(): void
    {
        self::$usersByRole = [];
        self::$entities = [];
    }

    /* ---------- Aléatoire déterministe ---------- */

    public static function int(int $min, int $max): int
    {
        return mt_rand($min, $max);
    }

    public static function chance(int $percent): bool
    {
        return mt_rand(1, 100) <= $percent;
    }

    public static function pick(array $items): mixed
    {
        return $items[array_rand($items)];
    }

    /**
     * Tire `$count` éléments distincts.
     */
    public static function pickMany(array $items, int $count): array
    {
        $count = max(0, min($count, count($items)));
        if ($count === 0) {
            return [];
        }

        $keys = (array) array_rand($items, $count);

        return array_values(array_intersect_key($items, array_flip($keys)));
    }

    /**
     * Tirage pondéré : ['valeur' => poids, ...].
     */
    public static function weighted(array $weights): string
    {
        $roll = mt_rand(1, array_sum($weights));
        foreach ($weights as $value => $weight) {
            $roll -= $weight;
            if ($roll <= 0) {
                return (string) $value;
            }
        }

        return (string) array_key_last($weights);
    }

    /**
     * Arrondi « commercial » d'un prix de pièce (au millier ou à 500 F CFA).
     */
    public static function price(int $min, int $max, int $step = 500): int
    {
        return (int) (round(mt_rand($min, $max) / $step) * $step);
    }

    public static function phone(): string
    {
        // Même format que les saisies de l'application : +225 suivi des 10 chiffres.
        return '+225'.self::pick(['07', '05', '01']).sprintf('%08d', mt_rand(0, 99999999));
    }
}
