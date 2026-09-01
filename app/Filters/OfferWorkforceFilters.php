<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class OfferWorkforceFilters extends QueryFilters
{
    protected array $allowedFilters = [
        'ids',
        'status_id',
    ];

    protected array $allowedIncludes = [
        'offerShock',
        'workforceType',
        'oldWorkforceType',
        'status',
        'createdBy',
        'updatedBy',
        'deletedBy',
    ];

    protected array $columnSearch = [];
}
