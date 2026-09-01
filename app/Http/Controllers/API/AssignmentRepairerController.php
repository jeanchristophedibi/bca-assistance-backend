<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignmentRepairer\CreateAssignmentRepairerRequest;
use App\Http\Requests\AssignmentRepairer\UpdateAssignmentRepairerRequest;
use App\Http\Resources\AssignmentRepairer\AssignmentRepairerResource;
use App\Models\AssignmentRepairer;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Gestion des réparateurs assignés à un dossier
 *
 * APIs pour la gestion des réparateurs liés aux dossiers
 */
class AssignmentRepairerController extends Controller
{
    use ApiResponse;

    /**
     * Lister les réparateurs liés aux dossiers
     *
     * @authenticated
     */
    public function index(): AnonymousResourceCollection
    {
        $assignmentRepairers = AssignmentRepairer::with('assignment:id,reference', 'repairers')
            ->useFilters()
            ->dynamicPaginate();

        return AssignmentRepairerResource::collection($assignmentRepairers);
    }

    /**
     * Ajouter un réparateur à un dossier
     *
     * @authenticated
     */
    public function store(CreateAssignmentRepairerRequest $request): JsonResponse
    {
        $assignmentRepairer = AssignmentRepairer::create([
            'assignment_id' => $request->assignment_id,
            'repairer_id' => $request->repairer_id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return $this->responseCreated('Opération effectuée avec succès', new AssignmentRepairerResource($assignmentRepairer->load('repairers')));
    }

    /**
     * Afficher un réparateur lié à un dossier
     *
     * @authenticated
     */
    public function show($id): JsonResponse
    {
        $assignmentRepairer = AssignmentRepairer::with('assignment:id,reference', 'repairers')->findOrFail($id);

        return $this->responseSuccess(null, new AssignmentRepairerResource($assignmentRepairer));
    }

    /**
     * Mettre à jour un réparateur lié à un dossier
     *
     * @authenticated
     */
    public function update(UpdateAssignmentRepairerRequest $request, $id): JsonResponse
    {
        $assignmentRepairer = AssignmentRepairer::findOrFail($id);
        $assignmentRepairer->update([
            'repairer_id' => $request->repairer_id,
            'updated_by' => auth()->id(),
        ]);

        return $this->responseSuccess('Opération effectuée avec succès', new AssignmentRepairerResource($assignmentRepairer->load('repairers')));
    }

    /**
     * Supprimer un réparateur lié à un dossier
     *
     * @authenticated
     */
    public function destroy($id): JsonResponse
    {
        $assignmentRepairer = AssignmentRepairer::findOrFail($id);
        $assignmentRepairer->update([
            'deleted_by' => auth()->id(),
        ]);
        $assignmentRepairer->delete();

        return $this->responseDeleted();
    }
}
