<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Photo;
use App\Models\Status;
use App\Enums\StatusEnum;
use App\Models\AssignmentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Jobs\GenerateAssignmentRequestPdfJob;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\AssignmentRequest\AssignmentRequestResource;
use App\Http\Requests\AssignmentRequest\AcceptAssignmentRequestRequest;
use App\Http\Requests\AssignmentRequest\CancelAssignmentRequestRequest;
use App\Http\Requests\AssignmentRequest\CreateAssignmentRequestRequest;
use App\Http\Requests\AssignmentRequest\RejectAssignmentRequestRequest;
use App\Http\Requests\AssignmentRequest\UpdateAssignmentRequestRequest;
use App\Http\Requests\AssignmentRequest\AddPhotosToAssignmentRequestRequest;

/**
 * @group Gestion des demandes d'expertise
 *
 * APIs pour la gestion des demandes d'expertise
 */
class AssignmentRequestController extends Controller
{
    use ApiResponse;

    public function __construct()
    {

    }

    /**
     * Lister toutes les demandes d'expertise
     *
     * @authenticated
     */
    public function index(): AnonymousResourceCollection
    {
        $assignmentRequests = AssignmentRequest::with('status', 'deletedBy', 'client', 'vehicle.brand', 'vehicle.vehicleModel', 'repairers', 'createdBy', 'updatedBy')
                                    ->withCount('photos')
                                    ->accessibleBy(auth()->user())
                                    ->useFilters()
                                    ->latest('created_at')
                                    ->dynamicPaginate();

        return AssignmentRequestResource::collection($assignmentRequests);
    }

    /**
     * Créer une demande d'expertise
     *
     * @authenticated
     */
    public function store(CreateAssignmentRequestRequest $request): JsonResponse
    {
        $now = Carbon::now();
        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;

        $assignmentRequest = AssignmentRequest::create([
            'reference' => 'REQ'.$today,
            'client_id' => auth()->user()->client_id,
            'vehicle_id' => $request->vehicle_id,
            'date' => $request->date,
            'assistance_place' => $request->assistance_place,
            'observation' => $request->observation,
            'status_id' => Status::where('code', StatusEnum::PENDING)->first()->id,
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
        ]);

        $repairers = $request->has('repairers')
            ? $request->repairers
            : ($request->filled('repairer_id') ? [['repairer_id' => $request->repairer_id]] : null);
        $assignmentRequest->syncRepairers($repairers, auth()->id());
        $files = [];
        if($request->hasfile('photos'))
        {
            $count = 0;
            if($request->file('photos') && $request->hasfile('photos')){
                foreach($request->file('photos') as $file)
                {
                    $count = $count + 1;
                    $name = 'IMG_BP'.$today.'_'.$count.'.png';
                    $file->move(public_path("storage/photos/assignment_request/{$assignmentRequest->reference}"), $name);

                    $photo = Photo::create([
                        'name' => $name,
                        'assignment_request_id' => $assignmentRequest->id,
                        'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                        'created_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id,
                    ]);
                }
            }
        }

        try {
            dispatch(new GenerateAssignmentRequestPdfJob($assignmentRequest));
        } catch (\Exception $e) {
            Log::error($e);
        }

        return $this->responseCreated('AssignmentRequest created successfully', new AssignmentRequestResource($assignmentRequest->load('repairers', 'status', 'client', 'vehicle', 'photos', 'createdBy', 'updatedBy')));
    }

    /**
     * Ajouter des photos à une demande d'expertise
     *
     * @authenticated
     */
    public function addPhotos(AddPhotosToAssignmentRequestRequest $request, $id): JsonResponse
    {
        $assignmentRequest = AssignmentRequest::where('id', $id)// ->accessibleBy(auth()->user())
                                            ->firstOrFail();

        $now = Carbon::now();
        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;

        $files = [];
        if($request->hasfile('photos'))
        {
            $count = 0;
            if($request->file('photos') && $request->hasfile('photos')){
                foreach($request->file('photos') as $file)
                {
                    $count = $count + 1;
                    $name = 'IMG_REQ'.$today.'_'.$count.'.png';
                    $file->move(public_path("storage/photos/assignment-request/{$assignmentRequest->reference}"), $name);

                    $photo = Photo::create([
                        'name' => $name,
                        'assignment_request_id' => $assignmentRequest->id,
                        'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                        'created_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id,
                    ]);
                }
            }
        }

        return $this->responseCreated('Photos added successfully', null);
    }

    /**
     * Afficher une demande d'expertise
     *
     * @authenticated
     */
    public function show($id): JsonResponse
    {
        $assignmentRequest = AssignmentRequest::accessibleBy(auth()->user())->findOrFail($id);

        return $this->responseSuccess(null, new AssignmentRequestResource($assignmentRequest->load('status', 'deletedBy', 'client', 'vehicle', 'repairers', 'createdBy.entity', 'updatedBy', 'cancelledBy', 'rejectedBy', 'acceptedBy', 'assignment.status', 'photos')));
    }

    /**
     * Mettre à jour une demande d'expertise
     *
     * @authenticated
     */
    public function update(UpdateAssignmentRequestRequest $request, $id): JsonResponse
    {
        $assignmentRequest = AssignmentRequest::accessibleBy(auth()->user())->findOrFail($id);

        $assignmentRequest->update(collect($request->validated())->except(['repairers', 'repairer_id'])->all());

        $repairers = $request->has('repairers')
            ? $request->repairers
            : ($request->filled('repairer_id') ? [['repairer_id' => $request->repairer_id]] : null);
        $assignmentRequest->syncRepairers($repairers, auth()->id());

        return $this->responseSuccess('AssignmentRequest updated Successfully', new AssignmentRequestResource($assignmentRequest->load('repairers', 'status', 'client', 'vehicle')));
    }

    /**
     * Accepter la demande d'expertise : la relie au dossier créé à partir d'elle
     *
     * @authenticated
     */
    public function accept(AcceptAssignmentRequestRequest $request, $id): JsonResponse
    {
        $assignmentRequest = AssignmentRequest::accessibleBy(auth()->user())->findOrFail($id);

        if($assignmentRequest->status_id != Status::where('code', StatusEnum::PENDING)->first()->id){
            return $this->responseUnprocessable('La demande d\'expertise n\'est pas en attente.', null);
        }

        $assignmentRequest->update([
            'assignment_id' => $request->assignment_id,
            'accepted_by' => auth()->user()->id,
            'accepted_at' => Carbon::now(),
            'status_id' => Status::where('code', StatusEnum::ACCEPTED)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        return $this->responseSuccess('Demande d\'expertise acceptée avec succès', new AssignmentRequestResource($assignmentRequest->load('status', 'assignment.status', 'acceptedBy')));
    }

    /**
     * Rejeter la demande d'expertise
     *
     * @authenticated
     */
    public function reject(RejectAssignmentRequestRequest $request, $id): JsonResponse
    {
        $assignmentRequest = AssignmentRequest::accessibleBy(auth()->user())->findOrFail($id);

        if($assignmentRequest->status_id == Status::where('code', StatusEnum::PENDING)->first()->id){
            $assignmentRequest->update([
                'rejection_reason' => $request->rejection_reason,
                'rejected_by' => auth()->user()->id,
                'rejected_at' => Carbon::now(),
                'status_id' => Status::where('code', StatusEnum::REJECTED)->first()->id,
                'updated_by' => auth()->user()->id,
            ]);
        } else {
            return $this->responseUnprocessable('La demande d\'expertise n\'est pas en attente.', null);
        }

        return $this->responseSuccess('Demandée d\'expertise rejetée avec succès', new AssignmentRequestResource($assignmentRequest));
    }

    /**
     * Annuler la demande d'expertise
     *
     * @authenticated
     */
    public function cancel(CancelAssignmentRequestRequest $request, $id): JsonResponse
    {
        $assignmentRequest = AssignmentRequest::accessibleBy(auth()->user())->findOrFail($id);

        if($assignmentRequest->status_id == Status::where('code', StatusEnum::PENDING)->first()->id){
            $assignmentRequest->update([
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_by' => auth()->user()->id,
                'cancelled_at' => Carbon::now(),
                'status_id' => Status::where('code', StatusEnum::CANCELLED)->first()->id,
                'updated_by' => auth()->user()->id,
            ]);
        } else {
            return $this->responseUnprocessable('La demande d\'expertise n\'est pas en attente.', null);
        }

        return $this->responseSuccess('Demandée d\'expertise annulée avec succès', new AssignmentRequestResource($assignmentRequest));
    }

    /**
     * Supprimer une demande d'expertise
     *
     * @authenticated
     */
    public function destroy($id): JsonResponse
    {
        $assignmentRequest = AssignmentRequest::accessibleBy(auth()->user())->findOrFail($id);

        // $assignmentRequest->update([
        //     'status_id' => Status::where('code', StatusEnum::DELETED)->first()->id,
        //     'deleted_at' => now(),
        //     'deleted_by' => auth()->user()->id,
        // ]);

        return $this->responseDeleted();
    }

   
}
