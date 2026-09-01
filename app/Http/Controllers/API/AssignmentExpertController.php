<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignmentExpert\UpdateAssignmentExpertRequest;
use App\Http\Requests\AssignmentExpert\CreateAssignmentExpertRequest;
use App\Http\Resources\AssignmentExpert\AssignmentExpertResource;
use App\Models\AssignmentExpert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Essa\APIToolKit\Api\ApiResponse;

/**
 * @group Gestion des dossiers assignés à des experts
 *
 * APIs pour la gestion des dossiers assignés à des experts
 */
class AssignmentExpertController extends Controller
{
    use ApiResponse;

    public function __construct()
    {

    }

    /**
     * Lister tous les experts assignés à un dossier
     *
     * @authenticated
     */
    public function index(): AnonymousResourceCollection
    {
        $assignmentExperts = AssignmentExpert::with('assignment:id,reference', 'expert:id,name')->useFilters()->dynamicPaginate();

        return AssignmentExpertResource::collection($assignmentExperts);
    }

    /**
     * Ajouter un expert à un dossier
     *
     * @authenticated
     */
    public function store(CreateAssignmentExpertRequest $request): JsonResponse
    {
        $assignmentExpert = AssignmentExpert::create([
            'assignment_id' => $request->assignment_id,
            'expert_id' => $request->expert_id,
            'date' => $request->date,
            'observation' => $request->observation,
        ]);

        return $this->responseCreated('Opération effectuée avec succès', new AssignmentExpertResource($assignmentExpert));
    }

    /**
     * Afficher un expert assigné à un dossier
     *
     * @authenticated
     */
    public function show($id): JsonResponse
    {
            $assignmentExpert = AssignmentExpert::with('assignment:id,reference', 'expert:id,name')->findOrFail($id);
        return $this->responseSuccess(null, new AssignmentExpertResource($assignmentExpert));
    }

    /**
     * Mettre à jour un expert assigné à un dossier
     *
     * @authenticated
     */
    public function update(UpdateAssignmentExpertRequest $request, $id): JsonResponse
    {       
        $assignmentExpert = AssignmentExpert::findOrFail($id);
        $assignmentExpert->update([
            'expert_id' => $request->expert_id,
            'date' => $request->date,
            'observation' => $request->observation,
        ]);
        return $this->responseCreated('Opération effectuée avec succès', new AssignmentExpertResource($assignmentExpert));
    }

    /**
     * Supprimer un expert assigné à un dossier
     *
     * @authenticated
     */
    public function destroy($id): JsonResponse
    {
        $assignmentExpert = AssignmentExpert::findOrFail($id);
        $assignmentExpert->delete();
        return $this->responseCreated('Opération effectuée avec succès', null);
    }
}
