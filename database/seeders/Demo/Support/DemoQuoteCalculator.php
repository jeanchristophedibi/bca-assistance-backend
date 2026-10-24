<?php

namespace Database\Seeders\Demo\Support;

use App\Enums\StatusEnum;
use App\Enums\WorkforceTypeEnum;
use App\Models\HourlyRate;
use App\Models\NumberPaintElement;
use App\Models\PaintingPrice;
use App\Models\PaintProductPrice;
use App\Models\WorkforceType;

/**
 * Calcule un choc (fournitures + main d'œuvre + ingrédients peinture + petites fournitures)
 * avec les mêmes formules que AssignmentController::calculate(), afin que les montants
 * de démo soient identiques à ceux que l'application recalculerait.
 *
 * Entrée d'un choc :
 *   [
 *     'shock_point_id', 'paint_type_id', 'hourly_rate_id', 'with_tax' => bool,
 *     'works' => [[supply_id, amount, disassembly, replacement, repair, paint, obsolescence,
 *                  control, in_order, obsolescence_rate, discount, recovery_amount, comment], ...],
 *     'workforces' => [[workforce_type_id, nb_hours, discount, all_paint], ...],
 *   ]
 */
class DemoQuoteCalculator
{
    private float $taxRate;

    private float $smallSupplyRate;

    public function __construct(private bool $withoutTaxOnSupplies = false)
    {
        $this->taxRate = (float) config('services.settings.tax_rate', 18);
        $this->smallSupplyRate = (float) config('services.settings.small_supply_rate', 2);
    }

    public function shock(array $shock): array
    {
        $works = [];
        $nbPaint = 0;
        $totals = array_fill_keys([
            'obsolescence_ht', 'obsolescence_tax', 'obsolescence',
            'recovery_ht', 'recovery_tax', 'recovery',
            'discount_ht', 'discount_tax', 'discount',
            'new_ht', 'new_tax', 'new',
            'in_order_ht', 'in_order_tax', 'in_order',
        ], 0);

        foreach ($shock['works'] as $position => $work) {
            $amount = $work['amount'];
            $discountHt = ceil(($work['discount'] * $amount) / 100);
            $discountTax = $this->tax($discountHt);
            $obsolescenceHt = ceil(($work['obsolescence_rate'] * ($amount - $discountHt)) / 100);
            $obsolescenceTax = $this->tax($obsolescenceHt);
            $recoveryHt = $work['recovery_amount'] ?? 0;
            $newHt = ceil($amount - ($obsolescenceHt + $discountHt));
            $newTax = $this->withoutTaxOnSupplies ? 0 : $this->tax($newHt);

            $works[] = [
                'supply_id' => $work['supply_id'],
                'disassembly' => $work['disassembly'] ?? false,
                'replacement' => $work['replacement'] ?? false,
                'repair' => $work['repair'] ?? false,
                'paint' => $work['paint'] ?? false,
                'obsolescence' => $work['obsolescence'] ?? false,
                'control' => $work['control'] ?? false,
                'in_order' => $work['in_order'] ?? false,
                'comment' => $work['comment'] ?? null,
                'position' => $position + 1,
                'amount' => $amount,
                'obsolescence_rate' => $work['obsolescence_rate'],
                'obsolescence_amount_excluding_tax' => $obsolescenceHt,
                'obsolescence_amount_tax' => $obsolescenceTax,
                'obsolescence_amount' => $obsolescenceHt + $obsolescenceTax,
                'recovery_amount_excluding_tax' => $recoveryHt,
                'recovery_amount_tax' => 0,
                'recovery_amount' => $recoveryHt,
                'discount' => $work['discount'],
                'discount_amount_excluding_tax' => $discountHt,
                'discount_amount_tax' => $discountTax,
                'discount_amount' => $discountHt + $discountTax,
                'new_amount_excluding_tax' => $newHt,
                'new_amount_tax' => $newTax,
                'new_amount' => $newHt + $newTax,
            ];

            if ($work['paint'] ?? false) {
                $nbPaint++;
            }

            $totals['obsolescence_ht'] += $obsolescenceHt;
            $totals['obsolescence_tax'] += $obsolescenceTax;
            $totals['obsolescence'] += $obsolescenceHt + $obsolescenceTax;
            $totals['recovery_ht'] += $recoveryHt;
            $totals['recovery'] += $recoveryHt;
            $totals['discount_ht'] += $discountHt;
            $totals['discount_tax'] += $discountTax;
            $totals['discount'] += $discountHt + $discountTax;
            $totals['new_ht'] += $newHt;
            $totals['new_tax'] += $newTax;
            $totals['new'] += $newHt + $newTax;
            if ($work['in_order'] ?? false) {
                $totals['in_order_ht'] += $newHt;
                $totals['in_order_tax'] += $newTax;
                $totals['in_order'] += $newHt + $newTax;
            }
        }

        $hourlyRate = $this->hourlyRate($shock['hourly_rate_id']);
        $paintTypeId = WorkforceType::where('code', WorkforceTypeEnum::PAINT)->value('id');
        $allPaint = false;
        $nbHoursPaintProduct = 0;
        $nbHoursTotal = 0;
        $workforces = [];
        $workforceHt = $workforceTax = $workforceTotal = 0;

        foreach ($shock['workforces'] as $position => $workforce) {
            $nbHoursTotal += $workforce['nb_hours'];

            if ($workforce['workforce_type_id'] == $paintTypeId) {
                $total = 0;
                if ($nbPaint > 0) {
                    $element = ($workforce['all_paint'] ?? false) ? 0 : min($nbPaint, 3);
                    $allPaint = $allPaint || ($workforce['all_paint'] ?? false);
                    $paintingPrice = $this->paintingPrice($shock['hourly_rate_id'], $shock['paint_type_id'], $element);
                    $nbHoursPaintProduct += $workforce['nb_hours'];
                    $total = $paintingPrice ? $workforce['nb_hours'] * $paintingPrice->param_1 + $paintingPrice->param_2 : 0;
                }
            } else {
                $total = $workforce['nb_hours'] * $hourlyRate;
            }

            $amountHt = ceil($total - ($total * $workforce['discount'] / 100));
            $amountTax = $shock['with_tax'] ? $this->tax($amountHt) : 0;

            $workforces[] = [
                'workforce_type_id' => $workforce['workforce_type_id'],
                'nb_hours' => $workforce['nb_hours'],
                'work_fee' => ceil($hourlyRate),
                'with_tax' => $shock['with_tax'],
                'discount' => $workforce['discount'],
                'all_paint' => $workforce['all_paint'] ?? false,
                'position' => $position + 1,
                'amount_excluding_tax' => $amountHt,
                'amount_tax' => $amountTax,
                'amount' => $amountHt + $amountTax,
            ];

            $workforceHt += $amountHt;
            $workforceTax += $amountTax;
            $workforceTotal += $amountHt + $amountTax;
        }

        $paintProductHt = 0;
        if ($nbPaint > 0) {
            $productPrice = $this->paintProductPrice($shock['paint_type_id'], $allPaint ? 0 : min($nbPaint, 3));
            $paintProductHt = $productPrice ? ceil($productPrice->value * $nbHoursPaintProduct) : 0;
        }
        $paintProductTax = $this->tax($paintProductHt);

        $smallSupplyHt = ceil(($totals['new_ht'] + $workforceHt + $paintProductHt + $totals['recovery_ht']) * ($this->smallSupplyRate / 100));
        $smallSupplyTax = $this->tax($smallSupplyHt);

        $amountHt = ceil($totals['new_ht'] + $workforceHt + $paintProductHt + $smallSupplyHt + $totals['recovery_ht']);
        $amountTax = ceil($totals['new_tax'] + $workforceTax + $paintProductTax + $smallSupplyTax);

        return [
            'attributes' => [
                'shock_point_id' => $shock['shock_point_id'],
                'paint_type_id' => $shock['paint_type_id'],
                'hourly_rate_id' => $shock['hourly_rate_id'],
                'with_tax' => $shock['with_tax'],
                'shock_work_in_order_amount_excluding_tax' => $totals['in_order_ht'],
                'shock_work_in_order_amount_tax' => $totals['in_order_tax'],
                'shock_work_in_order_amount' => $totals['in_order'],
                'shock_work_obsolescence_amount_excluding_tax' => $totals['obsolescence_ht'],
                'shock_work_obsolescence_amount_tax' => $totals['obsolescence_tax'],
                'shock_work_obsolescence_amount' => $totals['obsolescence'],
                'shock_work_recovery_amount_excluding_tax' => $totals['recovery_ht'],
                'shock_work_recovery_amount_tax' => 0,
                'shock_work_recovery_amount' => $totals['recovery'],
                'shock_work_discount_amount_excluding_tax' => $totals['discount_ht'],
                'shock_work_discount_amount_tax' => $totals['discount_tax'],
                'shock_work_discount_amount' => $totals['discount'],
                'shock_work_new_amount_excluding_tax' => $totals['new_ht'],
                'shock_work_new_amount_tax' => $totals['new_tax'],
                'shock_work_new_amount' => $totals['new'],
                'workforce_amount_excluding_tax' => $workforceHt,
                'workforce_amount_tax' => $workforceTax,
                'workforce_amount' => $workforceTotal,
                'paint_product_amount_excluding_tax' => $paintProductHt,
                'paint_product_amount_tax' => $paintProductTax,
                'paint_product_amount' => $paintProductHt + $paintProductTax,
                'small_supply_amount_excluding_tax' => $smallSupplyHt,
                'small_supply_amount_tax' => $smallSupplyTax,
                'small_supply_amount' => $smallSupplyHt + $smallSupplyTax,
                'amount_excluding_tax' => $amountHt,
                'amount_tax' => $amountTax,
                'amount' => $amountHt + $amountTax,
            ],
            'works' => $works,
            'workforces' => $workforces,
            'nb_hours' => $nbHoursTotal,
        ];
    }

    private function tax(float|int $amount): float
    {
        return ceil(($this->taxRate * $amount) / 100);
    }

    private function hourlyRate(int $id): float
    {
        static $cache = [];

        return $cache[$id] ??= (float) HourlyRate::where('id', $id)->value('value');
    }

    private function elementId(int $value): ?int
    {
        static $cache = [];

        return $cache[$value] ??= NumberPaintElement::where('value', $value)->value('id');
    }

    private function paintingPrice(int $hourlyRateId, int $paintTypeId, int $element): ?PaintingPrice
    {
        return PaintingPrice::where([
            'hourly_rate_id' => $hourlyRateId,
            'paint_type_id' => $paintTypeId,
            'number_paint_element_id' => $this->elementId($element),
            'status_id' => DemoContext::status(StatusEnum::ACTIVE),
        ])->first();
    }

    private function paintProductPrice(int $paintTypeId, int $element): ?PaintProductPrice
    {
        return PaintProductPrice::where([
            'paint_type_id' => $paintTypeId,
            'number_paint_element_id' => $this->elementId($element),
            'status_id' => DemoContext::status(StatusEnum::ACTIVE),
        ])->first();
    }
}
