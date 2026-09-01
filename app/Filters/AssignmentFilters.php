<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class AssignmentFilters extends QueryFilters
{
    protected array $allowedFilters = [
        'assignment_type_id',
        'expertise_type_id',
        'created_by',
        'edited_by',
        'directed_by',
        'realized_by',
        'validated_by',
        'opened_by',
        'paid_by',
        'unpaid_by',
        'status_id',
        'vehicle_id',
        'insurer_id',
        'broker_id',
        'client_id',
        'claim_nature_id',
        'status_id',
        'reference_updated_by',
        'generated_by',
        'work_sheet_established_by',
        'created_by',
        'opened_by',
        'paid_by',
        'unpaid_by',
        'first_correction_validated_by',
        'first_correction_unvalidated_by',
        'second_correction_validated_by',
        'second_correction_unvalidated_by',
        'validated_by',
        'unvalidated_by',
        'cancelled_by',
        'deleted_by',
        'archived_by',
        'classified_without_further_action_by',
        'pending_for_repairer_quote_by',
        'pending_for_repairer_quote_validation_by',
        'in_editing_by',
        'in_first_correction_by',
        'in_second_correction_by',
        'pending_for_repairer_validation_by',
        'pending_for_expert_validation_by',
        'validated_by_repairer_by',
        'unvalidated_by_repairer_by',
        'validated_by_expert_by',
        'unvalidated_by_expert_by',
        'agreement_for_work_subject_to_conditions_by',
        'quote_validated_by_repairer_by',
        'quote_unvalidated_by_repairer_at',
        'quote_validated_by_expert_by',
        'quote_unvalidated_by_expert_by',
        'edition_unvalidated_by',
        'edition_validated_by',
    ];

    protected array $allowedIncludes = [
        'insurer', 
        'broker', 
        'additionalInsurer',
        'repairers', 
        'client', 
        'vehicle', 
        'status', 
        'expertiseType', 
        'assignmentType', 
        'createdBy', 
        'openedBy',
        'paidBy',
        'unpaidBy',
        'directedBy',
        'realizedBy', 
        'editedBy', 
        'validatedBy', 
        'claimNature',
    ];


    protected array $columnSearch = [
        'reference','claim_number','weproov_reference'
    ];

    protected array $relationSearch = [
        'vehicle' => ['license_plate'],
        'insurer' => ['name'],
        'broker' => ['name'],
        'additionalInsurer' => ['name'],
        'repairers' => ['name'],
        'client' => ['name'],
    ];

    public function repairer_id($value): void
    {
        $this->builder->whereHas('repairers', fn ($q) => $q->where('entities.id', $value));
    }
}
