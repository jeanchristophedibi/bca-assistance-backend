<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class OfferShockWorkFilters extends QueryFilters
{
    protected array $allowedFilters = [
        'ids',
        'status_id',
    ];

    protected array $allowedIncludes = [
        'offerShock',
        'supply',
        'oldSupply',
        'status',
        'createdBy',
        'updatedBy',
        'deletedBy',
    ];

    protected array $columnSearch = [];
}
