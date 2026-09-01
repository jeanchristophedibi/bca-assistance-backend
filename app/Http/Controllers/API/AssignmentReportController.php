<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignmentReport\UpdateAssignmentReportRequest;
use App\Http\Requests\AssignmentReport\CreateAssignmentReportRequest;
use App\Http\Resources\AssignmentReport\AssignmentReportResource;
use App\Models\AssignmentReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Essa\APIToolKit\Api\ApiResponse;

/**
 * @group Gestion des rapports de dossiers
 *
 * APIs pour la gestion des rapports de dossiers
 */
class AssignmentReportController extends Controller
{
    use ApiResponse;

    public function __construct()
    {

    }

    public function index(): AnonymousResourceCollection
    {
        $assignmentReports = AssignmentReport::useFilters()->dynamicPaginate();

        return AssignmentReportResource::collection($assignmentReports);
    }

    public function store(CreateAssignmentReportRequest $request): JsonResponse
    {
        $assignmentReport = AssignmentReport::create($request->validated());

        return $this->responseCreated('AssignmentReport created successfully', new AssignmentReportResource($assignmentReport));
    }

    public function show(AssignmentReport $assignmentReport): JsonResponse
    {
        return $this->responseSuccess(null, new AssignmentReportResource($assignmentReport));
    }

    public function update(UpdateAssignmentReportRequest $request, AssignmentReport $assignmentReport): JsonResponse
    {
        $assignmentReport->update($request->validated());

        return $this->responseSuccess('AssignmentReport updated Successfully', new AssignmentReportResource($assignmentReport));
    }

    public function destroy(AssignmentReport $assignmentReport): JsonResponse
    {
        $assignmentReport->delete();

        return $this->responseDeleted();
    }

   
}
