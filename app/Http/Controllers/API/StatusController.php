<?php

namespace App\Http\Controllers\API;

use App\Models\Status;
use App\Enums\StatusEnum;
use App\Models\Assignment;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Actions\Status\CreateStatusAction;
use App\Http\Resources\Status\StatusResource;
use App\Http\Requests\Status\CreateStatusRequest;
use App\Http\Requests\Status\UpdateStatusRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * @group Gestion des statuts
 *
 * APIs pour la gestion des statuts
 */
class StatusController extends Controller
{
    use ApiResponse;

    public function __construct()
    {

    }

    // Lister tous les statuts
    /**
     * Lister tous les statuts
     *
     * @authenticated
     */
    public function index(): AnonymousResourceCollection
    {
        $statuses = Status::useFilters()->dynamicPaginate();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissionsData = [
            ['name' => \App\Enums\PermissionEnum::COST_SUPPLY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CALCULATE_DEPRECIATION_TABLE, 'guard_name' => 'sanctum'],
        ];

        // Upsert permissions
        Permission::upsert(
            $permissionsData,
            ['name', 'guard_name'], // clé unique
            [] // pas de champs à mettre à jour
        );

        return StatusResource::collection($statuses);
    }

    /**
     * Ajouter un statut
     *
     * @authenticated
     */
    public function store(CreateStatusRequest $request, CreateStatusAction $createStatus): JsonResponse
    {
        $status = $createStatus->execute($request->validated());

        return $this->responseCreated('Status created successfully', new StatusResource($status));
    }

    /**
     * Afficher un statut
     *
     * @authenticated
     */
    public function show($id): JsonResponse
    {
        $status = Status::findOrFail($id);

        return $this->responseSuccess(null, new StatusResource($status));
    }

    /**
     * Mettre à jour un statut
     *
     * @authenticated
     */
    public function update(UpdateStatusRequest $request, $id): JsonResponse
    {
        $status = Status::findOrFail($id);
        $status->update([
            'label' => $request->label,
            'description' => $request->description,
            'updated_by' => auth()->user()->id,
        ]);

        return $this->responseSuccess('Status updated Successfully', new StatusResource($status));
    }

    /**
     * Supprimer un statut
     *
     * @authenticated
     */
    public function destroy($id): JsonResponse
    {
        $status = Status::findOrFail($id);

        // $status->delete();

        return $this->responseDeleted();
    }

   
}
