<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class AssignmentRepairerFilters extends QueryFilters
{
    protected array $allowedFilters = [
        'assignment_id',
        'repairer_id',
        'status_id',
    ];

    protected array $allowedIncludes = [
        'assignment',
        'repairer',
        'status',
        'createdBy',
        'updatedBy',
        'deletedBy',
    ];

    protected array $columnSearch = [];
}
