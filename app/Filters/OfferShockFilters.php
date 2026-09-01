<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class OfferShockFilters extends QueryFilters
{
    protected array $allowedFilters = [
        'ids',
        'status_id',
    ];

    protected array $allowedIncludes = [
        'offer',
        'shockPoint',
        'paintType',
        'hourlyRate',
        'status',
        'offerShockWorks',
        'offerWorkforces',
        'createdBy',
        'updatedBy',
        'deletedBy',
    ];

    protected array $columnSearch = [];
}
