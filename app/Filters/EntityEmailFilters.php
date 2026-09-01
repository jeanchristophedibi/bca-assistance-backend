<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class EntityEmailFilters extends QueryFilters
{
    protected array $allowedFilters = ['entity_id', 'status_id'];

    protected array $columnSearch = ['email'];
}
