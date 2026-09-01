<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntityEmail\UpdateEntityEmailRequest;
use App\Http\Requests\EntityEmail\CreateEntityEmailRequest;
use App\Http\Resources\EntityEmail\EntityEmailResource;
use App\Models\EntityEmail;
use App\Models\Status;
use App\Enums\StatusEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Carbon\Carbon;
use Essa\APIToolKit\Api\ApiResponse;


/**
 * @group Gestion des emails des entités
 *
 * APIs pour la gestion des emails des entités
 */
class EntityEmailController extends Controller
{
    use ApiResponse;

    public function __construct()
    {

    }

    /**
     * Lister tous les emails des entités
     *
     * @authenticated
     */
    public function index(): AnonymousResourceCollection
    {
        $entityEmails = EntityEmail::with('entity', 'status', 'createdBy', 'updatedBy', 'deletedBy')
                ->accessibleBy(auth()->user())
                ->useFilters()
                ->latest('created_at')
                ->dynamicPaginate();

        return EntityEmailResource::collection($entityEmails);
    }

    /**
     * Créer un email d'entité
     *
     * @authenticated
     */
    public function store(CreateEntityEmailRequest $request): JsonResponse
    {
        $entityEmail = EntityEmail::create([
            'email' => $request->email,
            'entity_id' => $request->entity_id,
            'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
        ]);

        return $this->responseCreated('EntityEmail created successfully', new EntityEmailResource($entityEmail));
    }

    /**
     * Afficher un email d'entité
     *
     * @authenticated
     */
    public function show($id): JsonResponse
    {
        $entityEmail = EntityEmail::with('entity', 'status', 'createdBy', 'updatedBy', 'deletedBy')
            ->accessibleBy(auth()->user())
            ->findOrFail($id);

        return $this->responseSuccess(null, new EntityEmailResource($entityEmail));
    }

    /**
     * Mettre à jour un email d'entité
     *
     * @authenticated
     */
    public function update(UpdateEntityEmailRequest $request, $id): JsonResponse
    {
        $entityEmail = EntityEmail::accessibleBy(auth()->user())->findOrFail($id);
        $entityEmail->update([
            'email' => $request->email,
            'entity_id' => $request->entity_id,
            'updated_by' => auth()->user()->id,
        ]);

        return $this->responseSuccess('EntityEmail updated Successfully', new EntityEmailResource($entityEmail));
    }

    /**
     * Activer un email d'entité
     *
     * @authenticated
     */
    public function enable($id): JsonResponse
    {
        $entityEmail = EntityEmail::accessibleBy(auth()->user())->findOrFail($id);
        $entityEmail->update([
            'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        return $this->responseSuccess('EntityEmail enabled Successfully', null);
    }

    /**
     * Désactiver un email d'entité
     *
     * @authenticated
     */
    public function disable($id): JsonResponse
    {
        $entityEmail = EntityEmail::accessibleBy(auth()->user())->findOrFail($id);
        $entityEmail->update([
            'status_id' => Status::where('code', StatusEnum::INACTIVE)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        return $this->responseSuccess('EntityEmail disabled Successfully', null);
    }

    /**
     * Supprimer un email d'entité
     *
     * @authenticated
     */
    public function destroy($id): JsonResponse
    {
        $entityEmail = EntityEmail::accessibleBy(auth()->user())->findOrFail($id);
        $entityEmail->update([
            'status_id' => Status::where('code', StatusEnum::DELETED)->first()->id,
            'deleted_by' => auth()->user()->id,
            'deleted_at' => Carbon::now(),
        ]);

        $entityEmail->delete();

        return $this->responseSuccess('EntityEmail deleted Successfully', null);
    }

}
