<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class PaymentFilters extends QueryFilters
{
    protected array $allowedFilters = [
        'assignment_id',
    ];

    protected array $columnSearch = ['payments.reference'];

    protected array $relationSearch = [
        'assignment' => ['reference'],
    ];

    /* Filtres des chiffres clés de la page Paiements (colonnes préfixées : jointure aux dossiers). */
    public function status_id($value): void
    {
        $this->builder->whereIn('payments.status_id', array_filter(explode(',', (string) $value)));
    }

    public function payment_type_id($value): void
    {
        $this->builder->whereIn('payments.payment_type_id', array_filter(explode(',', (string) $value)));
    }
}
