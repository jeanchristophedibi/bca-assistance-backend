<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class ClientFilters extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ['name','phone_1','phone_2','email'];

    /* Filtres des chiffres clés de la page Clients. */
    public function is_exempt($value): void
    {
        $this->builder->where('clients.is_exempt', filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }

    /** company : client avec n° de compte contribuable ; individual : particulier. */
    public function kind($value): void
    {
        $value === 'company'
            ? $this->builder->whereNotNull('clients.taxpayer_account_number')
            : $this->builder->whereNull('clients.taxpayer_account_number');
    }
}
