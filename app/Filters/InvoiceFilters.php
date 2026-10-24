<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class InvoiceFilters extends QueryFilters
{
    protected array $allowedFilters = [
        'assignment_id',
    ];

    protected array $columnSearch = ['invoices.reference'];

    protected array $relationSearch = [
        'assignment' => ['reference'],
    ];

    /* Filtres des chiffres clés de la page Factures. La liste est jointe aux dossiers :
       les colonnes sont préfixées pour ne pas être ambiguës. */
    public function status_id($value): void
    {
        $this->builder->whereIn('invoices.status_id', array_filter(explode(',', (string) $value)));
    }

    /** sale (facture) ou credit_bill (avoir). */
    public function type($value): void
    {
        $this->builder->whereIn('invoices.type', array_filter(explode(',', (string) $value)));
    }
}
