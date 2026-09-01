<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Entity;
use App\Models\Status;
use App\Models\Vehicle;
use App\Enums\StatusEnum;
use App\Models\Assignment;
use App\Models\EntityType;
use App\Enums\EntityTypeEnum;
use App\Models\AssignmentType;
use App\Enums\AssignmentTypeEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;

/**
 * @group Statistiques du tableau de bord
 */
class DashboardController extends Controller
{
    use ApiResponse;

    /**
     * Afficher les statistiques des utilisateurs
     */
    public function users() : JsonResponse
    {
        // $this->authorize('viewAny', User::class);

        return $this->responseSuccess(null, [
            'total_users' => ['value' => User::accessibleBy(auth()->user())->count()],
            'active_users' => ['value' => User::where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->accessibleBy(auth()->user())->count()],
            'inactive_users' => ['value' => User::where('status_id', Status::where('code', StatusEnum::INACTIVE)->first()->id)->accessibleBy(auth()->user())->count()],
        ]);
    }

    /**
     * Afficher les statistiques des dossiers
     */
    public function assignments() : JsonResponse
    {
        // $this->authorize('viewAny', Assignment::class);

        return $this->responseSuccess(null, [
            // 'total_assignments' => ['value' => Assignment::accessibleBy(auth()->user())->count()],
            // 'open_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::OPENED)->first()->id)->accessibleBy(auth()->user())->count()],
            // 'realized_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::REALIZED)->first()->id)->accessibleBy(auth()->user())->count()],
            // 'edited_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::EDITED)->first()->id)->accessibleBy(auth()->user())->count()],
            // 'validated_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::VALIDATED)->first()->id)->accessibleBy(auth()->user())->count()],
            // 'paid_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::PAID)->first()->id)->accessibleBy(auth()->user())->count()],
            // 'closed_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::CLOSED)->first()->id)->accessibleBy(auth()->user())->count()],
            // 'cancelled_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::CANCELLED)->first()->id)->accessibleBy(auth()->user())->count()],

            'assignments_edition_time_expired' => Assignment::where('created_at', '<', Carbon::now()->subHours(24))->accessibleBy(auth()->user())->count(),
            'assignments_recovery_time_expired' => Assignment::where('created_at', '<', Carbon::now()->subHours(48))->accessibleBy(auth()->user())->count(),

            'total_assignments' => ['value' => Assignment::accessibleBy(auth()->user())->count()],
            'total_insurer_assignments' => ['value' => Assignment::where('assignment_type_id', AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()?->id)->accessibleBy(auth()->user())->count()],
            'total_particular_assignments' => ['value' => Assignment::where('assignment_type_id', AssignmentType::where('code', AssignmentTypeEnum::PARTICULAR)->first()?->id)->accessibleBy(auth()->user())->count()],
            'total_taxi_assignments' => ['value' => Assignment::where('assignment_type_id', AssignmentType::where('code', AssignmentTypeEnum::TAXI)->first()?->id)->accessibleBy(auth()->user())->count()],
            'total_evaluation_assignments' => ['value' => Assignment::where('assignment_type_id', AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()?->id)->accessibleBy(auth()->user())->count()],
            'total_against_expertise_assignments' => ['value' => Assignment::where('assignment_type_id', AssignmentType::where('code', AssignmentTypeEnum::AGAINST_EXPERTISE)->first()?->id)->accessibleBy(auth()->user())->count()],

            'pending_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id)->accessibleBy(auth()->user())->count()],
            'open_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::OPENED)->first()?->id)->accessibleBy(auth()->user())->count()],
            'realized_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::REALIZED)->first()?->id)->accessibleBy(auth()->user())->count()],
            'pending_for_repairer_quote_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE)->first()?->id)->accessibleBy(auth()->user())->count()],
            'pending_for_repairer_quote_validation_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE_VALIDATION)->first()?->id)->accessibleBy(auth()->user())->count()],
            'in_editing_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::IN_EDITING)->first()?->id)->accessibleBy(auth()->user())->count()],
            'in_first_correction_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::IN_FIRST_CORRECTION)->first()?->id)->accessibleBy(auth()->user())->count()],
            'in_second_correction_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::IN_SECOND_CORRECTION)->first()?->id)->accessibleBy(auth()->user())->count()],
            'for_correction_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::FOR_CORRECTION)->first()?->id)->accessibleBy(auth()->user())->count()],
            'pending_for_repairer_validation_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_VALIDATION)->first()?->id)->accessibleBy(auth()->user())->count()],
            'pending_for_expert_validation_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::PENDING_FOR_EXPERT_VALIDATION)->first()?->id)->accessibleBy(auth()->user())->count()],
            'validated_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::VALIDATED)->first()?->id)->accessibleBy(auth()->user())->count()],
            'paid_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::PAID)->first()?->id)->accessibleBy(auth()->user())->count()],
            'closed_assignments' => ['value' => Assignment::where('status_id', Status::where('code', StatusEnum::CLOSED)->first()?->id)->accessibleBy(auth()->user())->count()],
        ]);
    }

    /**
     * Afficher les statistiques des dossiers par type de dossier
     */
    public function assignmentsByAssignmentType() : JsonResponse
    {
        // $this->authorize('viewAny', Assignment::class);

        // Récupérer les paramètres de date de début et de fin (optionnels)
        $start_date = request()->filled('start_date') ? Carbon::parse(request()->start_date)->startOfDay() : null;
        $end_date   = request()->filled('end_date') ? Carbon::parse(request()->end_date)->endOfDay() : null;

        // Récupérer tous les statuts une seule fois pour optimiser les performances
        $statusIds = [
            'PENDING_FOR_OPENING' => Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()?->id,
            'OPENED' => Status::where('code', StatusEnum::OPENED)->first()?->id,
            'REALIZED' => Status::where('code', StatusEnum::REALIZED)->first()?->id,
            'PENDING_FOR_REPAIRER_QUOTE' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE)->first()?->id,
            'PENDING_FOR_REPAIRER_QUOTE_VALIDATION' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE_VALIDATION)->first()?->id,
            'IN_EDITING' => Status::where('code', StatusEnum::IN_EDITING)->first()?->id,
            'IN_FIRST_CORRECTION' => Status::where('code', StatusEnum::IN_FIRST_CORRECTION)->first()?->id,
            'IN_SECOND_CORRECTION' => Status::where('code', StatusEnum::IN_SECOND_CORRECTION)->first()?->id,
            'FOR_CORRECTION' => Status::where('code', StatusEnum::FOR_CORRECTION)->first()?->id,
            'PENDING_FOR_REPAIRER_VALIDATION' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_VALIDATION)->first()?->id,
            'PENDING_FOR_EXPERT_VALIDATION' => Status::where('code', StatusEnum::PENDING_FOR_EXPERT_VALIDATION)->first()?->id,
            'VALIDATED' => Status::where('code', StatusEnum::VALIDATED)->first()?->id,
            'PAID' => Status::where('code', StatusEnum::PAID)->first()?->id,
            'CLOSED' => Status::where('code', StatusEnum::CLOSED)->first()?->id,
            'CANCELLED' => Status::where('code', StatusEnum::CANCELLED)->first()?->id,
            'CLASSIFIED_WITHOUT_FURTHER_ACTION' => Status::where('code', StatusEnum::CLASSIFIED_WITHOUT_FURTHER_ACTION)->first()?->id,
        ];

        $assignmentTypes = AssignmentType::all();
        $statistics = [];

        foreach ($assignmentTypes as $assignmentType) {
            $getBaseQuery = function() use ($assignmentType, $end_date, $start_date) {
                $query = Assignment::where('assignment_type_id', $assignmentType->id)
                    ->accessibleBy(auth()->user());

                // Appliquer le filtre de date de début si fourni
                if ($end_date) {
                    $query->where('created_at', '>=', $end_date);
                }

                // Appliquer le filtre de date de fin si fourni
                if ($start_date) {
                    $query->where('created_at', '<=', $start_date);
                }

                return $query;
            };

            $statistics[$assignmentType->label] = [
                'assignment_type' => [
                    'id' => $assignmentType->id,
                    'code' => $assignmentType->code,
                    'label' => $assignmentType->label,
                    'description' => $assignmentType->description,
                ],
                'total_assignments' => ['value' => $getBaseQuery()->count()],
                'pending_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_OPENING'])->count()],
                'open_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['OPENED'])->count()],
                'realized_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['REALIZED'])->count()],
                'pending_for_repairer_quote_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_REPAIRER_QUOTE'])->count()],
                'pending_for_repairer_quote_validation_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_REPAIRER_QUOTE_VALIDATION'])->count()],
                'in_editing_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['IN_EDITING'])->count()],
                'in_first_correction_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['IN_FIRST_CORRECTION'])->count()],
                'in_second_correction_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['IN_SECOND_CORRECTION'])->count()],
                'for_correction_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['FOR_CORRECTION'])->count()],
                'pending_for_repairer_validation_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_REPAIRER_VALIDATION'])->count()],
                'pending_for_expert_validation_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_EXPERT_VALIDATION'])->count()],
                'validated_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['VALIDATED'])->count()],
                'paid_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PAID'])->count()],
                'closed_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['CLOSED'])->count()],
                'cancelled_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['CANCELLED'])->count()],
                'classified_without_further_action_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['CLASSIFIED_WITHOUT_FURTHER_ACTION'])->count()],
                'assignments_edition_time_expired' => $getBaseQuery()->where('created_at', '<', Carbon::now()->subHours(24))->count(),
                'assignments_recovery_time_expired' => $getBaseQuery()->where('created_at', '<', Carbon::now()->subHours(48))->count(),
            ];
        }

        // Calculer le total avec les mêmes filtres de date
        $totalQuery = Assignment::accessibleBy(auth()->user());
        if ($end_date) {
            $totalQuery->where('created_at', '>=', $end_date);
        }
        if ($start_date) {
            $totalQuery->where('created_at', '<=', $start_date);
        }

        return $this->responseSuccess(null, [
            'total_assignments' => ['value' => $totalQuery->count()],
            'date_filters' => [
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
            ],
            'statistics_by_assignment_type' => $statistics,
        ]);
    }

    /**
     * Afficher les statistiques des dossiers par compagnie d'assurance
     */
    public function assignmentsByInsurer() : JsonResponse
    {
        // $this->authorize('viewAny', Assignment::class);

        // Récupérer les paramètres de date de début et de fin (optionnels)
        $start_date = request()->filled('start_date') ? Carbon::parse(request()->start_date)->startOfDay() : null;
        $end_date   = request()->filled('end_date') ? Carbon::parse(request()->end_date)->endOfDay() : null;

        // Récupérer le paramètre d'ID de l'assureur (optionnel)
        $insurerId = request()->filled('insurer_id') ? request()->insurer_id : null;

        // Récupérer tous les statuts une seule fois pour optimiser les performances
        $statusIds = [
            'PENDING_FOR_OPENING' => Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()?->id,
            'OPENED' => Status::where('code', StatusEnum::OPENED)->first()?->id,
            'REALIZED' => Status::where('code', StatusEnum::REALIZED)->first()?->id,
            'PENDING_FOR_REPAIRER_QUOTE' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE)->first()?->id,
            'PENDING_FOR_REPAIRER_QUOTE_VALIDATION' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE_VALIDATION)->first()?->id,
            'IN_EDITING' => Status::where('code', StatusEnum::IN_EDITING)->first()?->id,
            'IN_FIRST_CORRECTION' => Status::where('code', StatusEnum::IN_FIRST_CORRECTION)->first()?->id,
            'IN_SECOND_CORRECTION' => Status::where('code', StatusEnum::IN_SECOND_CORRECTION)->first()?->id,
            'FOR_CORRECTION' => Status::where('code', StatusEnum::FOR_CORRECTION)->first()?->id,
            'PENDING_FOR_REPAIRER_VALIDATION' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_VALIDATION)->first()?->id,
            'PENDING_FOR_EXPERT_VALIDATION' => Status::where('code', StatusEnum::PENDING_FOR_EXPERT_VALIDATION)->first()?->id,
            'VALIDATED' => Status::where('code', StatusEnum::VALIDATED)->first()?->id,
            'PAID' => Status::where('code', StatusEnum::PAID)->first()?->id,
            'CLOSED' => Status::where('code', StatusEnum::CLOSED)->first()?->id,
            'CANCELLED' => Status::where('code', StatusEnum::CANCELLED)->first()?->id,
            'CLASSIFIED_WITHOUT_FURTHER_ACTION' => Status::where('code', StatusEnum::CLASSIFIED_WITHOUT_FURTHER_ACTION)->first()?->id,
        ];

        $insurerTypeId = EntityType::where('code', EntityTypeEnum::INSURER)->first()?->id;
        $brokerTypeId = EntityType::where('code', EntityTypeEnum::BROKER)->first()?->id;

        // Récupérer les assureurs et les courtiers
        $entitiesQuery = Entity::whereIn('entity_type_id', [$insurerTypeId, $brokerTypeId])
            ->accessibleBy(auth()->user());

        // Filtrer par ID d'assureur/courtier si fourni
        if ($insurerId !== null) {
            $entitiesQuery->where('id', $insurerId);
        }

        $insurers = $entitiesQuery->get();

        $statistics = [];

        foreach ($insurers as $insurer) {
            $getBaseQuery = function() use ($insurer, $start_date, $end_date) {
                $query = Assignment::where('insurer_id', $insurer->id)->where('assignment_type_id', AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()->id)
                    ->accessibleBy(auth()->user());

                // Appliquer le filtre de date de début si fourni
                if ($start_date) {
                    $query->where('created_at', '>=', $start_date);
                }

                // Appliquer le filtre de date de fin si fourni
                if ($end_date) {
                    $query->where('created_at', '<=', $end_date);
                }

                return $query;
            };

            $statistics[$insurer->name] = [
                'insurer' => [
                    'id' => $insurer->id,
                    'name' => $insurer->name ?? null,
                    'code' => $insurer->code ?? null,
                ],
                'total_assignments' => ['value' => $getBaseQuery()->count()],
                'pending_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_OPENING'])->count()],
                'open_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['OPENED'])->count()],
                'realized_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['REALIZED'])->count()],
                'pending_for_repairer_quote_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_REPAIRER_QUOTE'])->count()],
                'pending_for_repairer_quote_validation_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_REPAIRER_QUOTE_VALIDATION'])->count()],
                'in_editing_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['IN_EDITING'])->count()],
                'in_first_correction_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['IN_FIRST_CORRECTION'])->count()],
                'in_second_correction_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['IN_SECOND_CORRECTION'])->count()],
                'for_correction_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['FOR_CORRECTION'])->count()],
                'pending_for_repairer_validation_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_REPAIRER_VALIDATION'])->count()],
                'pending_for_expert_validation_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_EXPERT_VALIDATION'])->count()],
                'validated_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['VALIDATED'])->count()],
                'paid_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PAID'])->count()],
                'closed_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['CLOSED'])->count()],
                'cancelled_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['CANCELLED'])->count()],
                'classified_without_further_action_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['CLASSIFIED_WITHOUT_FURTHER_ACTION'])->count()],
                'assignments_edition_time_expired' => $getBaseQuery()->where('created_at', '<', Carbon::now()->subHours(24))->count(),
                'assignments_recovery_time_expired' => $getBaseQuery()->where('created_at', '<', Carbon::now()->subHours(48))->count(),
            ];
        }

        // Calculer le total avec les mêmes filtres (date + assureur)
        $totalQuery = Assignment::accessibleBy(auth()->user());
        if ($insurerId !== null) {
            $totalQuery->where('insurer_id', $insurerId);
        }
        if ($start_date) {
            $totalQuery->where('created_at', '>=', $start_date);
        }
        if ($end_date) {
            $totalQuery->where('created_at', '<=', $end_date);
        }

        return $this->responseSuccess(null, [
            'total_assignments' => ['value' => $totalQuery->count()],
            'filters' => [
                'insurer_id' => $insurerId,
                'start_date' => $start_date ? $start_date->format('Y-m-d') : null,
                'end_date' => $end_date ? $end_date->format('Y-m-d') : null,
            ],
            'statistics_by_insurer' => $statistics,
        ]);
    }

    /**
     * Afficher les statistiques des dossiers par réparateur
     */
    public function assignmentsByRepairer() : JsonResponse
    {
        // $this->authorize('viewAny', Assignment::class);

        // Récupérer les paramètres de date de début et de fin (optionnels)
        $start_date = request()->filled('start_date') ? Carbon::parse(request()->start_date)->startOfDay() : null;
        $end_date   = request()->filled('end_date') ? Carbon::parse(request()->end_date)->endOfDay() : null;

        // Récupérer le paramètre d'ID de réparateur (optionnel)
        $repairerId = request()->filled('repairer_id') ? request()->repairer_id : null;

        // Récupérer tous les statuts une seule fois pour optimiser les performances
        $statusIds = [
            'PENDING_FOR_OPENING' => Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()?->id,
            'OPENED' => Status::where('code', StatusEnum::OPENED)->first()?->id,
            'REALIZED' => Status::where('code', StatusEnum::REALIZED)->first()?->id,
            'PENDING_FOR_REPAIRER_QUOTE' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE)->first()?->id,
            'PENDING_FOR_REPAIRER_QUOTE_VALIDATION' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE_VALIDATION)->first()?->id,
            'IN_EDITING' => Status::where('code', StatusEnum::IN_EDITING)->first()?->id,
            'IN_FIRST_CORRECTION' => Status::where('code', StatusEnum::IN_FIRST_CORRECTION)->first()?->id,
            'IN_SECOND_CORRECTION' => Status::where('code', StatusEnum::IN_SECOND_CORRECTION)->first()?->id,
            'FOR_CORRECTION' => Status::where('code', StatusEnum::FOR_CORRECTION)->first()?->id,
            'PENDING_FOR_REPAIRER_VALIDATION' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_VALIDATION)->first()?->id,
            'PENDING_FOR_EXPERT_VALIDATION' => Status::where('code', StatusEnum::PENDING_FOR_EXPERT_VALIDATION)->first()?->id,
            'VALIDATED' => Status::where('code', StatusEnum::VALIDATED)->first()?->id,
            'PAID' => Status::where('code', StatusEnum::PAID)->first()?->id,
            'CLOSED' => Status::where('code', StatusEnum::CLOSED)->first()?->id,
            'CANCELLED' => Status::where('code', StatusEnum::CANCELLED)->first()?->id,
            'CLASSIFIED_WITHOUT_FURTHER_ACTION' => Status::where('code', StatusEnum::CLASSIFIED_WITHOUT_FURTHER_ACTION)->first()?->id,
        ];

        $repairerTypeId = EntityType::where('code', EntityTypeEnum::REPAIRER)->first()?->id;
        $repairersQuery = Entity::where('entity_type_id', $repairerTypeId)
            ->accessibleBy(auth()->user());

        // Filtrer par ID de réparateur si fourni
        if ($repairerId !== null) {
            $repairersQuery->where('id', $repairerId);
        }

        $repairers = $repairersQuery->get();

        $statistics = [];

        foreach ($repairers as $repairer) {
            $getBaseQuery = function() use ($repairer, $start_date, $end_date) {
                $query = Assignment::whereHas('repairers', fn ($q) => $q->where('entities.id', $repairer->id))
                    ->accessibleBy(auth()->user());

                // Appliquer le filtre de date de début si fourni
                if ($start_date) {
                    $query->where('created_at', '>=', $start_date);
                }

                // Appliquer le filtre de date de fin si fourni
                if ($end_date) {
                    $query->where('created_at', '<=', $end_date);
                }

                return $query;
            };

            $statistics[$repairer->name] = [
                'repairers' => [
                    'id' => $repairer->id,
                    'name' => $repairer->name ?? null,
                    'code' => $repairer->code ?? null,
                ],
                'total_assignments' => ['value' => $getBaseQuery()->count()],
                'pending_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_OPENING'])->count()],
                'open_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['OPENED'])->count()],
                'realized_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['REALIZED'])->count()],
                'pending_for_repairer_quote_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_REPAIRER_QUOTE'])->count()],
                'pending_for_repairer_quote_validation_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_REPAIRER_QUOTE_VALIDATION'])->count()],
                'in_editing_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['IN_EDITING'])->count()],
                'in_first_correction_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['IN_FIRST_CORRECTION'])->count()],
                'in_second_correction_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['IN_SECOND_CORRECTION'])->count()],
                'for_correction_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['FOR_CORRECTION'])->count()],
                'pending_for_repairer_validation_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_REPAIRER_VALIDATION'])->count()],
                'pending_for_expert_validation_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PENDING_FOR_EXPERT_VALIDATION'])->count()],
                'validated_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['VALIDATED'])->count()],
                'paid_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['PAID'])->count()],
                'closed_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['CLOSED'])->count()],
                'cancelled_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['CANCELLED'])->count()],
                'classified_without_further_action_assignments' => ['value' => $getBaseQuery()->where('status_id', $statusIds['CLASSIFIED_WITHOUT_FURTHER_ACTION'])->count()],
                'assignments_edition_time_expired' => $getBaseQuery()->where('created_at', '<', Carbon::now()->subHours(24))->count(),
                'assignments_recovery_time_expired' => $getBaseQuery()->where('created_at', '<', Carbon::now()->subHours(48))->count(),
            ];
        }

        // Calculer le total avec les mêmes filtres de date et de réparateur
        $totalQuery = Assignment::accessibleBy(auth()->user());
        if ($repairerId !== null) {
            $totalQuery->whereHas('repairers', fn ($q) => $q->where('entities.id', $repairerId));
        }
        if ($start_date) {
            $totalQuery->where('created_at', '>=', $start_date);
        }
        if ($end_date) {
            $totalQuery->where('created_at', '<=', $end_date);
        }

        return $this->responseSuccess(null, [
            'total_assignments' => ['value' => $totalQuery->count()],
            'filters' => [
                'repairer_id' => $repairerId,
                'start_date' => $start_date ? $start_date->format('Y-m-d') : null,
                'end_date' => $end_date ? $end_date->format('Y-m-d') : null,
            ],
            'statistics_by_repairer' => $statistics,
        ]);
    }

    /**
     * Afficher les statistiques des dossiers par mois, par année et par type de dossier
     */
    public function assignmentsByMonthYearAndType() : JsonResponse
    {
        // $this->authorize('viewAny', Assignment::class);

        // Récupérer le paramètre d'année (optionnel)
        $selected_year = request()->input('year') ?? request()->input('year');

        // Validation de l'année si fournie
        if ($selected_year !== null) {
            if (!is_numeric($selected_year) || strlen($selected_year) !== 4) {
                return $this->responseUnprocessable('Le format de l\'année est invalide. Format attendu: YYYY (ex: 2026)', null);
            }
            $selected_year = (int) $selected_year;
        }

        // Récupérer les années distinctes qui ont des dossiers
        $queryYears = Assignment::selectRaw('YEAR(created_at) as year')
            ->accessibleBy(auth()->user());

        // Si une année spécifique est demandée, filtrer par cette année
        if ($selected_year !== null) {
            $queryYears->whereYear('created_at', $selected_year);
        }

        $years = $queryYears->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Si une année spécifique est demandée mais n'a pas de données, inclure quand même l'année
        if ($selected_year !== null && !in_array($selected_year, $years)) {
            $years = [$selected_year];
        }

        // Si aucune année trouvée, utiliser l'année actuelle
        if (empty($years)) {
            $years = [$selected_year ?? Carbon::now()->year];
        }

        // Récupérer tous les types de dossiers
        $assignmentTypes = AssignmentType::all();

        // Récupérer les données groupées par année, mois et type
        $queryAssignments = Assignment::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                assignment_type_id,
                COUNT(*) as count
            ')
            ->accessibleBy(auth()->user())
            ->whereNotNull('assignment_type_id');

        // Appliquer le filtre d'année si fourni
        if ($selected_year !== null) {
            $queryAssignments->whereYear('created_at', $selected_year);
        }

        $assignmentsData = $queryAssignments
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'), 'assignment_type_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return ["{$item->year}_{$item->month}_{$item->assignment_type_id}" => $item->count];
            });

        // Construire la structure de résultats
        $statistics = [];

        foreach ($years as $year) {
            $yearData = [
                'year' => $year,
                'total' => 0,
                'months' => []
            ];

            // Pour chaque mois de l'année (1 à 12)
            for ($month = 1; $month <= 12; $month++) {
                $monthData = [
                    'month' => $month,
                    'month_name' => Carbon::create($year, $month, 1)->locale('fr')->translatedFormat('F'),
                    'total' => 0,
                    'by_assignment_type' => []
                ];

                // Pour chaque type de dossier
                foreach ($assignmentTypes as $assignmentType) {
                    $count = 0;

                    // Vérifier si on a des données pour cette combinaison année/mois/type
                    $key = "{$year}_{$month}_{$assignmentType->id}";
                    if (isset($assignmentsData[$key])) {
                        $count = $assignmentsData[$key];
                    }

                    $monthData['by_assignment_type'][] = [
                        'assignment_type_id' => $assignmentType->id,
                        'assignment_type_code' => $assignmentType->code,
                        'assignment_type_label' => $assignmentType->label,
                        'count' => $count
                    ];

                    $monthData['total'] += $count;
                }

                $yearData['total'] += $monthData['total'];
                $yearData['months'][] = $monthData;
            }

            $statistics[] = $yearData;
        }

        // Calculer le total avec le filtre d'année si fourni
        $totalQuery = Assignment::accessibleBy(auth()->user());
        if ($selected_year !== null) {
            $totalQuery->whereYear('created_at', $selected_year);
        }

        return $this->responseSuccess(null, [
            'total_assignments' => ['value' => $totalQuery->count()],
            'year_filter' => $selected_year,
            'statistics_by_year_month_and_type' => $statistics,
        ]);
    }

    /**
     * Afficher les statistiques des dossiers par mois, par année et par statut de dossier
     */
    public function assignmentsByMonthYearAndStatus() : JsonResponse
    {
        // $this->authorize('viewAny', Assignment::class);

        // Récupérer le paramètre d'année (optionnel)
        $selected_year = request()->input('year');

        // Validation de l'année si fournie
        if ($selected_year !== null) {
            if (!is_numeric($selected_year) || strlen($selected_year) !== 4) {
                return $this->responseUnprocessable('Le format de l\'année est invalide. Format attendu: YYYY (ex: 2026)', null);
            }
            $selected_year = (int) $selected_year;
        }

        // Récupérer les années distinctes qui ont des dossiers
        $queryYears = Assignment::selectRaw('YEAR(created_at) as year')
            ->accessibleBy(auth()->user());

        // Si une année spécifique est demandée, filtrer par cette année
        if ($selected_year !== null) {
            $queryYears->whereYear('created_at', $selected_year);
        }

        $years = $queryYears->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Si une année spécifique est demandée mais n'a pas de données, inclure quand même l'année
        if ($selected_year !== null && !in_array($selected_year, $years)) {
            $years = [$selected_year];
        }

        // Si aucune année trouvée, utiliser l'année actuelle
        if (empty($years)) {
            $years = [$selected_year ?? Carbon::now()->year];
        }

        // Récupérer tous les statuts de dossiers (statuts liés aux assignments)
        $statusIds = [
            'PENDING_FOR_OPENING' => Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()?->id,
            'OPENED' => Status::where('code', StatusEnum::OPENED)->first()?->id,
            'REALIZED' => Status::where('code', StatusEnum::REALIZED)->first()?->id,
            'PENDING_FOR_REPAIRER_QUOTE' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE)->first()?->id,
            'PENDING_FOR_REPAIRER_QUOTE_VALIDATION' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE_VALIDATION)->first()?->id,
            'IN_EDITING' => Status::where('code', StatusEnum::IN_EDITING)->first()?->id,
            'IN_FIRST_CORRECTION' => Status::where('code', StatusEnum::IN_FIRST_CORRECTION)->first()?->id,
            'IN_SECOND_CORRECTION' => Status::where('code', StatusEnum::IN_SECOND_CORRECTION)->first()?->id,
            'FOR_CORRECTION' => Status::where('code', StatusEnum::FOR_CORRECTION)->first()?->id,
            'PENDING_FOR_REPAIRER_VALIDATION' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_VALIDATION)->first()?->id,
            'PENDING_FOR_EXPERT_VALIDATION' => Status::where('code', StatusEnum::PENDING_FOR_EXPERT_VALIDATION)->first()?->id,
            'VALIDATED' => Status::where('code', StatusEnum::VALIDATED)->first()?->id,
            'PAID' => Status::where('code', StatusEnum::PAID)->first()?->id,
            'CLOSED' => Status::where('code', StatusEnum::CLOSED)->first()?->id,
            'CANCELLED' => Status::where('code', StatusEnum::CANCELLED)->first()?->id,
            'CLASSIFIED_WITHOUT_FURTHER_ACTION' => Status::where('code', StatusEnum::CLASSIFIED_WITHOUT_FURTHER_ACTION)->first()?->id,
        ];

        // Récupérer tous les statuts avec leurs informations
        $statuses = Status::whereIn('id', array_filter($statusIds))
            ->get();

        // Récupérer les données groupées par année, mois et statut
        $queryAssignments = Assignment::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                status_id,
                COUNT(*) as count
            ')
            ->accessibleBy(auth()->user())
            ->whereNotNull('status_id');

        // Appliquer le filtre d'année si fourni
        if ($selected_year !== null) {
            $queryAssignments->whereYear('created_at', $selected_year);
        }

        $assignmentsData = $queryAssignments
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'), 'status_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return ["{$item->year}_{$item->month}_{$item->status_id}" => $item->count];
            });

        // Construire la structure de résultats
        $statistics = [];

        foreach ($years as $year) {
            $yearData = [
                'year' => $year,
                'total' => 0,
                'months' => []
            ];

            // Pour chaque mois de l'année (1 à 12)
            for ($month = 1; $month <= 12; $month++) {
                $monthData = [
                    'month' => $month,
                    'month_name' => Carbon::create($year, $month, 1)->locale('fr')->translatedFormat('F'),
                    'total' => 0,
                    'by_status' => []
                ];

                // Pour chaque statut
                foreach ($statuses as $status) {
                    $count = 0;

                    // Vérifier si on a des données pour cette combinaison année/mois/statut
                    $key = "{$year}_{$month}_{$status->id}";
                    if (isset($assignmentsData[$key])) {
                        $count = $assignmentsData[$key];
                    }

                    $monthData['by_status'][] = [
                        'status_id' => $status->id,
                        'status_code' => $status->code,
                        'status_label' => $status->label ?? $status->code,
                        'count' => $count
                    ];

                    $monthData['total'] += $count;
                }

                $yearData['total'] += $monthData['total'];
                $yearData['months'][] = $monthData;
            }

            $statistics[] = $yearData;
        }

        // Calculer le total avec le filtre d'année si fourni
        $totalQuery = Assignment::accessibleBy(auth()->user());
        if ($selected_year !== null) {
            $totalQuery->whereYear('created_at', $selected_year);
        }

        return $this->responseSuccess(null, [
            'total_assignments' => ['value' => $totalQuery->count()],
            'year_filter' => $selected_year,
            'statistics_by_year_month_and_status' => $statistics,
        ]);
    }

    /**
     * Afficher les statistiques des compagnies d'assurance
     */
    public function insurers() : JsonResponse
    {
        // $this->authorize('viewAny', User::class);

        return $this->responseSuccess(null, [
            'total_insurers' => ['value' => Entity::where('entity_type_id', EntityType::where('code', EntityTypeEnum::INSURER)->first()->id)->accessibleBy(auth()->user())->count()],
            'active_insurers' => ['value' => Entity::where('entity_type_id', EntityType::where('code', EntityTypeEnum::INSURER)->first()->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->accessibleBy(auth()->user())->count()],
            'inactive_insurers' => ['value' => Entity::where('entity_type_id', EntityType::where('code', EntityTypeEnum::INSURER)->first()->id)->where('status_id', Status::where('code', StatusEnum::INACTIVE)->first()->id)->accessibleBy(auth()->user())->count()],
        ]);
    }

    /**
     * Afficher les statistiques des courtiers en assurances
     */
    public function brokers() : JsonResponse
    {
        // $this->authorize('viewAny', User::class);

        return $this->responseSuccess(null, [
            'total_brokers' => ['value' => Entity::where('entity_type_id', EntityType::where('code', EntityTypeEnum::BROKER)->first()->id)->accessibleBy(auth()->user())->count()],
            'active_brokers' => ['value' => Entity::where('entity_type_id', EntityType::where('code', EntityTypeEnum::BROKER)->first()->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->accessibleBy(auth()->user())->count()],
            'inactive_brokers' => ['value' => Entity::where('entity_type_id', EntityType::where('code', EntityTypeEnum::BROKER)->first()->id)->where('status_id', Status::where('code', StatusEnum::INACTIVE)->first()->id)->accessibleBy(auth()->user())->count()],
        ]);
    }

    /**
     * Afficher les statistiques des réparateurs
     */
    public function repairers() : JsonResponse
    {
        // $this->authorize('viewAny', User::class);

        return $this->responseSuccess(null, [
            'total_repairers' => ['value' => Entity::where('entity_type_id', EntityType::where('code', EntityTypeEnum::REPAIRER)->first()->id)->accessibleBy(auth()->user())->count()],
            'active_repairers' => ['value' => Entity::where('entity_type_id', EntityType::where('code', EntityTypeEnum::REPAIRER)->first()->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->accessibleBy(auth()->user())->count()],
            'inactive_repairers' => ['value' => Entity::where('entity_type_id', EntityType::where('code', EntityTypeEnum::REPAIRER)->first()->id)->where('status_id', Status::where('code', StatusEnum::INACTIVE)->first()->id)->accessibleBy(auth()->user())->count()],
        ]);
    }

    /**
     * Afficher les statistiques des véhicules
     */
    public function vehicles() : JsonResponse
    {
        // $this->authorize('viewAny', User::class);

        return $this->responseSuccess(null, [
            'total_vehicles' => ['value' => Vehicle::count()],
            'active_vehicles' => ['value' => Vehicle::where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->count()],
            'inactive_vehicles' => ['value' => Vehicle::where('status_id', Status::where('code', StatusEnum::INACTIVE)->first()->id)->count()],
        ]);
    }
}
