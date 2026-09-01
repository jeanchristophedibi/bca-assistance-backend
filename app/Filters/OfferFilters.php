<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class OfferFilters extends QueryFilters
{
    protected array $allowedFilters = [
        'ids',
        'status_id',
    ];

    protected array $allowedIncludes = [
        'comparison',
        'repairer',
        'status',
        'offerShocks',
        'createdBy',
        'updatedBy',
        'deletedBy',
    ];

    protected array $columnSearch = [];
}
