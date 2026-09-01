<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignmentRequestRepairer\CreateAssignmentRequestRepairerRequest;
use App\Http\Requests\AssignmentRequestRepairer\UpdateAssignmentRequestRepairerRequest;
use App\Http\Resources\AssignmentRequestRepairer\AssignmentRequestRepairerResource;
use App\Models\AssignmentRequestRepairer;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Gestion des réparateurs liés à une demande d'expertise
 *
 * APIs pour la gestion des réparateurs liés aux demandes d'expertise
 */
class AssignmentRequestRepairerController extends Controller
{
    use ApiResponse;

    /**
     * Lister les réparateurs liés aux demandes d'expertise
     *
     * @authenticated
     */
    public function index(): AnonymousResourceCollection
    {
        $assignmentRequestRepairers = AssignmentRequestRepairer::with('assignmentRequest:id,reference', 'repairers')
            ->useFilters()
            ->dynamicPaginate();

        return AssignmentRequestRepairerResource::collection($assignmentRequestRepairers);
    }

    /**
     * Ajouter un réparateur à une demande d'expertise
     *
     * @authenticated
     */
    public function store(CreateAssignmentRequestRepairerRequest $request): JsonResponse
    {
        $assignmentRequestRepairer = AssignmentRequestRepairer::create([
            'assignment_request_id' => $request->assignment_request_id,
            'repairer_id' => $request->repairer_id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return $this->responseCreated('Opération effectuée avec succès', new AssignmentRequestRepairerResource($assignmentRequestRepairer->load('repairers')));
    }

    /**
     * Afficher un réparateur lié à une demande d'expertise
     *
     * @authenticated
     */
    public function show($id): JsonResponse
    {
        $assignmentRequestRepairer = AssignmentRequestRepairer::with('assignmentRequest:id,reference', 'repairers')->findOrFail($id);

        return $this->responseSuccess(null, new AssignmentRequestRepairerResource($assignmentRequestRepairer));
    }

    /**
     * Mettre à jour un réparateur lié à une demande d'expertise
     *
     * @authenticated
     */
    public function update(UpdateAssignmentRequestRepairerRequest $request, $id): JsonResponse
    {
        $assignmentRequestRepairer = AssignmentRequestRepairer::findOrFail($id);
        $assignmentRequestRepairer->update([
            'repairer_id' => $request->repairer_id,
            'updated_by' => auth()->id(),
        ]);

        return $this->responseSuccess('Opération effectuée avec succès', new AssignmentRequestRepairerResource($assignmentRequestRepairer->load('repairers')));
    }

    /**
     * Supprimer un réparateur lié à une demande d'expertise
     *
     * @authenticated
     */
    public function destroy($id): JsonResponse
    {
        $assignmentRequestRepairer = AssignmentRequestRepairer::findOrFail($id);
        $assignmentRequestRepairer->update([
            'deleted_by' => auth()->id(),
        ]);
        $assignmentRequestRepairer->delete();

        return $this->responseDeleted();
    }
}
