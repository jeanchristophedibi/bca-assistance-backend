<?php

namespace Database\Seeders\Demo;

use App\Enums\StatusEnum;
use App\Models\Bank;
use App\Models\Brand;
use App\Models\Color;
use App\Models\ShockPoint;
use App\Models\Supply;
use App\Models\User;
use App\Models\VehicleModel;
use Database\Seeders\Demo\Support\DemoContext;
use Illuminate\Database\Seeder;

/**
 * Complète les référentiels trop maigres pour une démo réaliste
 * (pièces, points de choc, modèles, couleurs, banques). Idempotent.
 */
class DemoReferentialSeeder extends Seeder
{
    /**
     * Pièces : code => [libellé, zones de choc concernées, prix min, prix max] (F CFA HT).
     * Les zones servent à proposer des pièces cohérentes avec le point de choc.
     */
    public const SUPPLIES = [
        'S001' => ['Pare chocs avant', ['front'], 85000, 320000],
        'S002' => ['Pare chocs arrière', ['rear'], 80000, 290000],
        'S003' => ['Pare chocs latéral', ['side'], 45000, 120000],
        'S005' => ['Jupe avant', ['front'], 35000, 95000],
        'S004' => ['Jupe arrière', ['rear'], 35000, 90000],
        'S006' => ['Capot moteur', ['front'], 120000, 450000],
        'S007' => ['Aile avant gauche', ['front', 'side'], 65000, 210000],
        'S008' => ['Aile avant droite', ['front', 'side'], 65000, 210000],
        'S009' => ['Aile arrière gauche', ['rear', 'side'], 90000, 280000],
        'S010' => ['Aile arrière droite', ['rear', 'side'], 90000, 280000],
        'S011' => ['Portière avant gauche', ['side'], 180000, 520000],
        'S012' => ['Portière avant droite', ['side'], 180000, 520000],
        'S013' => ['Portière arrière gauche', ['side'], 160000, 480000],
        'S014' => ['Portière arrière droite', ['side'], 160000, 480000],
        'S015' => ['Hayon / Malle arrière', ['rear'], 150000, 560000],
        'S016' => ['Projecteur avant gauche', ['front'], 75000, 380000],
        'S017' => ['Projecteur avant droit', ['front'], 75000, 380000],
        'S018' => ['Feu arrière gauche', ['rear'], 35000, 160000],
        'S019' => ['Feu arrière droit', ['rear'], 35000, 160000],
        'S020' => ['Rétroviseur extérieur gauche', ['side'], 30000, 140000],
        'S021' => ['Rétroviseur extérieur droit', ['side'], 30000, 140000],
        'S022' => ['Pare-brise', ['front', 'roof'], 110000, 350000],
        'S023' => ['Lunette arrière', ['rear', 'roof'], 90000, 260000],
        'S024' => ['Calandre', ['front'], 40000, 180000],
        'S025' => ['Radiateur de refroidissement', ['front'], 70000, 240000],
        'S026' => ['Condenseur de climatisation', ['front'], 80000, 260000],
        'S027' => ['Traverse avant', ['front'], 55000, 170000],
        'S028' => ['Bas de caisse gauche', ['side'], 60000, 190000],
        'S029' => ['Bas de caisse droit', ['side'], 60000, 190000],
        'S030' => ['Pavillon (toit)', ['roof'], 250000, 700000],
        'S031' => ['Jante alliage', ['side', 'front', 'rear'], 65000, 230000],
        'S032' => ['Pneumatique', ['side', 'front', 'rear'], 45000, 140000],
        'S033' => ['Coussin gonflable conducteur', ['front'], 250000, 650000],
        'S034' => ['Ceinture de sécurité avant', ['front'], 45000, 120000],
        'S035' => ['Absorbeur de choc avant', ['front'], 25000, 85000],
    ];

    /** Points de choc : code => [libellé, zone]. */
    public const SHOCK_POINTS = [
        'SP001' => ['Arrière droit', 'rear'],
        'SP002' => ['Arrière gauche', 'rear'],
        'SP003' => ['Avant droit', 'front'],
        'SP004' => ['Avant gauche', 'front'],
        'SP005' => ['Avant central', 'front'],
        'SP006' => ['Arrière central', 'rear'],
        'SP007' => ['Latéral gauche', 'side'],
        'SP008' => ['Latéral droit', 'side'],
        'SP009' => ['Pavillon', 'roof'],
    ];

    /** Modèles : code marque => [modèles]. */
    public const MODELS = [
        'toyota' => ['Corolla', 'Yaris', 'RAV4', 'Hilux', 'Land Cruiser Prado', 'Fortuner', 'Hiace'],
        'hyundai' => ['Accent', 'Elantra', 'Tucson', 'Santa Fe', 'Creta'],
        'kia' => ['Picanto', 'Rio', 'Sportage', 'Sorento'],
        'peugeot' => ['208', '301', '308', '3008', '508', 'Partner'],
        'renault' => ['Logan', 'Sandero', 'Duster', 'Koleos', 'Kangoo'],
        'nissan' => ['Sunny', 'X-Trail', 'Navara', 'Patrol'],
        'mitsubishi' => ['L200', 'Pajero', 'Outlander'],
        'mercedes' => ['Classe C', 'Classe E', 'GLC', 'Sprinter'],
        'ford' => ['Ranger', 'EcoSport', 'Everest'],
        'volkswagen' => ['Polo', 'Golf', 'Tiguan', 'Amarok'],
        'bmw' => ['Série 3', 'X3', 'X5'],
        'mazda' => ['Mazda 3', 'CX-5', 'BT-50'],
        'citroen' => ['C3', 'C4', 'Berlingo'],
        'landrover' => ['Range Rover Evoque', 'Defender'],
        'audi' => ['A4', 'Q5'],
    ];

    public const COLORS = [
        'white' => 'Blanche',
        'red' => 'Rouge',
        'blue' => 'Bleu',
        'black' => 'Noire',
        'grey' => 'Grise',
        'silver' => 'Gris argent',
        'beige' => 'Beige',
        'green' => 'Verte',
        'orange' => 'Orange',
        'brown' => 'Marron',
    ];

    public const BANKS = [
        'SGCI' => 'Société Générale Côte d\'Ivoire',
        'SIB' => 'Société Ivoirienne de Banque',
        'NSIA_BANQUE' => 'NSIA Banque',
        'ECOBANK' => 'Ecobank Côte d\'Ivoire',
        'BICICI' => 'BICICI',
        'BOA' => 'Bank Of Africa',
        'CORIS_BANK' => 'Coris Bank International',
        'BNI' => 'Banque Nationale d\'Investissement',
        'BACI' => 'Banque Atlantique Côte d\'Ivoire',
    ];

    public function run(): void
    {
        $active = DemoContext::status(StatusEnum::ACTIVE);
        $admin = User::orderBy('id')->value('id');
        $audit = ['status_id' => $active, 'created_by' => $admin, 'updated_by' => $admin];

        foreach (self::SUPPLIES as $code => [$label]) {
            Supply::firstOrCreate(['code' => $code], ['label' => $label, 'description' => $label] + $audit);
        }

        foreach (self::SHOCK_POINTS as $code => [$label]) {
            ShockPoint::firstOrCreate(['code' => $code], ['label' => $label, 'description' => $label] + $audit);
        }

        foreach (self::COLORS as $code => $label) {
            Color::firstOrCreate(['code' => $code], ['label' => $label, 'description' => $label] + $audit);
        }

        foreach (self::MODELS as $brandCode => $models) {
            $brand = Brand::where('code', $brandCode)->first();
            if (! $brand) {
                continue;
            }

            foreach ($models as $label) {
                $exists = VehicleModel::where('brand_id', $brand->id)->where('label', $label)->exists();
                if (! $exists) {
                    VehicleModel::create([
                        'code' => $brandCode.'-'.str($label)->slug(),
                        'label' => $label,
                        'description' => $brand->label.' '.$label,
                        'brand_id' => $brand->id,
                    ] + $audit);
                }
            }
        }

        foreach (self::BANKS as $code => $name) {
            Bank::firstOrCreate(['code' => $code], ['name' => $name] + $audit);
        }
    }
}
