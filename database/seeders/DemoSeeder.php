<?php

namespace Database\Seeders;

use App\Models\Client;
use Database\Seeders\Demo\DemoAssignmentRequestSeeder;
use Database\Seeders\Demo\DemoAssignmentSeeder;
use Database\Seeders\Demo\DemoClientVehicleSeeder;
use Database\Seeders\Demo\DemoPartnerSeeder;
use Database\Seeders\Demo\DemoReferentialSeeder;
use Database\Seeders\Demo\Support\DemoContext;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Jeu de données de démonstration (à lancer APRÈS DatabaseSeeder) :
 *
 *   php artisan migrate:fresh --seed
 *   php artisan db:seed --class=DemoSeeder
 *
 * ou en une fois : SEED_DEMO_DATA=true php artisan migrate:fresh --seed
 *
 * Les données sont déterministes (même graine => mêmes dossiers) et datées
 * relativement à aujourd'hui. Tous les e-mails créés utilisent le domaine réservé
 * DemoContext::EMAIL_DOMAIN : aucune notification ne peut atteindre une vraie boîte.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('DemoSeeder refusé en production.');

            return;
        }

        if (Client::where('email', 'like', '%@'.DemoContext::EMAIL_DOMAIN)->exists()) {
            $this->command?->warn('Les données de démo sont déjà présentes. Pour repartir de zéro : php artisan migrate:fresh --seed && php artisan db:seed --class=DemoSeeder');

            return;
        }

        DemoContext::seed();

        DB::transaction(function () {
            $this->call([
                DemoReferentialSeeder::class,
                DemoPartnerSeeder::class,
                DemoClientVehicleSeeder::class,
                DemoAssignmentRequestSeeder::class,
                DemoAssignmentSeeder::class,
            ]);
        });

        $this->command?->table(['Compte de démo', 'Profil'], [
            ['nsia.admin@'.DemoContext::EMAIL_DOMAIN, 'Administrateur assureur (NSIA)'],
            ['axa.gestionnaire@'.DemoContext::EMAIL_DOMAIN, 'Utilisateur assureur (AXA)'],
            ['cfao.admin@'.DemoContext::EMAIL_DOMAIN, 'Administrateur réparateur (CFAO)'],
            ['marcory.atelier@'.DemoContext::EMAIL_DOMAIN, 'Utilisateur réparateur'],
            ['Comptes DEMO-CLI-xx', 'Clients (demandes d\'assistance)'],
        ]);
        $this->command?->info('Mot de passe commun : '.DemoContext::PASSWORD.' (comptes BCA : ceux de UserSeeder).');
    }
}
