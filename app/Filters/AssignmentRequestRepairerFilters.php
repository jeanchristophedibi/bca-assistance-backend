<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class AssignmentRequestRepairerFilters extends QueryFilters
{
    protected array $allowedFilters = [
        'assignment_request_id',
        'repairer_id',
        'status_id',
    ];

    protected array $allowedIncludes = [
        'assignmentRequest',
        'repairer',
        'status',
        'createdBy',
        'updatedBy',
        'deletedBy',
    ];

    protected array $columnSearch = [];
}
