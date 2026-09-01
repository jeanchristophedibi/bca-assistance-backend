<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class AssignmentRequestFilters extends QueryFilters
{
    protected array $allowedFilters = [
        'client_id',
        'vehicle_id',
        'status_id',
        'created_by',
        'updated_by',
        'cancelled_by',
        'rejected_by',
        'deleted_by',
    ];

    protected array $allowedIncludes = [
        'client',
        'vehicle',
        'repairers',
        'status',
        'photos',
        'createdBy',
        'updatedBy',
        'cancelledBy',
        'rejectedBy',
        'deletedBy',
    ];

    protected array $columnSearch = [
        'reference',
        'assistance_place',
        'observation',
    ];

    protected array $relationSearch = [
        'client' => ['name'],
        'vehicle' => ['license_plate'],
        'repairers' => ['name'],
    ];

    public function repairer_id($value): void
    {
        $this->builder->whereHas('repairers', fn ($q) => $q->where('entities.id', $value));
    }
}
