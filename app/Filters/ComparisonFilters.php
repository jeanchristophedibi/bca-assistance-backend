<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class ComparisonFilters extends QueryFilters
{
    protected array $allowedFilters = [
        'ids',
        'status_id',
    ];

    protected array $allowedIncludes = [
        'assignment',
        'status',
        'offers',
        'createdBy',
        'updatedBy',
        'deletedBy',
    ];

    protected array $columnSearch = [];
}
