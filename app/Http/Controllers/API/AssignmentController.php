<?php

namespace App\Http\Controllers\API;

use App\Enums\AssignmentTypeEnum;
use App\Enums\EntityCodeEnum;
use App\Enums\EntityTypeEnum;
use App\Enums\ExpertiseTypeEnum;
use App\Enums\HourlyRateEnum;
use App\Enums\NumberPaintElementEnum;
use App\Enums\PaintTypeEnum;
use App\Enums\PhotoTypeEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Enums\WorkforceTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assignment\AddCommentAssignmentRequest;
use App\Http\Requests\Assignment\CalculateAssignmentRequest;
use App\Http\Requests\Assignment\CalculateEvaluationAssignmentRequest;
use App\Http\Requests\Assignment\CreateAssignmentRequest;
use App\Http\Requests\Assignment\DeleteAssignmentRequest;
use App\Http\Requests\Assignment\EditAssignmentRequest;
use App\Http\Requests\Assignment\EvaluateAssignmentRequest;
use App\Http\Requests\Assignment\MarkAsUnpaidAssignmentRequest;
use App\Http\Requests\Assignment\RealizeAssignmentRequest;
use App\Http\Requests\Assignment\RestoreDeletedAssignmentRequest;
use App\Http\Requests\Assignment\UnvalidateAssignmentByExpertRequest;
use App\Http\Requests\Assignment\UnvalidateAssignmentByRepairerRequest;
use App\Http\Requests\Assignment\UnvalidateAssignmentRequest;
use App\Http\Requests\Assignment\UnvalidateFirstCorrectionAssignmentRequest;
use App\Http\Requests\Assignment\UnvalidateQuoteAssignmentByExpertRequest;
use App\Http\Requests\Assignment\UnvalidateQuoteAssignmentByRepairerRequest;
use App\Http\Requests\Assignment\UnvalidateSecondCorrectionAssignmentRequest;
use App\Http\Requests\Assignment\UpdateAssignmentRequest;
use App\Http\Requests\Assignment\UpdateEditAssignmentRequest;
use App\Http\Requests\Assignment\UpdateRealizedAssignmentRequest;
use App\Http\Requests\Assignment\ValidateAssignmentQuoteWithConditionsRequest;
use App\Http\Requests\Assignment\ValidateSecondCorrectionAssignmentRequest;
use App\Http\Requests\Assignment\ValidateWorkSheetByExpertAssignmentRequest;
use App\Http\Resources\Assignment\AssignmentResource;
use App\Jobs\GenerateExpertiseReportPdfJob;
use App\Jobs\GenerateExpertiseSheetPdfJob;
use App\Jobs\GenerateWorkSheetPdfJob;
use App\Jobs\SendAssignmentActionMailJob;
use App\Jobs\SendOpenedAssignmentNotificationJob;
use App\Jobs\SendValidatedAssignmentNotificationJob;
use App\Models\Ascertainment;
use App\Models\Assignment;
use App\Models\AssignmentExpert;
use App\Models\AssignmentType;
use App\Models\Client;
use App\Models\DepreciationTable;
use App\Models\Entity;
use App\Models\EntityType;
use App\Models\ExpertiseType;
use App\Models\HourlyRate;
use App\Models\NumberPaintElement;
use App\Models\OtherCost;
use App\Models\OtherCostType;
use App\Models\PaintingPrice;
use App\Models\PaintProductPrice;
use App\Models\PaintType;
use App\Models\Payment;
use App\Models\Photo;
use App\Models\PhotoType;
use App\Models\Receipt;
use App\Models\Shock;
use App\Models\ShockPoint;
use App\Models\ShockWork;
use App\Models\Status;
use App\Models\Supply;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleAge;
use App\Models\VehicleEnergy;
use App\Models\VehicleGenre;
use App\Models\Workforce;
use App\Models\WorkforceType;
use App\Services\MarketValue\MarketValueService;
use App\Services\Receipt\UpdateReceiptService;
use App\Services\WorkDuration\WorkDurationService;
use Carbon\Carbon;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

/**
 * @group Gestion des dossiers
 *
 * APIs pour la gestion des dossiers
 */
class AssignmentController extends Controller
{
    use ApiResponse;

    public function __construct()
    {

    }

    /**
     * Lister tous les dossiers
     *
     * @authenticated
     */
    public function index()
    {
        $start_date = request()->filled('start_date') ? Carbon::parse(request()->start_date)->startOfDay() : null;
        $end_date   = request()->filled('end_date') ? Carbon::parse(request()->end_date)->endOfDay() : null;
        $assignments = Assignment::with([
                                // 'experts', 'status', 'vehicle', 'vehicle.brand', 'vehicle.vehicleModel', 'vehicle.color', 'vehicle.bodywork', 'insurer', 'additionalInsurer', 'broker', 'repairers', 'client', 'assignmentType', 'expertiseType', 'generalState', 'claimNature', 'technicalConclusion', 'documentTransmitted', 'createdBy', 'updatedBy', 'deletedBy', 'closedBy', 'cancelledBy', 'editedBy', 'realizedBy', 'directedBy', 'workSheetEstablishedBy', 'validatedBy', 'referenceUpdatedBy', 'openedBy',
                                'shocks.shockPoint', 'shocks.shockWorks', 'shocks.shockWorks.supply', 'shocks.workforces', 'shocks.workforces.workforceType', 'shocks.paintType', 'shocks.hourlyRate', 'shocks.status', 'experts', 'otherCosts', 'ascertainments', 'ascertainments.ascertainmentType', 'receipts', 'receipts.receiptType', 'status', 'vehicle', 'vehicle.brand', 'vehicle.vehicleModel', 'vehicle.color', 'vehicle.bodywork', 'insurer', 'additionalInsurer', 'broker', 'repairers', 'client', 'assignmentType', 'expertiseType', 'generalState', 'claimNature', 'technicalConclusion', 'documentTransmitted', 'createdBy', 'updatedBy', 'deletedBy', 'closedBy', 'cancelledBy', 'editedBy', 'realizedBy', 'directedBy', 'workSheetEstablishedBy', 'validatedBy', 'referenceUpdatedBy', 'openedBy',
                                'shocks' => function($query) {
                                    $query->orderBy('position', 'asc');
                                },
                                'shocks.shockWorks' => function($query) {
                                    $query->orderBy('position', 'asc');
                                },
                                'shocks.workforces' => function($query) {
                                    $query->orderBy('position', 'asc');
                                },
                                'receipts' => function($query) {
                                    $query->orderBy('id', 'asc');
                                },
                                'receipts.receiptType' => function($query) {
                                    $query->orderBy('id', 'asc');
                                },
                                'ascertainments' => function($query) {
                                    $query->orderBy('id', 'asc');
                                },
                                'ascertainments.ascertainmentType' => function($query) {
                                    $query->orderBy('id', 'asc');
                                },
                            ]);

        if($start_date && $end_date){
            $assignments = $assignments->where('assignments.created_at', '>=', $start_date)->where('assignments.created_at', '<=', $end_date);
        } elseif ($start_date) {
            $assignments = $assignments->where('assignments.created_at', '>=', $start_date);
        } elseif ($end_date) {
            $assignments = $assignments->where('assignments.created_at', '<=', $end_date);
        }

        $status_id = Status::where('code', request()->status_code)->first()->id ?? null;

        if($status_id){
            $assignments = $assignments->where('status_id', $status_id);
        }

        $opened_by = request()->opened_by ?? null;
        if($opened_by){
            $assignments = $assignments->where('opened_by', $opened_by);
        }

        $created_by = request()->created_by ?? null;
        if($created_by){
            $assignments = $assignments->where('created_by', $created_by);
        }

        $insurer_id = request()->insurer_id ?? null;
        if($insurer_id){
            $assignments = $assignments->where(function($query) use ($insurer_id){
                $query->where(['insurer_id' => $insurer_id])->orWhere(['broker_id' => $insurer_id]);
            });
        }

        $assignments = $assignments->latest('assignments.created_at')
                    ->accessibleBy(auth()->user())
                    ->useFilters()
                    ->dynamicPaginate();

        if($start_date || $end_date || $status_id || $opened_by || $created_by || $insurer_id || request()->filled('vehicle_id') || request()->filled('insurer_id') || request()->filled('repairer_id') || request()->filled('client_id') || request()->filled('assignment_type_id') || request()->filled('expertise_type_id') || request()->filled('vehicle_id') || request()->filled('client_id') || request()->filled('claim_nature_id') || request()->filled('reference_updated_by') || request()->filled('generated_by') || request()->filled('work_sheet_established_by') || request()->filled('created_by') || request()->filled('opened_by') || request()->filled('paid_by') || request()->filled('unpaid_by') || request()->filled('first_correction_validated_by') || request()->filled('first_correction_unvalidated_by') || request()->filled('second_correction_validated_by') || request()->filled('second_correction_unvalidated_by') || request()->filled('validated_by') || request()->filled('unvalidated_by') || request()->filled('cancelled_by') || request()->filled('deleted_by') || request()->filled('archived_by') || request()->filled('classified_without_further_action_by') || request()->filled('pending_for_repairer_quote_by') || request()->filled('pending_for_repairer_quote_validation_by') || request()->filled('in_editing_by') || request()->filled('in_first_correction_by') || request()->filled('in_second_correction_by') || request()->filled('pending_for_repairer_validation_by') || request()->filled('pending_for_expert_validation_by') || request()->filled('validated_by_repairer_by') || request()->filled('unvalidated_by_repairer_by') || request()->filled('validated_by_expert_by') || request()->filled('unvalidated_by_expert_by') || request()->filled('agreement_for_work_subject_to_conditions_by') || request()->filled('quote_validated_by_repairer_by') || request()->filled('quote_unvalidated_by_repairer_at') || request()->filled('quote_validated_by_expert_by') || request()->filled('quote_unvalidated_by_expert_by') || request()->filled('edition_unvalidated_by') || request()->filled('edition_validated_by') || request()->filled('search')){
            $export_url = $this->export($start_date, $end_date, $status_id, $opened_by, $insurer_id);
        }

        return AssignmentResource::collection($assignments)->additional([
            'export_url' => $export_url ?? null,
        ]);
    }

    /**
     * Récupérer les dossiers dont la date de redaction est expirée (24 heures)
     *
     * @authenticated
     */
    public function get_assignment_edition_time_to_expired(): JsonResponse
    {
        $start_date = request()->filled('start_date') ? Carbon::parse(request()->start_date)->startOfDay() : null;
        $end_date   = request()->filled('end_date') ? Carbon::parse(request()->end_date)->endOfDay() : null;
        $assignments = Assignment::with([
                                'shocks.shockPoint', 'shocks.shockWorks', 'shocks.shockWorks.supply', 'shocks.workforces', 'shocks.workforces.workforceType', 'shocks.paintType', 'shocks.hourlyRate', 'shocks.status', 'experts', 'otherCosts', 'ascertainments', 'ascertainments.ascertainmentType', 'receipts', 'receipts.receiptType', 'status', 'vehicle', 'vehicle.brand', 'vehicle.vehicleModel', 'vehicle.color', 'vehicle.bodywork', 'insurer', 'additionalInsurer', 'broker', 'repairers', 'client', 'assignmentType', 'expertiseType', 'generalState', 'claimNature', 'technicalConclusion', 'documentTransmitted', 'createdBy', 'updatedBy', 'deletedBy', 'closedBy', 'cancelledBy', 'editedBy', 'realizedBy', 'directedBy', 'workSheetEstablishedBy', 'validatedBy', 'referenceUpdatedBy', 'openedBy',
                                'shocks' => function($query) {
                                    $query->orderBy('position', 'asc');
                                },
                                'shocks.shockWorks' => function($query) {
                                    $query->orderBy('position', 'asc');
                                },
                                'shocks.workforces' => function($query) {
                                    $query->orderBy('position', 'asc');
                                },
                                'receipts' => function($query) {
                                    $query->orderBy('id', 'asc');
                                },
                                'receipts.receiptType' => function($query) {
                                    $query->orderBy('id', 'asc');
                                },
                                'ascertainments' => function($query) {
                                    $query->orderBy('id', 'asc');
                                },
                                'ascertainments.ascertainmentType' => function($query) {
                                    $query->orderBy('id', 'asc');
                                },
                            ]);

        if($start_date && $end_date){
            $assignments = $assignments->where('assignments.created_at', '>=', $start_date)->where('assignments.created_at', '<=', $end_date);
        } elseif ($start_date) {
            $assignments = $assignments->where('assignments.created_at', '>=', $start_date);
        } elseif ($end_date) {
            $assignments = $assignments->where('assignments.created_at', '<=', $end_date);
        }

        $status_id = Status::where('code', request()->status_code)->first()->id ?? null;

        if($status_id){
            $assignments = $assignments->where('status_id', $status_id);
        }

        $opened_by = request()->opened_by ?? null;
        if($opened_by){
            $assignments = $assignments->where('opened_by', $opened_by);
        }

        $created_by = request()->created_by ?? null;
        if($created_by){
            $assignments = $assignments->where('created_by', $created_by);
        }

        $insurer_id = request()->insurer_id ?? null;
        if($insurer_id){
            $assignments = $assignments->where(function($query) use ($insurer_id){
                $query->where(['insurer_id' => $insurer_id])->orWhere(['broker_id' => $insurer_id]);
            });
        }

        $assignments = $assignments->where('assignments.created_at', '<', Carbon::now()->subHours(24))
                        ->latest('assignments.created_at')
                        ->useFilters()
                        ->dynamicPaginate();

        if($start_date || $end_date || $status_id || $opened_by){
            $export_url = $this->export($start_date, $end_date, $status_id, $opened_by, $insurer_id);
        }
       
        return $this->responseSuccess('Dossier dont la date de redaction est expirée',[
            'data' => $assignments,
            'export_url' => $export_url ?? null,
        ]);
    }

    /**
     * Récupérer les dossiers dont la date de recouvrement est expirée (48 heures)
     *
     * @authenticated
     */
    public function get_assignment_recovery_time_to_expired(): JsonResponse
    {
        $start_date = request()->filled('start_date') ? Carbon::parse(request()->start_date)->startOfDay() : null;
        $end_date   = request()->filled('end_date') ? Carbon::parse(request()->end_date)->endOfDay() : null;
        $assignments = Assignment::with([
                                'shocks.shockPoint', 'shocks.shockWorks', 'shocks.shockWorks.supply', 'shocks.workforces', 'shocks.workforces.workforceType', 'shocks.paintType', 'shocks.hourlyRate', 'shocks.status', 'experts', 'otherCosts', 'ascertainments', 'ascertainments.ascertainmentType', 'receipts', 'receipts.receiptType', 'status', 'vehicle', 'vehicle.brand', 'vehicle.vehicleModel', 'vehicle.color', 'vehicle.bodywork', 'insurer', 'additionalInsurer', 'broker', 'repairers',
                                'shocks' => function($query) {
                                    $query->orderBy('position', 'asc');
                                },
                                'shocks.shockWorks' => function($query) {
                                    $query->orderBy('position', 'asc');
                                },
                                'shocks.workforces' => function($query) {
                                    $query->orderBy('position', 'asc');
                                },
                                'receipts' => function($query) {
                                    $query->orderBy('id', 'asc');
                                },
                                'receipts.receiptType' => function($query) {
                                    $query->orderBy('id', 'asc');
                                },
                                'ascertainments' => function($query) {
                                    $query->orderBy('id', 'asc');
                                },
                                'ascertainments.ascertainmentType' => function($query) {
                                    $query->orderBy('id', 'asc');
                                },
                            ]);

        if($start_date && $end_date){
            $assignments = $assignments->where('assignments.created_at', '>=', $start_date)->where('assignments.created_at', '<=', $end_date);
        } elseif ($start_date) {
            $assignments = $assignments->where('assignments.created_at', '>=', $start_date);
        } elseif ($end_date) {
            $assignments = $assignments->where('assignments.created_at', '<=', $end_date);
        }

        $status_id = Status::where('code', request()->status_code)->first()->id ?? null;

        if($status_id){
            $assignments = $assignments->where('status_id', $status_id);
        }

        $opened_by = request()->opened_by ?? null;
        if($opened_by){
            $assignments = $assignments->where('opened_by', $opened_by);
        }

        $created_by = request()->created_by ?? null;
        if($created_by){
            $assignments = $assignments->where('created_by', $created_by);
        }

        $insurer_id = request()->insurer_id ?? null;
        if($insurer_id){
            $assignments = $assignments->where(function($query) use ($insurer_id){
                $query->where(['insurer_id' => $insurer_id])->orWhere(['broker_id' => $insurer_id]);
            });
        }

        $assignments = $assignments->where('assignments.created_at', '<', Carbon::now()->subHours(48))
                        ->latest('created_at')
                        ->useFilters()
                        ->dynamicPaginate();

        if($start_date || $end_date || $status_id || $opened_by){
            $export_url = $this->export($start_date, $end_date, $status_id, $opened_by, $insurer_id);
        }
       
        return $this->responseSuccess('Dossier dont la date de recouvrement est expirée', [
            'data' => $assignments,
            'export_url' => $export_url ?? null,
        ]);
    }

    /**
     * Exporter les dossiers par période
     * 
     *
     * @authenticated
     */
    public function export($start_date, $end_date, $status_id, $opened_by, $insurer_id) : string
    {        
        $start_date = $start_date ? Carbon::parse($start_date)->startOfDay() : null;
        $end_date = $end_date ? Carbon::parse($end_date)->endOfDay() : null;

        $assignments = \App\Models\Assignment::with('client', 'insurer', 'additionalInsurer', 'repairers', 'status');

        if ($start_date && $end_date) {
            $assignments = $assignments->where('assignments.created_at', '>=', $start_date)->where('assignments.created_at', '<=', $end_date);
        } elseif ($start_date) {
            $assignments = $assignments->where('assignments.created_at', '>=', $start_date);
        } elseif ($end_date) {
            $assignments = $assignments->where('assignments.created_at', '<=', $end_date);
        }

        if($status_id){
            $assignments = $assignments->where('status_id', $status_id);
        }

        if($opened_by){
            $assignments = $assignments->where('created_by', $opened_by);
        }

        if($insurer_id){
            $assignments = $assignments->where(function($query) use ($insurer_id){
                $query->where(['insurer_id' => $insurer_id])->orWhere(['broker_id' => $insurer_id]);
            });
        }

        $assignments = $assignments->latest('assignments.created_at')
            ->useFilters()
            ->get();

        $exportData = [];
        // En-tête
        $exportData[] = [
            'Référence',
            'Véhicule',
            'Client',
            'Assureur',
            'Assureur additionnel',
            'Réparateur',
            'Statut',
            'Date de création'
        ];

        foreach ($assignments as $assignment) {
            $exportData[] = [
                $assignment->reference,
                $assignment->vehicle ? $assignment->vehicle->license_plate : '',
                $assignment->client ? $assignment->client->name : '',
                $assignment->insurer ? $assignment->insurer->name : '',
                $assignment->broker ? $assignment->broker->name : '',
                $assignment->additionalInsurer ? $assignment->additionalInsurer->name : '',
                $assignment->repairer ? $assignment->repairer->name : '',
                $assignment->status ? $assignment->status->label : '',
                $assignment->created_at ?  $assignment->created_at->format('d/m/Y H:i:s') : '',
            ];
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Dossiers');

        foreach ($exportData as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $value);
            }
        }

        // Utilisation de Maatwebsite\Excel\Facades\Excel pour exporter
        $filename = 'export_dossiers.xlsx';
        $filepath = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0777, true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filepath);

        $url = asset('storage/exports/' . $filename);

        return $url;
    }

    /**
     * Récupérer tous les modèles
     *
     * @authenticated
     */
    public function all_models()
    {
        $filename = 'database.sql';
        $filepath = storage_path('app/public/backups/' . $filename);

        // Vérifier si le fichier existe
        if (!file_exists($filepath)) {
            return $this->responseUnprocessable('Le fichier de sauvegarde n\'existe pas.');
        }

        // Retourner le téléchargement du fichier
        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Statistiques des dossiers
     *
     * @queryParam start_date string La date de début de la période.
     * @queryParam end_date string La date de fin de la période.
     *
     * @authenticated
     */
    public function statistics(): JsonResponse
    {
        $start_date = request()->filled('start_date') ? Carbon::parse(request()->start_date)->startOfDay() : null;
        $end_date   = request()->filled('end_date') ? Carbon::parse(request()->end_date)->endOfDay() : null;

        $assignments_by_year_and_month_count = Assignment::selectRaw('YEAR(assignments.created_at) as year, MONTH(assignments.created_at) as month, COUNT(*) as count');

        if ($start_date && $end_date) {
            $assignments_by_year_and_month_count = $assignments_by_year_and_month_count->whereBetween('assignments.created_at', [$start_date, $end_date]);
        } elseif ($start_date) {
            $assignments_by_year_and_month_count = $assignments_by_year_and_month_count->where('assignments.created_at', '>=', $start_date);
        } elseif ($end_date) {
            $assignments_by_year_and_month_count = $assignments_by_year_and_month_count->where('assignments.created_at', '<=', $end_date);
        }

        if (request()->has('status_id')) {
            $assignments_by_year_and_month_count = $assignments_by_year_and_month_count->where('assignments.status_id', request()->status_id);
        }

        if (request()->has('opened_by')) {
            $assignments_by_year_and_month_count = $assignments_by_year_and_month_count->where('assignments.opened_by', request()->opened_by);
        }

        if (request()->has('created_by')) {
            $assignments_by_year_and_month_count = $assignments_by_year_and_month_count->where('assignments.created_by', request()->created_by);
        }

        $insurer_id = request()->insurer_id ?? null;
        if($insurer_id){
            $assignments_by_year_and_month_count = $assignments_by_year_and_month_count->where(function($query) use ($insurer_id){
                $query->where(['insurer_id' => $insurer_id])->orWhere(['broker_id' => $insurer_id]);
            });
        }

        $assignments_by_year_and_month_count = $assignments_by_year_and_month_count
            ->groupBy(DB::raw('YEAR(assignments.created_at)'), DB::raw('MONTH(assignments.created_at)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->useFilters()
            ->get();


        $auth_user = User::with('currentRole')->where('id', auth()->user()->id)->first();

        if($auth_user->currentRole->name == RoleEnum::CEO->value || $auth_user->currentRole->name == RoleEnum::ADMIN->value 
        || $auth_user->currentRole->name == RoleEnum::SYSTEM_ADMIN->value || $auth_user->currentRole->name == RoleEnum::ACCOUNTANT_MANAGER->value
        || $auth_user->currentRole->name == RoleEnum::ACCOUNTANT->value){
        $assignments_by_year_and_month_amount = Assignment::join('receipts', 'assignments.id', '=', 'receipts.assignment_id')
            ->selectRaw('YEAR(assignments.created_at) as year, MONTH(assignments.created_at) as month, SUM(receipts.amount) as amount');

        if($start_date && $end_date){
            $assignments_by_year_and_month_amount = $assignments_by_year_and_month_amount->whereBetween('assignments.created_at', [$start_date, $end_date]);
        } elseif ($start_date) {
            $assignments_by_year_and_month_amount = $assignments_by_year_and_month_amount->where('assignments.created_at', '>=', $start_date);
        } elseif ($end_date) {
            $assignments_by_year_and_month_amount = $assignments_by_year_and_month_amount->where('assignments.created_at', '<=', $end_date);
        }

        if (request()->has('status_id')) {
            $assignments_by_year_and_month_amount = $assignments_by_year_and_month_amount->where('assignments.status_id', request()->status_id);
        }

        if(request()->has('opened_by')){
            $assignments_by_year_and_month_amount = $assignments_by_year_and_month_amount->where('assignments.opened_by', request()->opened_by);
        }

        if (request()->has('created_by')) {
            $assignments_by_year_and_month_amount = $assignments_by_year_and_month_amount->where('assignments.created_by', request()->created_by);
        }

        if($insurer_id){
            $assignments_by_year_and_month_amount = $assignments_by_year_and_month_amount->where(function($query) use ($insurer_id){
                $query->where(['insurer_id' => $insurer_id])->orWhere(['broker_id' => $insurer_id]);
            });
        }

        $assignments_by_year_and_month_amount = $assignments_by_year_and_month_amount
            ->groupBy(DB::raw('YEAR(assignments.created_at)'), DB::raw('MONTH(assignments.created_at)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->useFilters()
            ->get();
        } else {
            $assignments_by_year_and_month_amount = [];
        }

        // Export des statistiques des dossiers

        // Création d'un classeur avec deux feuilles
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        if(count($assignments_by_year_and_month_count) > 0){
            $export_assignments_by_year_and_month_count_data = [];
            // En-tête
            $export_assignments_by_year_and_month_count_data[] = [
                'Année',
                'Mois',
                'Nombre de dossiers'
            ];

            foreach ($assignments_by_year_and_month_count as $assignment) {
                $export_assignments_by_year_and_month_count_data[] = [
                    $assignment->year,
                    $assignment->month,
                    $assignment->count,
                ];
            }

            // Première feuille : Nombre de dossiers par année et mois
            $sheetCount = $spreadsheet->getActiveSheet();
            $sheetCount->setTitle('Nombre de dossiers');

            foreach ($export_assignments_by_year_and_month_count_data as $rowIndex => $row) {
                foreach ($row as $colIndex => $value) {
                    $sheetCount->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $value);
                }
            }
        }

        if(count($assignments_by_year_and_month_amount) > 0){
            $export_assignments_by_year_and_month_amount_data = [];
            // En-tête
            $export_assignments_by_year_and_month_amount_data[] = [
                'Année',
                'Mois',
                'Montant des dossiers'
            ];
            
            foreach ($assignments_by_year_and_month_amount as $assignment) {
                $export_assignments_by_year_and_month_amount_data[] = [
                    $assignment->year,
                    $assignment->month,
                    $assignment->amount,
                ];
            }

            // Deuxième feuille : Montant des dossiers par année et mois
            $sheetAmount = $spreadsheet->createSheet();
            $sheetAmount->setTitle('Montant des dossiers');

            foreach ($export_assignments_by_year_and_month_amount_data as $rowIndex => $row) {
                foreach ($row as $colIndex => $value) {
                    $sheetAmount->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $value);
                }
            }
        }

        // Sauvegarde du fichier
        $filename = 'export_assignments_stats.xlsx';
        $filepath = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0777, true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filepath);

        $url = asset('storage/exports/' . $filename);

        return $this->responseSuccess('Statistiques des dossiers', [
            'assignments_by_year_and_month_count' => $assignments_by_year_and_month_count,
            'assignments_by_year_and_month_amount' => $assignments_by_year_and_month_amount,
            'export_url' => $url,
        ]);
    }

    /**
     * Exporter les statistiques des dossiers par période
     * 
     *
     * @authenticated
     */
    public function export_statistics($start_date, $end_date, $status_id) : string
    {
        $start_date = $start_date ? \Carbon\Carbon::parse($start_date)->startOfDay() : null;
        $end_date = $end_date ? \Carbon\Carbon::parse($end_date)->endOfDay() : null;

        $assignments = \App\Models\Assignment::selectRaw('YEAR(assignments.created_at) as year, MONTH(assignments.created_at) as month, COUNT(*) as count, SUM(receipts.amount) as amount');

        $assignments = $assignments->join('receipts', 'assignments.id', '=', 'receipts.assignment_id');

        if ($start_date && $end_date) {
            $assignments = $assignments->whereBetween('assignments.created_at', [$start_date, $end_date]);
        } elseif ($start_date) {
            $assignments = $assignments->where('assignments.created_at', '>=', $start_date);
        } elseif ($end_date) {
            $assignments = $assignments->where('assignments.created_at', '<=', $end_date);
        }

        if($status_id){
            $assignments = $assignments->where('status_id', $status_id);
        }

        $assignments = $assignments->latest('assignments.created_at')
            ->useFilters()
            ->get();

        $exportData = [];
        // En-tête
        $exportData[] = [
            'Année',
            'Mois',
            'Nombre de dossiers',
            'Montant des dossiers'
        ];

        foreach ($assignments as $assignment) {
            $exportData[] = [
                $assignment->year,
                $assignment->month,
                $assignment->count,
                $assignment->amount,
            ];
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($exportData as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $value);
            }
        }

        // Utilisation de Maatwebsite\Excel\Facades\Excel pour exporter
        $filename = 'export_statistiques_dossiers.xlsx';
        $filepath = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0777, true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filepath);

        $url = asset('storage/exports/' . $filename);

        return $url;
    }

    /**
     * ZIP pour photos/report/<référence>/ et photos/work-sheet/<référence>/.
     * Par appel HTTP : un seul lot par cible (ex. 1 référence si subfolders_per_archive = 1), pas tout le carnet d’un coup.
     * Les références déjà dans le manifeste sont ignorées.
     *
     * @authenticated
     */
    public function zip_public_storage_folders(): JsonResponse
    {
        if (! extension_loaded('zip')) {
            return $this->responseUnprocessable('L\'extension PHP zip n\'est pas activée.');
        }

        $subfoldersPerArchive = 15;
        $publicRoot = storage_path('app/public');

        if (! is_dir($publicRoot)) {
            return $this->responseUnprocessable('Le répertoire storage/app/public est introuvable.');
        }

        $photoTargets = [
            [
                'parent_relative' => 'photos/report',
                'backup_slug' => 'photos-report',
            ],
            [
                'parent_relative' => 'photos/work-sheet',
                'backup_slug' => 'photos-work-sheet',
            ],
        ];

        $manifest = $this->loadPublicStorageZipManifest();
        $backupRoot = storage_path('app/public/backups');
        if (! is_dir($backupRoot)) {
            mkdir($backupRoot, 0775, true);
        }

        $timestamp = date('Y-m-d_His');
        $archivesByTarget = [];
        $totalSkippedSubfolders = 0;
        $totalArchivedSubfolders = 0;

        foreach ($photoTargets as $target) {
            $parentRelative = $target['parent_relative'];
            $backupSlug = $target['backup_slug'];
            $absoluteParent = $publicRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $parentRelative);

            if (! is_dir($absoluteParent)) {
                $archivesByTarget[] = [
                    'parent' => $parentRelative,
                    'parts' => [],
                    'skipped_already_zipped_subfolders' => 0,
                    'message' => 'Répertoire absent sur le disque.',
                ];

                continue;
            }

            $pendingSubdirs = [];
            $skippedHere = 0;

            foreach ($this->listImmediateSubdirectories($absoluteParent) as $name => $absoluteSub) {
                $folderKey = $parentRelative.'/'.$name;

                if (isset($manifest[$folderKey])) {
                    $skippedHere++;

                    continue;
                }

                $pendingSubdirs[] = [
                    'key' => $folderKey,
                    'path' => $absoluteSub,
                    'name' => $name,
                ];
            }

            $totalSkippedSubfolders += $skippedHere;

            if ($pendingSubdirs === []) {
                $archivesByTarget[] = [
                    'parent' => $parentRelative,
                    'parts' => [],
                    'skipped_already_zipped_subfolders' => $skippedHere,
                    'message' => $skippedHere > 0
                        ? 'Tous les sous-dossiers ont déjà été archivés.'
                        : 'Aucun sous-dossier dans ce répertoire.',
                ];

                continue;
            }

            $allChunks = array_chunk($pendingSubdirs, $subfoldersPerArchive);
            $partsTotal = count($allChunks);
            /** Un seul chunk par requête pour éviter de traiter toute la file d’un coup (504 / manifest x N). */
            $chunks = array_slice($allChunks, 0, 1);
            $batchesRemainingAfterThisRun = max(0, $partsTotal - count($chunks));
            $folderBackupDir = $backupRoot.DIRECTORY_SEPARATOR.$backupSlug;

            if (! is_dir($folderBackupDir)) {
                mkdir($folderBackupDir, 0775, true);
            }

            $folderParts = [];

            foreach ($chunks as $index => $chunk) {
                $partNum = $index + 1;
                $filename = sprintf('%s_%s_part%03d_of_%03d.zip', $backupSlug, $timestamp, $partNum, $partsTotal);
                $zipPath = $folderBackupDir.DIRECTORY_SEPARATOR.$filename;

                $zip = new \ZipArchive();

                if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                    return $this->responseUnprocessable('Impossible de créer le fichier ZIP : '.$filename);
                }

                $fileList = $this->collectFilesForPhotoSubdirectories($chunk);
                $subdirNamesInChunk = array_column($chunk, 'name');

                foreach ($fileList as $item) {
                    $zip->addFile($item['path'], $item['entry']);
                }

                foreach ($subdirNamesInChunk as $dirName) {
                    if (! $this->subdirectoryHasAnyFileInList($fileList, $dirName)) {
                        $zip->addEmptyDir($dirName.'/');
                    }
                }

                $zip->close();

                foreach ($chunk as $sub) {
                    $manifest[$sub['key']] = Carbon::now()->toIso8601String();
                    $totalArchivedSubfolders++;
                }

                $this->savePublicStorageZipManifest($manifest);

                $folderParts[] = [
                    'filename' => $filename,
                    'download_url' => asset('storage/backups/'.$backupSlug.'/'.$filename),
                    'subfolders' => array_column($chunk, 'name'),
                    'subfolders_count' => count($chunk),
                    'files_count' => count($fileList),
                    'part' => $partNum,
                    'parts_total' => $partsTotal,
                ];
            }

            $archivesByTarget[] = [
                'parent' => $parentRelative,
                'parts' => $folderParts,
                'skipped_already_zipped_subfolders' => $skippedHere,
                'batches_remaining_in_queue' => $batchesRemainingAfterThisRun,
                'note' => $batchesRemainingAfterThisRun > 0
                    ? 'Relancer l’endpoint pour traiter les lots suivants.'
                    : null,
            ];
        }

        return $this->responseSuccess('Archives créées avec succès', [
            'subfolders_per_archive' => $subfoldersPerArchive,
            'manifest_path' => $this->publicStorageZipManifestPath(),
            'targets' => array_column($photoTargets, 'parent_relative'),
            'skipped_already_zipped_subfolders_total' => $totalSkippedSubfolders,
            'new_subfolders_archived_total' => $totalArchivedSubfolders,
            'archives_by_target' => $archivesByTarget,
        ]);
    }

    /**
     * @return array<string, string> nom du sous-dossier => chemin absolu
     */
    private function listImmediateSubdirectories(string $absolutePath): array
    {
        $out = [];
        foreach (scandir($absolutePath) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $full = $absolutePath.DIRECTORY_SEPARATOR.$entry;
            if (is_dir($full)) {
                $out[$entry] = $full;
            }
        }

        return $out;
    }

    /**
     * @param  array<int, array{key: string, path: string, name: string}>  $chunk
     * @return array<int, array{path: string, entry: string}>
     */
    private function collectFilesForPhotoSubdirectories(array $chunk): array
    {
        $files = [];

        foreach ($chunk as $sub) {
            $absoluteSub = rtrim($sub['path'], DIRECTORY_SEPARATOR);
            $dirName = $sub['name'];

            if (! is_dir($absoluteSub)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($absoluteSub, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if (! $file->isFile()) {
                    continue;
                }
                $realPath = $file->getRealPath();
                if ($realPath === false) {
                    continue;
                }
                $relativePath = substr($realPath, strlen($absoluteSub) + 1);
                $files[] = [
                    'path' => $realPath,
                    'entry' => $dirName.'/'.str_replace('\\', '/', $relativePath),
                ];
            }
        }

        return $files;
    }

    /**
     * @param  array<int, array{path: string, entry: string}>  $fileList
     */
    private function subdirectoryHasAnyFileInList(array $fileList, string $dirName): bool
    {
        $prefix = $dirName.'/';

        foreach ($fileList as $item) {
            if (str_starts_with($item['entry'], $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function publicStorageZipManifestPath(): string
    {
        return storage_path('app/public_storage_zip_manifest_photos_.json');
    }

    /**
     * @return array<string, string> clés manifeste (chemins relatifs depuis storage/app/public, ex. sous-dossier photos) => date ISO
     */
    private function loadPublicStorageZipManifest(): array
    {
        $path = $this->publicStorageZipManifestPath();

        if (! is_file($path)) {
            return [];
        }

        $json = file_get_contents($path);
        $data = json_decode($json ?? '', true);

        return is_array($data) ? $data : [];
    }

    /**
     * @param  array<string, string>  $manifest
     */
    private function savePublicStorageZipManifest(array $manifest): void
    {
        file_put_contents(
            $this->publicStorageZipManifestPath(),
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            LOCK_EX
        );
    }

    /**
     * Calculer un dossier
     *
     * @authenticated
     */
    public function calculate(CalculateAssignmentRequest $request): JsonResponse
    {
        $assignment = Assignment::findOrFail($request->assignment_id);
        
        $evaluations = [];
        $vehicle = Vehicle::with('vehicleGenre', 'vehicleEnergy')->findOrFail($request->vehicle_id);
        
        $ascertainments = [];
        $ascertainment = $request->get('ascertainments');

        foreach ($ascertainment as $data) {
            $ascertainments[] = [
                'ascertainment_type_id' => $data['ascertainment_type_id'],
                'very_good' => $data['very_good'],
                'good' => $data['good'],
                'acceptable' => $data['acceptable'],
                'less_good' => $data['less_good'],
                'bad' => $data['bad'],
                'very_bad' => $data['very_bad'],
                'comment' => $data['comment'],
            ];
        }

        $shocks = [];
        $total_amount_excluding_tax = 0;
        $total_amount_tax = 0;
        $total_amount = 0;

        $shocks_amount_excluding_tax = 0;
        $shocks_amount_tax = 0;
        $shocks_amount = 0;
        
        if(count($request->get('shocks')) > 0){
            foreach ($request->get('shocks') as $data) {

                $shock_works = [];
                $obsolescence_amount_excluding_tax = 0;
                $obsolescence_amount_tax = 0;
                $obsolescence_amount = 0;
                $recovery_amount_excluding_tax = 0;
                $recovery_amount_tax = 0;
                $recovery_amount = 0;
                $new_amount_excluding_tax = 0;
                $new_amount_tax = 0;
                $new_amount = 0;
                $total_obsolescence_amount_excluding_tax = 0;
                $total_obsolescence_amount_tax = 0;
                $total_obsolescence_amount = 0;
                $total_recovery_amount_excluding_tax = 0;
                $total_recovery_amount_tax = 0;
                $total_recovery_amount = 0;
                $total_discount_amount_excluding_tax = 0;
                $total_discount_amount_tax = 0;
                $total_discount_amount = 0;
                $total_new_amount_excluding_tax = 0;
                $total_new_amount_tax = 0;
                $total_new_amount = 0;
                $total_in_order_amount_excluding_tax = 0;
                $total_in_order_amount_tax = 0;
                $total_in_order_amount = 0;
                $nb_paint = 0;

                if(count($data['shock_works']) > 0){
                    foreach ($data['shock_works'] as $shockWork) {
                        $discount = $shockWork['discount'];
                        $discount_amount_excluding_tax = ceil(($shockWork['discount'] * $shockWork['amount']) / 100);
                        $discount_amount_tax = ceil((config('services.settings.tax_rate') * $discount_amount_excluding_tax) / 100);
                        $discount_amount = ceil($discount_amount_excluding_tax + $discount_amount_tax);

                        $obsolescence_rate = $shockWork['obsolescence_rate'];
                        $obsolescence_amount_excluding_tax = ceil(($shockWork['obsolescence_rate'] * ($shockWork['amount'] - $discount_amount_excluding_tax)) / 100);
                        $obsolescence_amount_tax = ceil((config('services.settings.tax_rate') * $obsolescence_amount_excluding_tax) / 100);
                        $obsolescence_amount = ceil($obsolescence_amount_excluding_tax + $obsolescence_amount_tax);
            
                        $recovery_amount_excluding_tax = $shockWork['recovery_amount'];
                        $recovery_amount_tax = 0;
                        $recovery_amount = ceil($recovery_amount_excluding_tax + $recovery_amount_tax);
            
                        $new_amount_excluding_tax = ceil($shockWork['amount'] - ($obsolescence_amount_excluding_tax + $discount_amount_excluding_tax));
                        if($assignment->expertise_type_id == ExpertiseType::where('code', ExpertiseTypeEnum::EVALUATION)->first()->id || $assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()->id){
                            $new_amount_tax = 0;
                        } else {
                            $new_amount_tax = ceil((config('services.settings.tax_rate') * $new_amount_excluding_tax) / 100);
                        }
                        $new_amount = ceil($new_amount_excluding_tax + $new_amount_tax);
            
                        $shock_works[] = [
                            'supply_id' => $shockWork['supply_id'],
                            'supply_label' => Supply::where('id', $shockWork['supply_id'])->first()->label,
                            'disassembly' => $shockWork['disassembly'] ?? false,
                            'replacement' => $shockWork['replacement'] ?? false,
                            'repair' => $shockWork['repair'] ?? false,
                            'paint' => $shockWork['paint'] ?? false,
                            'obsolescence' => $shockWork['obsolescence'] ?? false,
                            'control' => $shockWork['control'] ?? false,
                            'in_order' => $shockWork['in_order'] ?? false,
                            'comment' => $shockWork['comment'],
                            'amount' => $shockWork['amount'],
                            'obsolescence_rate' => $obsolescence_rate,
                            'obsolescence_amount_excluding_tax' => $obsolescence_amount_excluding_tax,
                            'obsolescence_amount_tax' => $obsolescence_amount_tax,
                            'obsolescence_amount' => $obsolescence_amount,
                            'recovery_amount_excluding_tax' => $recovery_amount_excluding_tax,
                            'recovery_amount_tax' => $recovery_amount_tax,
                            'recovery_amount' => $recovery_amount,
                            'discount' => $discount,
                            'discount_amount_excluding_tax' => $discount_amount_excluding_tax,
                            'discount_amount_tax' => $discount_amount_tax,
                            'discount_amount' => $discount_amount,
                            'new_amount_excluding_tax' => $new_amount_excluding_tax,
                            'new_amount_tax' => $new_amount_tax,
                            'new_amount' => $new_amount,
                        ];
            
                        if($shockWork['paint'] == true){
                            $nb_paint += 1;
                        }
            
                        $total_obsolescence_amount_excluding_tax += $obsolescence_amount_excluding_tax;
                        $total_obsolescence_amount_tax += $obsolescence_amount_tax;
                        $total_obsolescence_amount += $obsolescence_amount;
                        $total_recovery_amount_excluding_tax += $recovery_amount_excluding_tax;
                        $total_recovery_amount_tax += $recovery_amount_tax;
                        $total_recovery_amount += $recovery_amount;
                        $total_discount_amount_excluding_tax += $discount_amount_excluding_tax;
                        $total_discount_amount_tax += $discount_amount_tax;
                        $total_discount_amount += $discount_amount;
                        $total_new_amount_excluding_tax += $new_amount_excluding_tax;
                        $total_new_amount_tax += $new_amount_tax;
                        $total_new_amount += $new_amount;
                        if($shockWork['in_order'] == true){
                            $total_in_order_amount_excluding_tax += $new_amount_excluding_tax;
                            $total_in_order_amount_tax += $new_amount_tax;
                            $total_in_order_amount += $new_amount;
                        }
                    }
                }
        
                $workforces = [];
                $total_workforce_amount_excluding_tax = 0;
                $total_workforce_amount_tax = 0;
                $total_workforce_amount = 0;
                $workforce_amount_excluding_tax = 0;
                $workforce_amount_tax = 0;
                $workforce_amount = 0;
        
                $nb_hours_paint_product = 0;
                $all_paint = false;
                
                if(count($data['workforces']) > 0 && $data['paint_type_id'] && $data['hourly_rate_id']){
                    foreach ($data['workforces'] as $workforce) {
                        if($workforce['workforce_type_id'] == WorkforceType::where('code', WorkforceTypeEnum::PAINT)->first()->id){
                            if($nb_paint == 0){
                                $total = 0;
                            } else {
                                if($nb_paint == 1){
                                    $painting_price = PaintingPrice::where(['hourly_rate_id' => HourlyRate::where('id', $data['hourly_rate_id'])->first()->id, 'paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::ONE)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first();
                                    $nb_hours_paint_product += $workforce['nb_hours'];
                                } elseif($nb_paint == 2){
                                    $painting_price = PaintingPrice::where(['hourly_rate_id' => HourlyRate::where('id', $data['hourly_rate_id'])->first()->id, 'paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::TWO)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first();
                                    $nb_hours_paint_product += $workforce['nb_hours'];
                                } else {
                                    $painting_price = PaintingPrice::where(['hourly_rate_id' => HourlyRate::where('id', $data['hourly_rate_id'])->first()->id, 'paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::THREE)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first();
                                    $nb_hours_paint_product += $workforce['nb_hours'];
                                } 

                                if($workforce['all_paint'] == true){
                                    $all_paint = true;
                                    $painting_price = PaintingPrice::where(['hourly_rate_id' => HourlyRate::where('id', $data['hourly_rate_id'])->first()->id, 'paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::ALL)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first();
                                    $nb_hours_paint_product += $workforce['nb_hours'];
                                }
                                $total = $painting_price ? $workforce['nb_hours'] * $painting_price->param_1 + $painting_price->param_2 : 0;
                            }
                        } else {
                            $total = $workforce['nb_hours'] * HourlyRate::where(['id' => $data['hourly_rate_id'], 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first()->value;
                        }

                        $amount_excluding_tax = ceil($total - ($total * $workforce['discount'] / 100));
                        if($data['with_tax'] == false){
                            $amount_tax = 0;
                        } else {
                            $amount_tax = ceil((config('services.settings.tax_rate') * $amount_excluding_tax) / 100);
                        }
                        $amount = ceil($amount_excluding_tax + $amount_tax);
            
                        $workforces[] = [
                            'workforce_type_id' => $workforce['workforce_type_id'],
                            'workforce_type_label' => WorkforceType::where('id', $workforce['workforce_type_id'])->first()->label,
                            'nb_hours' => $workforce['nb_hours'],
                            'work_fee' => ceil(HourlyRate::where(['id' => $data['hourly_rate_id'], 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first()->value),
                            'with_tax' => ceil($data['with_tax']),
                            'discount' => $workforce['discount'],
                            'amount_excluding_tax' => $amount_excluding_tax,
                            'amount_tax' => $amount_tax,
                            'amount' => $amount,
                        ];
            
                        $total_workforce_amount_excluding_tax += $amount_excluding_tax;
                        $total_workforce_amount_tax += $amount_tax;
                        $total_workforce_amount += $amount;
                    }
                }
                
                if($data['paint_type_id'] && $data['hourly_rate_id']){
                    if($nb_paint == 0){
                        $paint_product_price = null;
                    } elseif($nb_paint == 1){
                        $paint_product_price = PaintProductPrice::where(['paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::ONE)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first();
                    } elseif($nb_paint == 2){
                        $paint_product_price = PaintProductPrice::where(['paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::TWO)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first();
                    } else {
                        $paint_product_price = PaintProductPrice::where(['paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::THREE)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first();
                    } 

                    if($all_paint == true){
                        $paint_product_price = PaintProductPrice::where(['paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::ALL)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first();
                    }

                    $total_paint_product_amount_excluding_tax = $paint_product_price ? ceil($paint_product_price->value * $nb_hours_paint_product) : 0;
                    $total_paint_product_amount_tax = ceil((config('services.settings.tax_rate') * $total_paint_product_amount_excluding_tax) / 100);
                    $total_paint_product_amount = ceil($total_paint_product_amount_excluding_tax + $total_paint_product_amount_tax);
            
                    $total_small_supply_amount_excluding_tax = ceil(($total_new_amount_excluding_tax + $total_workforce_amount_excluding_tax + $total_paint_product_amount_excluding_tax + $total_recovery_amount_excluding_tax) * (config('services.settings.small_supply_rate') / 100));
                    $total_small_supply_amount_tax = ceil((config('services.settings.tax_rate') * $total_small_supply_amount_excluding_tax) / 100);
                    $total_small_supply_amount = ceil($total_small_supply_amount_excluding_tax + $total_small_supply_amount_tax);
            
                }
        
                $shocks[] = [
                    'shock_point_id' => $data['shock_point_id'],
                    'shock_point_label' => ShockPoint::where('id', $data['shock_point_id'])->first()->label,
                    'total_in_order_amount_excluding_tax' => $total_in_order_amount_excluding_tax ?? 0,
                    'total_in_order_amount_tax' => $total_in_order_amount_tax ?? 0,
                    'total_in_order_amount' => $total_in_order_amount ?? 0,
                    'total_obsolescence_amount_excluding_tax' => $total_obsolescence_amount_excluding_tax ?? 0,
                    'total_obsolescence_amount_tax' => $total_obsolescence_amount_tax ?? 0,
                    'total_obsolescence_amount' => $total_obsolescence_amount ?? 0,
                    'total_recovery_amount_excluding_tax' => $total_recovery_amount_excluding_tax ?? 0,
                    'total_recovery_amount_tax' => $total_recovery_amount_tax ?? 0,
                    'total_recovery_amount' => $total_recovery_amount ?? 0,
                    'total_discount_amount_excluding_tax' => $total_discount_amount_excluding_tax ?? 0,
                    'total_discount_amount_tax' => $total_discount_amount_tax ?? 0,
                    'total_discount_amount' => $total_discount_amount ?? 0,
                    'total_new_amount_excluding_tax' => $total_new_amount_excluding_tax ?? 0,
                    'total_new_amount_tax' => $total_new_amount_tax ?? 0,
                    'total_new_amount' => $total_new_amount ?? 0,
                    'shock_works' => $shock_works,
                    'total_workforce_amount_excluding_tax' => $total_workforce_amount_excluding_tax ?? 0,
                    'total_workforce_amount_tax' => $total_workforce_amount_tax ?? 0,
                    'total_workforce_amount' => $total_workforce_amount ?? 0,
                    'total_paint_product_amount_excluding_tax' => $total_paint_product_amount_excluding_tax ?? 0,
                    'total_paint_product_amount_tax' => $total_paint_product_amount_tax ?? 0,
                    'total_paint_product_amount' => $total_paint_product_amount ?? 0,
                    'total_small_supply_amount_excluding_tax' => $total_small_supply_amount_excluding_tax ?? 0,
                    'total_small_supply_amount_tax' => $total_small_supply_amount_tax ?? 0,
                    'total_small_supply_amount' => $total_small_supply_amount ?? 0,
                    'workforces' => $workforces,
                    'total_shock_amount_excluding_tax' => ceil($total_new_amount_excluding_tax + $total_workforce_amount_excluding_tax + $total_paint_product_amount_excluding_tax + $total_small_supply_amount_excluding_tax + $total_recovery_amount_excluding_tax) ?? 0,
                    'total_shock_amount_tax' => ceil($total_new_amount_tax + $total_workforce_amount_tax + $total_paint_product_amount_tax + $total_small_supply_amount_tax + $total_recovery_amount_tax) ?? 0,
                    'total_shock_amount' => ceil($total_new_amount + $total_workforce_amount + $total_paint_product_amount + $total_small_supply_amount + $total_recovery_amount) ?? 0,
                ];
    
                $shocks_amount_excluding_tax = $total_new_amount_excluding_tax + $total_workforce_amount_excluding_tax + $total_paint_product_amount_excluding_tax + $total_small_supply_amount_excluding_tax + $total_recovery_amount_excluding_tax;
                $shocks_amount_tax = $total_new_amount_tax + $total_workforce_amount_tax + $total_paint_product_amount_tax + $total_small_supply_amount_tax + $total_recovery_amount_tax;
                $shocks_amount = $total_new_amount + $total_workforce_amount + $total_paint_product_amount + $total_small_supply_amount + $total_recovery_amount;
            }
        }

        $other_costs = [];
        $total_other_costs_amount_excluding_tax = 0;
        $total_other_costs_amount_tax = 0;
        $total_other_costs_amount = 0;
        $other_costs_amount_excluding_tax = 0;
        $other_costs_amount_tax = 0;
        $other_costs_amount = 0;

        if(count($request->get('other_costs')) > 0){
            foreach ($request->get('other_costs') as $item) {
                $other_costs_amount_excluding_tax = $item['amount'];
                $other_costs_amount_tax = 0;
                $other_costs_amount = $other_costs_amount_excluding_tax + $other_costs_amount_tax;
    
                $other_costs[] = [
                    'other_cost_type_id' => $item['other_cost_type_id'],
                    'other_cost_type_label' => OtherCostType::where('id', $item['other_cost_type_id'])->first()->label,
                    'other_costs_amount_excluding_tax' => ceil($other_costs_amount_excluding_tax),
                    'other_costs_amount_tax' => ceil($other_costs_amount_tax),
                    'other_costs_amount' => ceil($other_costs_amount),
                ];
    
                $total_other_costs_amount_excluding_tax += $other_costs_amount_excluding_tax;
                $total_other_costs_amount_tax += $other_costs_amount_tax;
                $total_other_costs_amount += $other_costs_amount;
            }
        }

        $total_amount_excluding_tax = $shocks_amount_excluding_tax + $total_other_costs_amount_excluding_tax;
        $total_amount_tax = $shocks_amount_tax + $total_other_costs_amount_tax;
        $total_amount = $shocks_amount + $total_other_costs_amount;

        if($vehicle->vehicleGenre && $vehicle->vehicleEnergy){
            $marketValueService = app(MarketValueService::class);
            $result = json_decode(json_encode($marketValueService->calculateTheoreticalMarketValue($vehicle->vehicleGenre->id, $vehicle->vehicleEnergy->id, $vehicle->new_market_value, $vehicle->mileage, $vehicle->first_entry_into_circulation_date, $request->expertise_date)));
            
            $kilometric_incidence = 0;
            $is_up = null;
            if ($vehicle->vehicleEnergy->code == 'VE01') {
                $max_mileage_essence_per_month = $vehicle->vehicleGenre->max_mileage_essence_per_year / 12;
                $kilometric_incidence = (($max_mileage_essence_per_month * $result->month_diff) - $vehicle->mileage) * 25;
            } else {
                $max_mileage_diesel_per_month = $vehicle->vehicleGenre->max_mileage_diesel_per_year / 12;
                $kilometric_incidence = (($max_mileage_diesel_per_month * $result->month_diff) - $vehicle->mileage) * 40;
            }

            if ($kilometric_incidence > 0) {
                $is_up = true;
            } else {
                $is_up = false;
                $kilometric_incidence = -1 * $kilometric_incidence;
            }

            $expertise_date = $result->expertise_date;
            $first_entry_into_circulation_date = $result->first_entry_into_circulation_date;
            $vehicle_age = $result->vehicle_age;
            $theorical_depreciation_rate = $result->theorical_depreciation_rate;
            $theorical_vehicle_market_value = $result->theorical_vehicle_market_value;
            
            $less_value_work = $total_amount;
            $special_accessory = $request->special_accessory ?? 0;
            $market_incidence = ceil($result->vehicle_new_value * $request->market_incidence_rate / 100);

            if($kilometric_incidence > ($theorical_vehicle_market_value / 2)){
                $kilometric_incidence = $theorical_vehicle_market_value / 2;
            }
            
            if($assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()->id){
                $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence - $less_value_work + $special_accessory : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence - $less_value_work + $special_accessory;
            } else {
                $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence;
            }

            $depreciation_rate = $result->vehicle_new_value > 0 ? number_format(100 - ($vehicle_market_value * 100 / $result->vehicle_new_value), 2, ',', '') : 0;
            $depreciation_rate = floatval(str_replace(',', '.', $depreciation_rate));
            
            $evaluations[] = [
                'vehicle' => $vehicle,
                'expertise_date' => $expertise_date,
                'first_entry_into_circulation_date' => $first_entry_into_circulation_date,
                'vehicle_new_value' => $result->vehicle_new_value,
                'vehicle_age' => $vehicle_age,
                'vehicle_max_mileage_essence_per_year' => $vehicle->vehicleGenre->max_mileage_essence_per_year,
                'diff_year' => $result->year_diff,
                'diff_month' => $result->month_diff,
                'theorical_depreciation_rate' => $theorical_depreciation_rate,
                'theorical_vehicle_market_value' => $theorical_vehicle_market_value,
                'market_incidence_rate' => $request->market_incidence_rate,
                'less_value_work' => $less_value_work,
                'special_accessory' => $special_accessory,
                'is_up' => $is_up,
                'kilometric_incidence' => $kilometric_incidence,
                'market_incidence' => $market_incidence,
                'depreciation_rate' => $depreciation_rate,
                'vehicle_market_value' =>  ceil($result->vehicle_new_value - ($result->vehicle_new_value * $depreciation_rate / 100)),
                'vehicle_market_value_rounded' =>  ceil($result->vehicle_new_value - ($result->vehicle_new_value * $depreciation_rate / 100)),
            ];

        }

        return $this->responseSuccess('Calcul effectué avec succès', [
            'ascertainments' => $ascertainments,
            'evaluations' => $vehicle->vehicleGenre && $vehicle->vehicleEnergy ? $evaluations : null,
            'shocks' => $shocks,
            'other_costs' => $other_costs,
            'shocks_amount_excluding_tax' => $shocks_amount_excluding_tax,
            'shocks_amount_tax' => $shocks_amount_tax,
            'shocks_amount' => $shocks_amount,
            'total_other_costs_amount_excluding_tax' => $total_other_costs_amount_excluding_tax,
            'total_other_costs_amount_tax' => $total_other_costs_amount_tax,
            'total_other_costs_amount' => $total_other_costs_amount,
            'total_amount_excluding_tax' => $total_amount_excluding_tax,
            'total_amount_tax' => $total_amount_tax,
            'total_amount' => $total_amount,
        ]);
    }

    /**
     * Calcul d'évaluation d'un dossier
     *
     * @authenticated
     */
    public function calculate_evaluation(CalculateEvaluationAssignmentRequest $request): JsonResponse
    {
        $evaluations = [];
        $vehicle = Vehicle::with('vehicleGenre', 'vehicleEnergy')->findOrFail($request->vehicle_id);
        
        $marketValueService = app(MarketValueService::class);
        $result = json_decode(json_encode($marketValueService->calculateTheoreticalMarketValue($vehicle->vehicleGenre->id, $vehicle->vehicleEnergy->id, $vehicle->new_market_value, $vehicle->mileage, $vehicle->first_entry_into_circulation_date, $request->expertise_date)));
        
        $kilometric_incidence = 0;
        $is_up = null;
        if ($vehicle->vehicleEnergy->code == 'VE01') {
            $max_mileage_essence_per_month = $vehicle->vehicleGenre->max_mileage_essence_per_year / 12;
            $kilometric_incidence = (($max_mileage_essence_per_month * $result->month_diff) - $vehicle->mileage) * 25;
        } else {
            $max_mileage_diesel_per_month = $vehicle->vehicleGenre->max_mileage_diesel_per_year / 12;
            $kilometric_incidence = (($max_mileage_diesel_per_month * $result->month_diff) -$vehicle->mileage) * 40;
        }

        if ($kilometric_incidence > 0) {
            $is_up = true;
        } else {
            $is_up = false;
            $kilometric_incidence = -1 * $kilometric_incidence;
        }

        $expertise_date = $result->expertise_date;
        $first_entry_into_circulation_date = $result->first_entry_into_circulation_date;
        $vehicle_age = $result->vehicle_age;
        $theorical_depreciation_rate = $result->theorical_depreciation_rate;
        $theorical_vehicle_market_value = $result->theorical_vehicle_market_value;
        
        $ascertainments = [];
        $ascertainment = $request->get('ascertainments');

        foreach ($ascertainment as $data) {
            $ascertainments[] = [
                'ascertainment_type_id' => $data['ascertainment_type_id'],
                'very_good' => $data['very_good'],
                'good' => $data['good'],
                'acceptable' => $data['acceptable'],
                'less_good' => $data['less_good'],
                'bad' => $data['bad'],
                'very_bad' => $data['very_bad'],
                'comment' => $data['comment'],
            ];
        }

        $shocks = [];
        $total_amount_excluding_tax = 0;
        $total_amount_tax = 0;
        $total_amount = 0;

        $shocks_amount_excluding_tax = 0;
        $shocks_amount_tax = 0;
        $shocks_amount = 0;
        
        if(count($request->get('shocks')) > 0){
            foreach ($request->get('shocks') as $data) {

                $shock_works = [];
                $new_amount_excluding_tax = 0;
                $new_amount_tax = 0;
                $new_amount = 0;
                $total_new_amount_excluding_tax = 0;
                $total_new_amount_tax = 0;
                $total_new_amount = 0;
    
                if(count($data['shock_works']) > 0){
                    foreach ($data['shock_works'] as $shockWork) {
                        $new_amount_excluding_tax = ceil($shockWork['amount']);
                        $new_amount_tax = ceil((config('services.settings.tax_rate') * $new_amount_excluding_tax) / 100);
                        $new_amount = ceil($new_amount_excluding_tax + $new_amount_tax);
            
                        $shock_works[] = [
                            'supply_id' => $shockWork['supply_id'],
                            'supply_label' => Supply::where('id', $shockWork['supply_id'])->first()->label,
                            'disassembly' => $shockWork['disassembly'],
                            'replacement' => $shockWork['replacement'],
                            'repair' => $shockWork['repair'],
                            'paint' => $shockWork['paint'],
                            'control' => $shockWork['control'],
                            'in_order' => $shockWork['in_order'],
                            'comment' => $shockWork['comment'],
                            'amount' => $new_amount,
                            'new_amount_excluding_tax' => $new_amount_excluding_tax,
                            'new_amount_tax' => $new_amount_tax,
                            'new_amount' => $new_amount,
                        ];
            
                        $total_new_amount_excluding_tax += $new_amount_excluding_tax;
                        $total_new_amount_tax += $new_amount_tax;
                        $total_new_amount += $new_amount;
                    }
                }
        
                $workforces = [];
                $total_workforce_amount_excluding_tax = 0;
                $total_workforce_amount_tax = 0;
                $total_workforce_amount = 0;
                $workforce_amount_excluding_tax = 0;
                $workforce_amount_tax = 0;
                $workforce_amount = 0;
        
                if(count($data['workforces']) > 0){
                    foreach ($data['workforces'] as $workforce) {
                        $amount_excluding_tax = ceil($workforce['amount']);
                        $amount_tax = 0;
                        $amount = ceil($amount_excluding_tax + $amount_tax);
            
                        $workforces[] = [
                            'workforce_type_id' => $workforce['workforce_type_id'],
                            'workforce_type_label' => WorkforceType::where('id', $workforce['workforce_type_id'])->first()->label,
                            'amount_excluding_tax' => $amount_excluding_tax,
                            'amount_tax' => $amount_tax,
                            'amount' => $amount,
                        ];
            
                        $total_workforce_amount_excluding_tax += $amount_excluding_tax;
                        $total_workforce_amount_tax += $amount_tax;
                        $total_workforce_amount += $amount;
                    }
                }
        
                $shocks[] = [
                    'shock_point_id' => $data['shock_point_id'],
                    'shock_point_label' => ShockPoint::where('id', $data['shock_point_id'])->first()->label,
                    'total_new_amount_excluding_tax' => $total_new_amount_excluding_tax ?? 0,
                    'total_new_amount_tax' => $total_new_amount_tax ?? 0,
                    'total_new_amount' => $total_new_amount ?? 0,
                    'shock_works' => $shock_works,
                    'total_workforce_amount_excluding_tax' => $total_workforce_amount_excluding_tax ?? 0,
                    'total_workforce_amount_tax' => $total_workforce_amount_tax ?? 0,
                    'total_workforce_amount' => $total_workforce_amount ?? 0,
                    'workforces' => $workforces,
                    'total_shock_amount_excluding_tax' => ceil($total_new_amount_excluding_tax + $total_workforce_amount_excluding_tax) ?? 0,
                    'total_shock_amount_tax' => ceil($total_new_amount_tax + $total_workforce_amount_tax) ?? 0,
                    'total_shock_amount' => ceil($total_new_amount + $total_workforce_amount) ?? 0,
                ];
    
                $shocks_amount_excluding_tax = $total_new_amount_excluding_tax + $total_workforce_amount_excluding_tax;
                $shocks_amount_tax = $total_new_amount_tax + $total_workforce_amount_tax;
                $shocks_amount = $total_new_amount + $total_workforce_amount;
            }
        }

        $other_costs = [];
        $total_other_costs_amount_excluding_tax = 0;
        $total_other_costs_amount_tax = 0;
        $total_other_costs_amount = 0;
        $other_costs_amount_excluding_tax = 0;
        $other_costs_amount_tax = 0;
        $other_costs_amount = 0;

        if(count($request->get('other_costs')) > 0){
            foreach ($request->get('other_costs') as $item) {
                $other_costs_amount_excluding_tax = ceil($item['amount']);
                $other_costs_amount_tax = 0;
                $other_costs_amount = ceil($other_costs_amount_excluding_tax + $other_costs_amount_tax);
    
                $other_costs[] = [
                    'other_cost_type_id' => $item['other_cost_type_id'],
                    'other_cost_type_label' => OtherCostType::where('id', $item['other_cost_type_id'])->first()->label,
                    'other_costs_amount_excluding_tax' => $other_costs_amount_excluding_tax,
                    'other_costs_amount_tax' => $other_costs_amount_tax,
                    'other_costs_amount' => $other_costs_amount,
                ];
    
                $total_other_costs_amount_excluding_tax += $other_costs_amount_excluding_tax;
                $total_other_costs_amount_tax += $other_costs_amount_tax;
                $total_other_costs_amount += $other_costs_amount;
            }
        }

        $total_amount_excluding_tax = $shocks_amount_excluding_tax + $total_other_costs_amount_excluding_tax;
        $total_amount_tax = $shocks_amount_tax + $total_other_costs_amount_tax;
        $total_amount = $shocks_amount + $total_other_costs_amount;

        $less_value_work = $total_amount;
        $special_accessory = $request->special_accessory ?? 0;
        $market_incidence = ceil($result->vehicle_new_value * $request->market_incidence_rate / 100);

        if($kilometric_incidence > ($theorical_vehicle_market_value / 2)){
            $kilometric_incidence = $theorical_vehicle_market_value / 2;
        }

        if($assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()->id){
            $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence - $less_value_work + $special_accessory : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence - $less_value_work + $special_accessory;
        } else {
            $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence;
        }

        $depreciation_rate = $result->vehicle_new_value > 0 ? number_format(100 - ($vehicle_market_value * 100 / $result->vehicle_new_value), 2, ',', '') : 0;
        $depreciation_rate = floatval(str_replace(',', '.', $depreciation_rate));
        
        $evaluations[] = [
            'vehicle' => $vehicle,
            'expertise_date' => $expertise_date,
            'first_entry_into_circulation_date' => $first_entry_into_circulation_date,
            'vehicle_new_value' => $result->vehicle_new_value,
            'vehicle_age' => $vehicle_age,
            'vehicle_max_mileage_essence_per_year' => $vehicle->vehicleGenre->max_mileage_essence_per_year,
            'diff_year' => $result->year_diff,
            'diff_month' => $result->month_diff,
            'theorical_depreciation_rate' => $theorical_depreciation_rate,
            'theorical_vehicle_market_value' => $theorical_vehicle_market_value,
            'market_incidence_rate' => $request->market_incidence_rate,
            'less_value_work' => $less_value_work,
            'special_accessory' => $special_accessory,
            'is_up' => $is_up,
            'kilometric_incidence' => $kilometric_incidence,
            'market_incidence' => $market_incidence,
            'depreciation_rate' => $depreciation_rate,
            'vehicle_market_value' =>  ceil($result->vehicle_new_value - ($result->vehicle_new_value * $depreciation_rate / 100)),
            'vehicle_market_value_rounded' =>  ceil($result->vehicle_new_value - ($result->vehicle_new_value * $depreciation_rate / 100)),
        ];

        return $this->responseSuccess('Calcul effectué avec succès', [
            'ascertainments' => $ascertainments,
            'shocks' => $shocks,
            'other_costs' => $other_costs,
            'shocks_amount_excluding_tax' => $shocks_amount_excluding_tax,
            'shocks_amount_tax' => $shocks_amount_tax,
            'shocks_amount' => $shocks_amount,
            'total_other_costs_amount_excluding_tax' => $total_other_costs_amount_excluding_tax,
            'total_other_costs_amount_tax' => $total_other_costs_amount_tax,
            'total_other_costs_amount' => $total_other_costs_amount,
            'total_amount_excluding_tax' => $total_amount_excluding_tax,
            'total_amount_tax' => $total_amount_tax,
            'total_amount' => $total_amount,
            'evaluations' => $evaluations,
        ]);
    }


    /**
     * Évaluer un dossier
     *
     * @authenticated
     */
    public function evaluate(EvaluateAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        // if($assignment->status_id != Status::where('code', StatusEnum::REALIZED)->first()->id){
        //     return $this->responseUnprocessable("Le dossier n'est pas encore réalisé ou est déjà rédigé, veuillez le réaliser avant de le rédiger.", null);
        // }

        $vehicle = Vehicle::with('vehicleGenre', 'vehicleEnergy')->findOrFail($assignment->vehicle_id);

        $marketValueService = app(MarketValueService::class);
        $result = json_decode(json_encode($marketValueService->calculateTheoreticalMarketValue($vehicle->vehicleGenre->id, $vehicle->vehicleEnergy->id, $vehicle->new_market_value, $vehicle->mileage, $vehicle->first_entry_into_circulation_date, $request->expertise_date)));
        
        $kilometric_incidence = 0;
        $is_up = null;
        if ($vehicle->vehicleEnergy->code == 'VE01') {
            $max_mileage_essence_per_month = $vehicle->vehicleGenre->max_mileage_essence_per_year / 12;
            $kilometric_incidence = (($max_mileage_essence_per_month * $result->month_diff) - $vehicle->mileage) * 25;
        } else {
            $max_mileage_diesel_per_month = $vehicle->vehicleGenre->max_mileage_diesel_per_year / 12;
            $kilometric_incidence = (($max_mileage_diesel_per_month * $result->month_diff) -$vehicle->mileage) * 40;
        }

        if ($kilometric_incidence > 0) {
            $is_up = true;
        } else {
            $is_up = false;
            $kilometric_incidence = -1 * $kilometric_incidence;
        }

        $expertise_date = $result->expertise_date;
        $first_entry_into_circulation_date = $result->first_entry_into_circulation_date;
        $vehicle_age = $result->vehicle_age;
        $theorical_depreciation_rate = $result->theorical_depreciation_rate;
        $theorical_vehicle_market_value = $result->theorical_vehicle_market_value;

        $ascertainments = $request->get('ascertainments');

        if(count($ascertainments) > 0){
            foreach ($ascertainments as $data) {
                $ascertainment = Ascertainment::create([
                    'assignment_id' => $assignment->id,
                    'ascertainment_type_id' => $data['ascertainment_type_id'],
                    'very_good' => $data['very_good'],
                    'good' => $data['good'],
                    'acceptable' => $data['acceptable'],
                    'less_good' => $data['less_good'],
                    'bad' => $data['bad'],
                    'very_bad' => $data['very_bad'],
                    'comment' => $data['comment'],
                    'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]);
            }
        }

        $shocks = $request->get('shocks');

        if(count($shocks) > 0){
            foreach ($shocks as $data) {
                $shock = Shock::create([
                    'assignment_id' => $assignment->id,
                    'shock_point_id' => $data['shock_point_id'],
                    'paint_type_id' => $data['paint_type_id'],
                    'hourly_rate_id' => $data['hourly_rate_id'],
                    'with_tax' => $data['with_tax'],
                    'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]);

                if(count($data['shock_works']) > 0){
                    foreach ($data['shock_works'] as $item) {
                        $new_amount_excluding_tax = ceil($item['amount']);
                        $new_amount_tax = ceil((config('services.settings.tax_rate') * $new_amount_excluding_tax) / 100);
                        $new_amount = ceil($new_amount_excluding_tax + $new_amount_tax);
                        
                        $shockWork = ShockWork::create([
                            'shock_id' => $shock->id,
                            'supply_id' => $item['supply_id'],
                            'disassembly' => $item['disassembly'],
                            'replacement' => $item['replacement'],
                            'repair' => $item['repair'],
                            'paint' => $item['paint'],
                            'control' => $item['control'],
                            'in_order' => $item['in_order'],
                            'comment' => $item['comment'],
                            'new_amount_excluding_tax' => $new_amount_excluding_tax,
                            'new_amount_tax' => $new_amount_tax,
                            'new_amount' => $new_amount,
                            'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                            'created_by' => auth()->user()->id,
                            'updated_by' => auth()->user()->id,
                        ]);
                    }
        
                }
    
                if(count($data['workforces']) > 0){
                    foreach ($data['workforces'] as $item) {
                        
                        $amount_excluding_tax = ceil($item['amount']);
                        $amount_tax = 0;
                        $amount = ceil($amount_excluding_tax + $amount_tax);

                        $workforce = Workforce::create([
                            'shock_id' => $shock->id,
                            'workforce_type_id' => $item['workforce_type_id'],
                            'amount_excluding_tax' => $amount_excluding_tax,
                            'amount_tax' => $amount_tax,
                            'amount' => $amount,
                            'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                            'created_by' => auth()->user()->id,
                            'updated_by' => auth()->user()->id,
                        ]);
                    }
                }

                $total_new_amount_excluding_tax = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('new_amount_excluding_tax');
                $total_new_amount_tax = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('new_amount_tax');
                $total_new_amount = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('new_amount');
    
                $total_workforce_amount_excluding_tax = Workforce::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount_excluding_tax');
                $total_workforce_amount_tax = Workforce::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount_tax');
                $total_workforce_amount = Workforce::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount');
    
                $shock->update([
                    'shock_work_new_amount_excluding_tax' => ceil($total_new_amount_excluding_tax),
                    'shock_work_new_amount_tax' => ceil($total_new_amount_tax),
                    'shock_work_new_amount' => ceil($total_new_amount),
                    'workforce_amount_excluding_tax' => ceil($total_workforce_amount_excluding_tax),
                    'workforce_amount_tax' => ceil($total_workforce_amount_tax),
                    'workforce_amount' => ceil($total_workforce_amount),
                    'amount_excluding_tax' => ceil($total_new_amount_excluding_tax + $total_workforce_amount_excluding_tax),
                    'amount_tax' => ceil($total_new_amount_tax + $total_workforce_amount_tax),
                    'amount' => ceil($total_new_amount + $total_workforce_amount),
                ]);
            }
    
            $total_shock_amount_excluding_tax = ceil(Shock::where('assignment_id', $assignment->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount_excluding_tax'));
            $total_shock_amount_tax = ceil(Shock::where('assignment_id', $assignment->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount_tax'));
            $total_shock_amount = ceil(Shock::where('assignment_id', $assignment->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount'));
        }

        $other_costs = $request->get('other_costs');

        if(count($other_costs) > 0){
            foreach ($other_costs as $data) {
                $other_cost = OtherCost::create([
                    'assignment_id' => $assignment->id,
                    'other_cost_type_id' => $data['other_cost_type_id'],
                    'amount_excluding_tax' => ceil($data['amount']),
                    'amount_tax' => 0,
                    'amount' => ceil($data['amount']),
                    'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]);
            }
        }

        $total_other_cost_amount_excluding_tax = OtherCost::where('assignment_id', $assignment->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount_excluding_tax');
        $total_other_cost_amount_tax = OtherCost::where('assignment_id', $assignment->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount_tax');
        $total_other_cost_amount = OtherCost::where('assignment_id', $assignment->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount');

        $total_amount_excluding_tax = $total_shock_amount_excluding_tax + $total_other_cost_amount_excluding_tax;
        $total_amount_tax = $total_shock_amount_tax + $total_other_cost_amount_tax;
        $total_amount = $total_shock_amount + $total_other_cost_amount;

        $less_value_work = $total_amount;
        $special_accessory = $request->special_accessory ?? 0;
        $market_incidence = ceil($result->vehicle_new_value * $request->market_incidence_rate / 100);

        if($kilometric_incidence > ($theorical_vehicle_market_value / 2)){
            $kilometric_incidence = $theorical_vehicle_market_value / 2;
        }

        if($assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()->id){
            $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence - $less_value_work + $special_accessory : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence - $less_value_work + $special_accessory;
        } else {
            $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence;
        }

        $depreciation_rate = $result->vehicle_new_value > 0 ? number_format(100 - ($vehicle_market_value * 100 / $result->vehicle_new_value), 2, ',', '') : 0;
        $depreciation_rate = floatval(str_replace(',', '.', $depreciation_rate));

        $evaluations = [
            'vehicle_age' => $result->vehicle_age,
            'diff_year' => $result->year_diff,
            'diff_month' => $result->month_diff,
            'theorical_depreciation_rate' => $theorical_depreciation_rate,
            'theorical_vehicle_market_value' => $theorical_vehicle_market_value,
            'market_incidence_rate' => $request->market_incidence_rate,
            'less_value_work' => $less_value_work,
            'special_accessory' => $special_accessory,
            'is_up' => $is_up,
            'kilometric_incidence' => $kilometric_incidence,
            'market_incidence' => $market_incidence,
            'depreciation_rate' => $depreciation_rate,
            'vehicle_market_value' =>  ceil($result->vehicle_new_value - ($result->vehicle_new_value * $depreciation_rate / 100)),
            'vehicle_market_value_rounded' =>  ceil($result->vehicle_new_value - ($result->vehicle_new_value * $depreciation_rate / 100)),
        ];

        $assignment->update([
            'general_state_id' => $request->general_state_id,
            'technical_conclusion_id' => $request->technical_conclusion_id,
            'seen_before_work_date' => $request->seen_before_work_date,
            'seen_during_work_date' => $request->seen_during_work_date,
            'seen_after_work_date' => $request->seen_after_work_date,
            'contact_date' => $request->contact_date,
            'expertise_place' => $request->expertise_place,
            'assured_value' => $request->assured_value,
            'salvage_value' => $request->salvage_value,
            'new_market_value' => $result->vehicle_new_value,
            'depreciation_rate' => $result->theorical_depreciation_rate,
            'market_value' => $result->theorical_vehicle_market_value,
            'expert_remark' => $request->expert_remark,
            'work_sheet_remark_id' => $request->work_sheet_remark_id,
            'report_remark_id' => $request->report_remark_id,
            'shock_amount_excluding_tax' => $total_shock_amount_excluding_tax,
            'shock_amount_tax' => $total_shock_amount_tax,
            'shock_amount' => $total_shock_amount,
            'other_cost_amount_excluding_tax' => $total_other_cost_amount_excluding_tax,
            'other_cost_amount_tax' => $total_other_cost_amount_tax,
            'other_cost_amount' => $total_other_cost_amount,
            'total_amount_excluding_tax' => $total_amount_excluding_tax,
            'total_amount_tax' => $total_amount_tax,
            'total_amount' => $total_amount,
            'evaluations' => json_encode($evaluations),
            'status_id' => Status::where('code', StatusEnum::IN_EDITING)->first()->id,
            'updated_by' => auth()->user()->id,
            'edited_by' => auth()->user()->id,
            'edited_at' => Carbon::now(),
        ]);
        
        dispatch(new GenerateExpertiseReportPdfJob($assignment));
            
        return $this->responseCreated('Opération effectuée avec succès ', new AssignmentResource($assignment));
    }

    /**
     * Ajouter un dossier
     *
     * @authenticated
     */
    public function store(CreateAssignmentRequest $request): JsonResponse
    {
        if($request->assignment_type_id != AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()->id){
            $now = Carbon::now();
            $annee = date("Y");
            $mois_jour_heure = date("mdH");
            $time = date("is");
            $today = $annee.'_'.$mois_jour_heure.'_'.$time;

            $year = substr($annee, -2);
            $month = date("m");
            $suffix = $month.'-'.$year.'-BCA';
            $reference = 'D'.$today;

            if($request->claim_number && Assignment::where('claim_number', $request->claim_number)->where(function($query){
                $query->where('status_id', Status::where('code', StatusEnum::OPENED)->first()->id)
                    ->orWhere('status_id', Status::where('code', StatusEnum::REALIZED)->first()->id)
                    ->orWhere('status_id', Status::where('code', StatusEnum::EDITED)->first()->id)
                    ->orWhere('status_id', Status::where('code', StatusEnum::VALIDATED)->first()->id);
            })->count() > 0){
                return $this->responseUnprocessable('Le numéro de sinistre existe déjà pour un dossier ouvert, réalisé, rédigé ou validé');
            }

            $insurer = Entity::find($request->insurer_id);

            $last_assignment = Assignment::where('reference_updated_at', 'like', Carbon::now()->format('Y-m').'%')->where('assignment_type_id', $request->assignment_type_id);
            if(!$last_assignment){
                $last_assignment = Assignment::where('created_at', 'like', Carbon::now()->format('Y-m').'%')->where('assignment_type_id', $request->assignment_type_id);
            }
            
            if(AssignmentTypeEnum::INSURER->value && $request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()->id){
                $latest_assignment = Assignment::join('entities', 'assignments.insurer_id', '=', 'entities.id')
                                    ->where('reference_updated_at', 'like', Carbon::now()->format('Y-m').'%')->where('assignment_type_id', $request->assignment_type_id)->where(function($query) use ($request, $insurer){
                                        $query->where(['insurer_id' => $insurer->id])->orWhere(['broker_id' => $request->insurer_id]);
                                    })
                                    ->latest('reference_updated_at')->first();

                if($latest_assignment && $insurer->entity_type_id == EntityType::where('code', EntityTypeEnum::BROKER)->first()->id){
                    $last_assignment = $latest_assignment;
                } else {
                    $last_assignment = $last_assignment->where(['broker_id' => null])->where(function($query) use ($request, $insurer){
                                            $query->where(['insurer_id' => $insurer->id])->orWhere(['broker_id' => $request->insurer_id]);
                                        })->latest('reference_updated_at')->first();
                }
            } else {
                $last_assignment = $last_assignment->latest('reference_updated_at')->first();
            }

            if($last_assignment){
                $prefix = explode("-", $last_assignment->reference)[0];
                $number = preg_match_all('/\d+/', $prefix, $matches);
                $number = $matches[0][0];
                $number = $number + 1;
                $prefix = preg_split('/\d+/', $prefix);
                if(mb_strlen($prefix[0]) == 1){
                    $prefix = $prefix[0][0];
                } else {
                    $prefix = $prefix[0];
                }
                $length = mb_strlen($number);
                if($length == 1){
                    $reference = $prefix.'0000'.$number.'-'.$suffix;
                } else if($length == 2){
                    $reference = $prefix.'000'.$number.'-'.$suffix;
                } else if($length == 3){
                    $reference = $prefix.'00'.$number.'-'.$suffix;
                } else if($length == 4){
                    $reference = $prefix.'0'.$number.'-'.$suffix;
                } else {
                    $reference = $prefix.$number.'-'.$suffix;
                }
                if(Assignment::where('reference', $reference)->exists()){
                    $assignment = Assignment::where('reference', $reference)->first();
                    
                    $prefix = explode("-", $assignment->reference)[0];
                    $number = preg_match_all('/\d+/', $prefix, $matches);
                    $number = $matches[0][0];
                    $number = $number + 1;
                    $prefix = preg_split('/\d+/', $prefix);
                    if(mb_strlen($prefix[0]) == 1){
                        $prefix = $prefix[0][0];
                    } else {
                        $prefix = $prefix[0];
                    }
                    $length = mb_strlen($number);
                    if($length == 1){
                        $reference = $prefix.'0000'.$number.'-'.$suffix;
                    } else if($length == 2){
                        $reference = $prefix.'000'.$number.'-'.$suffix;
                    } else if($length == 3){
                        $reference = $prefix.'00'.$number.'-'.$suffix;
                    } else if($length == 4){
                        $reference = $prefix.'0'.$number.'-'.$suffix;
                    } else {
                        $reference = $prefix.$number.'-'.$suffix;
                    }
                }
            } else {
                if(AssignmentTypeEnum::INSURER->value && $request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()->id){
                    if(EntityCodeEnum::NSIA->value &&$request->insurer_id == Entity::where('code', EntityCodeEnum::NSIA)->first()->id){
                        $reference = 'G00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::AXA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::AXA)->first()->id){
                        $reference = 'B00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::SANLAM_ALLIANZ->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SANLAM_ALLIANZ)->first()->id){
                        $reference = 'K00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::GNA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::GNA)->first()->id){
                        $reference = 'D00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::ATLANTIQUE->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::ATLANTIQUE)->first()->id){
                        $reference = 'E00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::COMAR->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::COMAR)->first()->id){
                        $reference = 'C00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::AFG->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::AFG)->first()->id){
                        $reference = 'AF00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::ATLANTA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::ATLANTA)->first()->id){
                        $reference = 'A00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::ACTIVA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::ACTIVA)->first()->id){
                        $reference = 'L00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::SIDAM->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SIDAM)->first()->id){
                        $reference = 'F00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::SUNU->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SUNU)->first()->id){
                        $reference = 'S00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::SCHIBA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SCHIBA)->first()->id){
                        $reference = 'H00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::SONAM->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SONAM)->first()->id){
                        $reference = 'H00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::LEADWAY->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::LEADWAY)->first()->id){
                        $reference = 'V00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::AMSA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::AMSA)->first()->id){
                        $reference = 'P00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::ACTIVA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::ACTIVA)->first()->id){
                        $reference = 'L00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::SERENITY->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SERENITY)->first()->id){
                        $reference = 'M00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::COMAR->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::COMAR)->first()->id){
                        $reference = 'C00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::SAAR->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SAAR)->first()->id){
                        $reference = 'I00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::WAFA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::WAFA)->first()->id){
                        $reference = 'W00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::CORIS->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::CORIS)->first()->id){
                        $reference = 'Y00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::BNICB->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::BNICB)->first()->id){
                        $reference = 'Z00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::SMABTP->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SMABTP)->first()->id){
                        $reference = 'SM00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::LOYALE->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::LOYALE)->first()->id){
                        $reference = 'LL00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::AACI->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::AACI)->first()->id){
                        $reference = 'X00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::SONAR->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SONAR)->first()->id){
                        $reference = 'SO00001'.'-'.$suffix;
                    }

                    if(EntityCodeEnum::ASCOMA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::ASCOMA)->first()->id){
                        $reference = 'J00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::OLEA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::OLEA)->first()->id){
                        $reference = 'Q00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::WTW->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::WTW)->first()->id){
                        $reference = 'N00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::FILHEL->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::FILHEL)->first()->id){
                        $reference = 'O00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::UNAP->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::UNAP)->first()->id){
                        $reference = 'U00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::BAROLD)->first()->id){
                        $reference = 'BA00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::OREOLE->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::OREOLE)->first()->id){
                        $reference = 'OR00001'.'-'.$suffix;
                    }
                    if(EntityCodeEnum::WALLYX->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::WALLYX)->first()->id){
                        $reference = 'WA00001'.'-'.$suffix;
                    }
                }

                if(AssignmentTypeEnum::PARTICULAR && $request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::PARTICULAR)->first()->id){
                    $reference = 'PC00001'.'-'.$suffix;
                }
        
                if(AssignmentTypeEnum::TAXI->value && $request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::TAXI)->first()->id){
                    $reference = 'TA00001'.'-'.$suffix;
                }
        
                if(AssignmentTypeEnum::EVALUATION->value && $request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()->id){
                    $reference = 'EV00001'.'-'.$suffix;
                }

                if(AssignmentTypeEnum::AGAINST_EXPERTISE->value && $request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::AGAINST_EXPERTISE)->first()->id){
                    $reference = 'CE00001'.'-'.$suffix;
                }

            }

            $vehicle = Vehicle::with('vehicleGenre', 'vehicleEnergy')->find($request->vehicle_id);

            $assignment = Assignment::create([
                'reference' => $reference,
                'vehicle_id' => $request->vehicle_id ?? null,
                'insurer_id' => $request->insurer_id ?? null,
                // 'broker_id' => $request->broker_id ?? null,
                'additional_insurer_id' => $request->additional_insurer_id ?? null,
                'client_id' => $request->client_id ?? null,
                'assignment_type_id' => $request->assignment_type_id,
                'expertise_type_id' => $request->expertise_type_id,
                'document_transmitted_id' => json_encode($request->document_transmitted_id),
                'assured_value' => $request->assured_value,
                'policy_number' => $request->policy_number,
                'claim_number' => $request->claim_number,
                'weproov_reference' => $request->weproov_reference,
                'claim_starts_at' => $request->claim_starts_at,
                'claim_ends_at' => $request->claim_ends_at,
                'expertise_date' => $request->expertise_date,
                'seen_before_work_date' => $request->expertise_date,
                'expertise_place' => $request->expertise_place,
                'circumstance' => $request->circumstance,
                'damage_declared' => $request->damage_declared,
                'point_noted' => $request->point_noted,
                'received_at' => $request->received_at,
                'vehicle_mileage' => $request->vehicle_mileage,
                'observation_realizer_name' => $request->observation_realizer_name,
                'observation_realizer_phone' => $request->observation_realizer_phone,
                'observation_place' => $request->observation_place,
                'observation_date' => $request->observation_date,
                'status_id' => Status::where('code', StatusEnum::OPENED)->first()->id,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
                'reference_updated_by' => auth()->user()->id,
                'reference_updated_at' => Carbon::now(),
            ]);

            if($assignment && $request->get('experts')){
                $experts = $request->get('experts');

                foreach ($experts as $data) {
                    $expert = AssignmentExpert::create([
                        'assignment_id' => $assignment->id,
                        'expert_id' => $data['expert_id'],
                        'date' => $data['date'],
                        'observation' => $data['observation'] ?? null,
                    ]);
                }
            }

            $this->syncAssignmentRepairers($assignment, $request);

            try {
                dispatch(new GenerateExpertiseSheetPdfJob($assignment));
            } catch (\Exception $e) {
                Log::error($e);
            }

            return $this->responseCreated('Opération effectuée avec succès', new AssignmentResource($assignment));

        }

        if($request->claim_number && Assignment::where('claim_number', $request->claim_number)->where(function($query){
            $query->where('status_id', Status::where('code', StatusEnum::OPENED)->first()->id)
                ->orWhere('status_id', Status::where('code', StatusEnum::REALIZED)->first()->id)
                ->orWhere('status_id', Status::where('code', StatusEnum::EDITED)->first()->id)
                ->orWhere('status_id', Status::where('code', StatusEnum::VALIDATED)->first()->id);
        })->count() > 0){
            return $this->responseUnprocessable('Le numéro de sinistre existe déjà pour un dossier ouvert, réalisé, rédigé ou validé');
        }

        $now = Carbon::now();
        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;

        $year = substr($annee, -2);
        $month = date("m");
        $suffix = $month.'-'.$year.'-BCA';
        $reference = 'D'.$today;

        $vehicle = Vehicle::with('vehicleGenre', 'vehicleEnergy')->find($request->vehicle_id);

        $assignment = Assignment::create([
            'reference' => $reference,
            'vehicle_id' => $request->vehicle_id ?? null,
            'insurer_id' => $request->insurer_id ?? null,
            // 'broker_id' => $request->broker_id ?? null,
            'additional_insurer_id' => $request->additional_insurer_id ?? null,
            'client_id' => $request->client_id ?? null,
            'assignment_type_id' => $request->assignment_type_id,
            'expertise_type_id' => $request->expertise_type_id,
            'document_transmitted_id' => json_encode($request->document_transmitted_id),
            'assured_value' => $request->assured_value,
            'policy_number' => $request->policy_number,
            'claim_number' => $request->claim_number,
            'weproov_reference' => $request->weproov_reference,
            'claim_starts_at' => $request->claim_starts_at,
            'claim_ends_at' => $request->claim_ends_at,
            'expertise_date' => $request->expertise_date,
            'seen_before_work_date' => $request->expertise_date,
            'expertise_place' => $request->expertise_place,
            'circumstance' => $request->circumstance,
            'damage_declared' => $request->damage_declared,
            'point_noted' => $request->point_noted,
            'received_at' => $request->received_at,
            'vehicle_mileage' => $request->vehicle_mileage,
            'observation_realizer_name' => $request->observation_realizer_name,
            'observation_realizer_phone' => $request->observation_realizer_phone,
            'observation_place' => $request->observation_place,
            'observation_date' => $request->observation_date,
            'status_id' => Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id,
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
            'reference_updated_by' => auth()->user()->id,
            'reference_updated_at' => Carbon::now(),
        ]);

        if($assignment && $request->get('experts')){
            $experts = $request->get('experts');

            foreach ($experts as $data) {
                $expert = AssignmentExpert::create([
                    'assignment_id' => $assignment->id,
                    'expert_id' => $data['expert_id'],
                    'date' => $data['date'],
                    'observation' => $data['observation'] ?? null,
                ]);
            }
        }

        $this->syncAssignmentRepairers($assignment, $request);

        try {
            dispatch(new SendOpenedAssignmentNotificationJob($assignment));
        } catch (\Exception $e) {
            Log::error($e);
        }
            
        return $this->responseCreated('Opération effectuée avec succès', new AssignmentResource($assignment));
    }

    /**
     * Ajouter un dossier par un expert
     *
     * @authenticated
     */
    public function openByExpert(CreateAssignmentRequest $request): JsonResponse
    {
        $now = Carbon::now();
        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;

        $year = substr($annee, -2);
        $month = date("m");
        $suffix = $month.'-'.$year.'-BCA';
        $reference = 'D'.$today;

        if($request->claim_number && Assignment::where('claim_number', $request->claim_number)->where(function($query){
            $query->where('status_id', Status::where('code', StatusEnum::OPENED)->first()->id)
                ->orWhere('status_id', Status::where('code', StatusEnum::REALIZED)->first()->id)
                ->orWhere('status_id', Status::where('code', StatusEnum::EDITED)->first()->id)
                ->orWhere('status_id', Status::where('code', StatusEnum::VALIDATED)->first()->id);
        })->count() > 0){
            return $this->responseUnprocessable('Le numéro de sinistre existe déjà pour un dossier ouvert, réalisé, rédigé ou validé');
        }

        $insurer = Entity::find($request->insurer_id);

        $last_assignment = Assignment::where('reference_updated_at', 'like', Carbon::now()->format('Y-m').'%')->where('assignment_type_id', $request->assignment_type_id)->where('status_id','!=', Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id);
        if(!$last_assignment){
            $last_assignment = Assignment::where('created_at', 'like', Carbon::now()->format('Y-m').'%')->where('assignment_type_id', $request->assignment_type_id)->where('status_id','!=', Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id);
        }
        
        if(AssignmentTypeEnum::INSURER->value && $request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()->id){
            $latest_assignment = Assignment::join('entities', 'assignments.insurer_id', '=', 'entities.id')
                                ->where('reference_updated_at', 'like', Carbon::now()->format('Y-m').'%')->where('assignment_type_id', $request->assignment_type_id)->where('status_id','!=', Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id)->where(function($query) use ($request, $insurer){
                                    $query->where(['insurer_id' => $insurer->id])->orWhere(['broker_id' => $request->insurer_id]);
                                })
                                ->latest('reference_updated_at')->first();

            if($latest_assignment && $insurer->entity_type_id == EntityType::where('code', EntityTypeEnum::BROKER)->first()->id){
                $last_assignment = $latest_assignment;
            } else {
                $last_assignment = $last_assignment->where(['broker_id' => null])->where(function($query) use ($request, $insurer){
                                        $query->where(['insurer_id' => $insurer->id])->orWhere(['broker_id' => $request->insurer_id]);
                                    })->latest('reference_updated_at')->first();
            }
        } else {
            $last_assignment = $last_assignment->latest('reference_updated_at')->first();
        }

        if($last_assignment){
            $prefix = explode("-", $last_assignment->reference)[0];
            $number = preg_match_all('/\d+/', $prefix, $matches);
            $number = $matches[0][0];
            $number = $number + 1;
            $prefix = preg_split('/\d+/', $prefix);
            if(mb_strlen($prefix[0]) == 1){
                $prefix = $prefix[0][0];
            } else {
                $prefix = $prefix[0];
            }
            $length = mb_strlen($number);
            if($length == 1){
                $reference = $prefix.'0000'.$number.'-'.$suffix;
            } else if($length == 2){
                $reference = $prefix.'000'.$number.'-'.$suffix;
            } else if($length == 3){
                $reference = $prefix.'00'.$number.'-'.$suffix;
            } else if($length == 4){
                $reference = $prefix.'0'.$number.'-'.$suffix;
            } else {
                $reference = $prefix.$number.'-'.$suffix;
            }
            if(Assignment::where('reference', $reference)->exists()){
                $assignment = Assignment::where('reference', $reference)->first();
                
                $prefix = explode("-", $assignment->reference)[0];
                $number = preg_match_all('/\d+/', $prefix, $matches);
                $number = $matches[0][0];
                $number = $number + 1;
                $prefix = preg_split('/\d+/', $prefix);
                if(mb_strlen($prefix[0]) == 1){
                    $prefix = $prefix[0][0];
                } else {
                    $prefix = $prefix[0];
                }
                $length = mb_strlen($number);
                if($length == 1){
                    $reference = $prefix.'0000'.$number.'-'.$suffix;
                } else if($length == 2){
                    $reference = $prefix.'000'.$number.'-'.$suffix;
                } else if($length == 3){
                    $reference = $prefix.'00'.$number.'-'.$suffix;
                } else if($length == 4){
                    $reference = $prefix.'0'.$number.'-'.$suffix;
                } else {
                    $reference = $prefix.$number.'-'.$suffix;
                }
            }
        } else {
            if(AssignmentTypeEnum::INSURER->value && $request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()->id){
                if(EntityCodeEnum::NSIA->value &&$request->insurer_id == Entity::where('code', EntityCodeEnum::NSIA)->first()->id){
                    $reference = 'G00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::AXA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::AXA)->first()->id){
                    $reference = 'B00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SANLAM_ALLIANZ->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SANLAM_ALLIANZ)->first()->id){
                    $reference = 'K00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::GNA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::GNA)->first()->id){
                    $reference = 'D00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::ATLANTIQUE->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::ATLANTIQUE)->first()->id){
                    $reference = 'E00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::COMAR->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::COMAR)->first()->id){
                    $reference = 'C00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::AFG->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::AFG)->first()->id){
                    $reference = 'AF00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::ATLANTA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::ATLANTA)->first()->id){
                    $reference = 'A00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::ACTIVA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::ACTIVA)->first()->id){
                    $reference = 'L00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SIDAM->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SIDAM)->first()->id){
                    $reference = 'F00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SUNU->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SUNU)->first()->id){
                    $reference = 'S00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SCHIBA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SCHIBA)->first()->id){
                    $reference = 'H00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SONAM->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SONAM)->first()->id){
                    $reference = 'H00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::LEADWAY->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::LEADWAY)->first()->id){
                    $reference = 'V00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::AMSA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::AMSA)->first()->id){
                    $reference = 'P00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::ACTIVA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::ACTIVA)->first()->id){
                    $reference = 'L00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SERENITY->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SERENITY)->first()->id){
                    $reference = 'M00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::COMAR->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::COMAR)->first()->id){
                    $reference = 'C00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SAAR->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SAAR)->first()->id){
                    $reference = 'I00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::WAFA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::WAFA)->first()->id){
                    $reference = 'W00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::CORIS->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::CORIS)->first()->id){
                    $reference = 'Y00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::BNICB->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::BNICB)->first()->id){
                    $reference = 'Z00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SMABTP->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SMABTP)->first()->id){
                    $reference = 'SM00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::LOYALE->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::LOYALE)->first()->id){
                    $reference = 'LL00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::AACI->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::AACI)->first()->id){
                    $reference = 'X00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SONAR->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::SONAR)->first()->id){
                    $reference = 'SO00001'.'-'.$suffix;
                }

                if(EntityCodeEnum::ASCOMA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::ASCOMA)->first()->id){
                    $reference = 'J00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::OLEA->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::OLEA)->first()->id){
                    $reference = 'Q00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::WTW->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::WTW)->first()->id){
                    $reference = 'N00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::FILHEL->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::FILHEL)->first()->id){
                    $reference = 'O00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::UNAP->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::UNAP)->first()->id){
                    $reference = 'U00001'.'-'.$suffix;
                }
                if($request->insurer_id == Entity::where('code', EntityCodeEnum::BAROLD)->first()->id){
                    $reference = 'BA00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::OREOLE->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::OREOLE)->first()->id){
                    $reference = 'OR00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::WALLYX->value && $request->insurer_id == Entity::where('code', EntityCodeEnum::WALLYX)->first()->id){
                    $reference = 'WA00001'.'-'.$suffix;
                }
            }

            if(AssignmentTypeEnum::PARTICULAR && $request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::PARTICULAR)->first()->id){
                $reference = 'PC00001'.'-'.$suffix;
            }
    
            if(AssignmentTypeEnum::TAXI->value && $request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::TAXI)->first()->id){
                $reference = 'TA00001'.'-'.$suffix;
            }
    
            if(AssignmentTypeEnum::EVALUATION->value && $request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()->id){
                $reference = 'EV00001'.'-'.$suffix;
            }

            if(AssignmentTypeEnum::AGAINST_EXPERTISE->value && $request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::AGAINST_EXPERTISE)->first()->id){
                $reference = 'CE00001'.'-'.$suffix;
            }
        }

        $vehicle = Vehicle::with('vehicleGenre', 'vehicleEnergy')->find($request->vehicle_id);

        $assignment = Assignment::create([
            'reference' => $reference,
            'vehicle_id' => $request->vehicle_id ?? null,
            'insurer_id' => $request->insurer_id ?? null,
            // 'broker_id' => $request->broker_id ?? null,
            'additional_insurer_id' => $request->additional_insurer_id ?? null,
            'client_id' => $request->client_id ?? null,
            'assignment_type_id' => $request->assignment_type_id,
            'expertise_type_id' => $request->expertise_type_id,
            'document_transmitted_id' => json_encode($request->document_transmitted_id),
            'assured_value' => $request->assured_value,
            'policy_number' => $request->policy_number,
            'claim_number' => $request->claim_number,
            'weproov_reference' => $request->weproov_reference,
            'claim_starts_at' => $request->claim_starts_at,
            'claim_ends_at' => $request->claim_ends_at,
            'expertise_date' => $request->expertise_date,
            'seen_before_work_date' => $request->expertise_date,
            'expertise_place' => $request->expertise_place,
            'circumstance' => $request->circumstance,
            'damage_declared' => $request->damage_declared,
            'point_noted' => $request->point_noted,
            'received_at' => $request->received_at,
            'vehicle_mileage' => $request->vehicle_mileage,
            'status_id' => Status::where('code', StatusEnum::OPENED)->first()->id,
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
            'reference_updated_by' => auth()->user()->id,
            'reference_updated_at' => Carbon::now(),
        ]);

        if($assignment && $request->get('experts')){
            $experts = $request->get('experts');

            foreach ($experts as $data) {
                $expert = AssignmentExpert::create([
                    'assignment_id' => $assignment->id,
                    'expert_id' => $data['expert_id'],
                    'date' => $data['date'],
                    'observation' => $data['observation'] ?? null,
                ]);
            }
        }

        $this->syncAssignmentRepairers($assignment, $request);

        try {
            dispatch(new GenerateExpertiseSheetPdfJob($assignment));
        } catch (\Exception $e) {
            Log::error($e);
        }
            
        return $this->responseCreated('Opération effectuée avec succès', new AssignmentResource($assignment));
    }


    /**
     * Ouvrir un dossier
     *
     * @authenticated
     */
    public function open($id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        if($assignment->status_id != Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id){
            return $this->responseUnprocessable('Le dossier est déjà ouvert');
        }

        $now = Carbon::now();
        $annee = date("Y");
        $mois_jour_heure = date("mdH");
        $time = date("is");
        $today = $annee.'_'.$mois_jour_heure.'_'.$time;

        $year = substr($annee, -2);
        $month = date("m");
        $suffix = $month.'-'.$year.'-BCA';

        $reference = $assignment->reference;

        $insurer = Entity::find($assignment->insurer_id);

        $last_assignment = Assignment::where('reference_updated_at', 'like', Carbon::now()->format('Y-m').'%')
                                ->where('assignment_type_id', $assignment->assignment_type_id)
                                ->where('assignments.status_id', '!=', Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id)
                                ->where('assignments.id', '!=', $assignment->id);
        
        if(!$last_assignment){
            $last_assignment = Assignment::where('created_at', 'like', Carbon::now()->format('Y-m').'%')
                                ->where('assignment_type_id', $assignment->assignment_type_id)
                                ->where('assignments.status_id', '!=', Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id)
                                ->where('assignments.id', '!=', $assignment->id);
        }
        
        if(AssignmentTypeEnum::INSURER->value && $assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()->id){
            $latest_assignment = Assignment::join('entities', 'assignments.insurer_id', '=', 'entities.id')
                                ->where('reference_updated_at', 'like', Carbon::now()->format('Y-m').'%')->where('assignment_type_id', $assignment->assignment_type_id)->where(function($query) use ($assignment, $insurer){
                                    $query->where(['insurer_id' => $insurer->id])->orWhere(['broker_id' => $assignment->insurer_id]);
                                })
                                ->where('assignments.status_id', '!=', Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id)
                                ->where('assignments.id', '!=', $assignment->id)
                                ->latest('reference_updated_at')->first();


            if($latest_assignment && $insurer->entity_type_id == EntityType::where('code', EntityTypeEnum::BROKER)->first()->id){
                $last_assignment = $latest_assignment;
            } else {
                $last_assignment = $last_assignment->where(['broker_id' => null])->where(function($query) use ($assignment, $insurer){
                                        $query->where(['insurer_id' => $insurer->id])->orWhere(['broker_id' => $insurer->id]);
                                    })->latest('reference_updated_at')->first();

            }

        } else {
            $last_assignment = $last_assignment->latest('reference_updated_at')->first();
        }



        if($last_assignment){
            $prefix = explode("-", $last_assignment->reference)[0];
            $number = preg_match_all('/\d+/', $prefix, $matches);
            $number = $matches[0][0];
            $number = $number + 1;
            $prefix = preg_split('/\d+/', $prefix);
            if(mb_strlen($prefix[0]) == 1){
                $prefix = $prefix[0][0];
            } else {
                $prefix = $prefix[0];
            }
            $length = mb_strlen($number);
            if($length == 1){
                $reference = $prefix.'0000'.$number.'-'.$suffix;
            } else if($length == 2){
                $reference = $prefix.'000'.$number.'-'.$suffix;
            } else if($length == 3){
                $reference = $prefix.'00'.$number.'-'.$suffix;
            } else if($length == 4){
                $reference = $prefix.'0'.$number.'-'.$suffix;
            } else {
                $reference = $prefix.$number.'-'.$suffix;
            }
            if(Assignment::where('reference', $reference)->exists()){
                $assignment = Assignment::where('reference', $reference)->first();
                
                $prefix = explode("-", $assignment->reference)[0];
                $number = preg_match_all('/\d+/', $prefix, $matches);
                $number = $matches[0][0];
                $number = $number + 1;
                $prefix = preg_split('/\d+/', $prefix);
                if(mb_strlen($prefix[0]) == 1){
                    $prefix = $prefix[0][0];
                } else {
                    $prefix = $prefix[0];
                }
                $length = mb_strlen($number);
                if($length == 1){
                    $reference = $prefix.'0000'.$number.'-'.$suffix;
                } else if($length == 2){
                    $reference = $prefix.'000'.$number.'-'.$suffix;
                } else if($length == 3){
                    $reference = $prefix.'00'.$number.'-'.$suffix;
                } else if($length == 4){
                    $reference = $prefix.'0'.$number.'-'.$suffix;
                } else {
                    $reference = $prefix.$number.'-'.$suffix;
                }
            }
        } else {
            if(AssignmentTypeEnum::INSURER->value && $assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()?->id){
                if(EntityCodeEnum::NSIA->value &&$assignment->insurer_id == Entity::where('code', EntityCodeEnum::NSIA)->first()?->id){
                    $reference = 'G00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::AXA->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::AXA)->first()?->id){
                    $reference = 'B00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SANLAM_ALLIANZ->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::SANLAM_ALLIANZ)->first()?->id){
                    $reference = 'K00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::GNA->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::GNA)->first()?->id){
                    $reference = 'D00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::ATLANTIQUE->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::ATLANTIQUE)->first()?->id){
                    $reference = 'E00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::COMAR->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::COMAR)->first()?->id){
                    $reference = 'C00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::AFG->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::AFG)->first()?->id){
                    $reference = 'AF00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::ATLANTA->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::ATLANTA)->first()?->id){
                    $reference = 'A00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::ACTIVA->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::ACTIVA)->first()?->id){
                    $reference = 'L00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SIDAM->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::SIDAM)->first()?->id){
                    $reference = 'F00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SUNU->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::SUNU)->first()?->id){
                    $reference = 'S00001'.'-'.$suffix;
                }
                // if(EntityCodeEnum::MATCA->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::MATCA)->first()?->id){
                //     $reference = 'M00001'.'-'.$suffix;
                // }
                if(EntityCodeEnum::SCHIBA->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::SCHIBA)->first()?->id){
                    $reference = 'H00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SONAM->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::SONAM)->first()?->id){
                    $reference = 'H00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::LEADWAY->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::LEADWAY)->first()?->id){
                    $reference = 'V00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::AMSA->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::AMSA)->first()?->id){
                    $reference = 'P00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::ACTIVA->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::ACTIVA)->first()?->id){
                    $reference = 'L00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SERENITY->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::SERENITY)->first()?->id){
                    $reference = 'M00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::COMAR->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::COMAR)->first()?->id){
                    $reference = 'C00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SAAR->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::SAAR)->first()?->id){
                    $reference = 'I00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::WAFA->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::WAFA)->first()?->id){
                    $reference = 'W00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::CORIS->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::CORIS)->first()?->id){
                    $reference = 'Y00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::BNICB->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::BNICB)->first()?->id){
                    $reference = 'Z00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SMABTP->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::SMABTP)->first()?->id){
                    $reference = 'SM00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::LOYALE->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::LOYALE)->first()?->id){
                    $reference = 'LL00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::AACI->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::AACI)->first()?->id){
                    $reference = 'X00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::SONAR->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::SONAR)->first()?->id){
                    $reference = 'SO00001'.'-'.$suffix;
                }

                if(EntityCodeEnum::ASCOMA->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::ASCOMA)->first()?->id){
                    $reference = 'J00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::OLEA->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::OLEA)->first()?->id){
                    $reference = 'Q00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::WTW->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::WTW)->first()?->id){
                    $reference = 'N00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::FILHEL->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::FILHEL)->first()?->id){
                    $reference = 'O00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::UNAP->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::UNAP)->first()?->id){
                    $reference = 'U00001'.'-'.$suffix;
                }
                if($assignment->insurer_id == Entity::where('code', EntityCodeEnum::BAROLD)->first()?->id){
                    $reference = 'BA00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::OREOLE->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::OREOLE)->first()?->id){
                    $reference = 'OR00001'.'-'.$suffix;
                }
                if(EntityCodeEnum::WALLYX->value && $assignment->insurer_id == Entity::where('code', EntityCodeEnum::WALLYX)->first()?->id){
                    $reference = 'WA00001'.'-'.$suffix;
                }

                // if(EntityCodeEnum::ASCOMA->value && $assignment->broker_id == Entity::where('code', EntityCodeEnum::ASCOMA)->first()?->id){
                //     $reference = 'J00001'.'-'.$suffix;
                // }
                // if(EntityCodeEnum::OLEA->value && $assignment->broker_id == Entity::where('code', EntityCodeEnum::OLEA)->first()?->id){
                //     $reference = 'Q00001'.'-'.$suffix;
                // }
                // if(EntityCodeEnum::WTW->value && $assignment->broker_id == Entity::where('code', EntityCodeEnum::WTW)->first()?->id){
                //     $reference = 'N00001'.'-'.$suffix;
                // }
                // if(EntityCodeEnum::FILHEL->value && $assignment->broker_id == Entity::where('code', EntityCodeEnum::FILHEL)->first()?->id){
                //     $reference = 'O00001'.'-'.$suffix;
                // }
                // if(EntityCodeEnum::UNAP->value && $assignment->broker_id == Entity::where('code', EntityCodeEnum::UNAP)->first()?->id){
                //     $reference = 'U00001'.'-'.$suffix;
                // }
            }

            if(AssignmentTypeEnum::PARTICULAR && $assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::PARTICULAR)->first()?->id){
                $reference = 'PC00001'.'-'.$suffix;
            }
    
            if(AssignmentTypeEnum::TAXI->value && $assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::TAXI)->first()?->id){
                $reference = 'TA00001'.'-'.$suffix;
            }
    
            if(AssignmentTypeEnum::EVALUATION->value && $assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()?->id){
                $reference = 'EV00001'.'-'.$suffix;
            }

            if(AssignmentTypeEnum::AGAINST_EXPERTISE->value && $assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::AGAINST_EXPERTISE)->first()?->id){
                $reference = 'CE00001'.'-'.$suffix;
            }
        }

        $assignment->update([
            'reference' => $reference,
            'opened_by' => auth()->user()->id,
            'opened_at' => Carbon::now(),
            'status_id' => Status::where('code', StatusEnum::OPENED)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        try {
            dispatch(new GenerateExpertiseSheetPdfJob($assignment));
        } catch (\Exception $e) {
            Log::error($e);
        }

        return $this->responseSuccess('Dossier ouvert avec succès', new AssignmentResource($assignment));
    }

    /**
     * Réaliser un dossier
     *
     * @authenticated
     */
    public function realize(RealizeAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        if($assignment->status_id != Status::where('code', StatusEnum::OPENED)->first()->id){
            return $this->responseUnprocessable("Le dossier n'est pas encore ouvert, veuillez l'ouvrir avant de le réaliser.", null);
        }

        $assignment->update([
            'vehicle_mileage' => $request->mileage,
            'expertise_date' => $request->expertise_date,
            'expertise_place' => $request->expertise_place,
            'point_noted' => $request->point_noted,
            'directed_by' => $request->directed_by,
            'status_id' => Status::where('code', StatusEnum::REALIZED)->first()->id,
            'realized_by' => auth()->user()->id,
            'realized_at' => Carbon::now(),
            'updated_by' => auth()->user()->id,
        ]);

        $this->syncAssignmentRepairers($assignment, $request);

        if($assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()?->id || $assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::AGAINST_EXPERTISE)->first()?->id){
            $assignment->update([
                'status_id' => Status::where('code', StatusEnum::IN_EDITING)->first()->id,
                'work_sheet_established_by' => auth()->user()->id,
                'work_sheet_established_at' => Carbon::now(),
                'updated_by' => auth()->user()->id,
            ]);
        }

        if($request->mileage){
            $vehicle = Vehicle::where('id',$assignment->vehicle_id)->first();
            $vehicle->mileage = $request->mileage;
            $vehicle->save();
        }

        dispatch(new GenerateExpertiseSheetPdfJob($assignment));

        return $this->responseCreated('Opération effectuée avec succès', new AssignmentResource($assignment));
    }

    /**
     * Rédiger un dossier
     *
     * @authenticated
     */
    public function edit(EditAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        if($assignment->status_id != Status::where('code', StatusEnum::REALIZED)->first()->id){
            return $this->responseUnprocessable("Le dossier n'est pas encore réalisé ou est déjà rédigé, veuillez le réaliser avant de le rédiger.", null);
        }

        // if(!$assignment->work_sheet_established_at){
        //     return $this->responseUnprocessable("La fiche d'expertise n'est pas encore établie, veuillez l'établir.", null);
        // }

        $vehicle = Vehicle::with('vehicleGenre', 'vehicleEnergy')->findOrFail($assignment->vehicle_id);

        if($request->new_market_value){
            $vehicle->update([
                'new_market_value' => $request->new_market_value,
            ]);
        }

        if($vehicle->vehicleGenre && $vehicle->vehicleEnergy){
            $marketValueService = app(MarketValueService::class);
            $result = json_decode(json_encode($marketValueService->calculateTheoreticalMarketValue($vehicle->vehicleGenre->id, $vehicle->vehicleEnergy->id, $vehicle->new_market_value, $vehicle->mileage, $vehicle->first_entry_into_circulation_date, $assignment->expertise_date)));
            
            $kilometric_incidence = 0;
            $is_up = null;
            if ($vehicle->vehicleEnergy->code == 'VE01') {
                $max_mileage_essence_per_month = $vehicle->vehicleGenre->max_mileage_essence_per_year / 12;
                $kilometric_incidence = (($max_mileage_essence_per_month * $result->month_diff) - $vehicle->mileage) * 25;
            } else {
                $max_mileage_diesel_per_month = $vehicle->vehicleGenre->max_mileage_diesel_per_year / 12;
                $kilometric_incidence = (($max_mileage_diesel_per_month * $result->month_diff) -$vehicle->mileage) * 40;
            }

            if ($kilometric_incidence > 0) {
                $is_up = true;
            } else {
                $is_up = false;
                $kilometric_incidence = -1 * $kilometric_incidence;
            }

            $expertise_date = $result->expertise_date;
            $first_entry_into_circulation_date = $result->first_entry_into_circulation_date;
            $vehicle_age = $result->vehicle_age;
            $theorical_depreciation_rate = $result->theorical_depreciation_rate;
            $theorical_vehicle_market_value = $result->theorical_vehicle_market_value;
        }

        $ascertainments = $request->get('ascertainments');

        if(count($ascertainments) > 0){
            foreach ($ascertainments as $data) {
                $ascertainment = Ascertainment::create([
                    'assignment_id' => $assignment->id,
                    'ascertainment_type_id' => $data['ascertainment_type_id'],
                    'very_good' => $data['very_good'],
                    'good' => $data['good'],
                    'acceptable' => $data['acceptable'],
                    'less_good' => $data['less_good'],
                    'bad' => $data['bad'],
                    'very_bad' => $data['very_bad'],
                    'comment' => $data['comment'],
                    'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]);
            }
        }

        $shocks = $request->get('shocks');

        if(count($shocks) > 0){
            Shock::query()->where('assignment_id', $assignment->id)->update([
                'status_id' => Status::where('code', StatusEnum::DELETED)->first()->id,
                'deleted_by' => auth()->user()->id,
                'deleted_at' => Carbon::now(),
            ]);
            Shock::query()->where('assignment_id', $assignment->id)->delete();
            foreach ($shocks as $data) {
                $exist_shock = Shock::where('assignment_id', $assignment->id)->where('shock_point_id', $data['shock_point_id'])->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->first();
                if($exist_shock){
                    $shock = $exist_shock;
                } else {
                    $shock = Shock::create([
                        'assignment_id' => $assignment->id,
                        'shock_point_id' => $data['shock_point_id'],
                        'paint_type_id' => $data['paint_type_id'],
                        'hourly_rate_id' => $data['hourly_rate_id'],
                        'with_tax' => $data['with_tax'],
                        'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                        'created_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id,
                    ]);
                }

                if(count($data['shock_works']) > 0){
                    ShockWork::query()->where('shock_id', $shock->id)->update([
                        'status_id' => Status::where('code', StatusEnum::DELETED)->first()->id,
                        'deleted_by' => auth()->user()->id,
                        'deleted_at' => Carbon::now(),
                    ]);
                    ShockWork::query()->where('shock_id', $shock->id)->delete();
                    $shock_work_position = 1;
                    foreach ($data['shock_works'] as $item) {

                        $discount = $item['discount'];
                        $discount_amount_excluding_tax = ceil(($item['discount'] * $item['amount']) / 100);
                        $discount_amount_tax = ceil((config('services.settings.tax_rate') * $discount_amount_excluding_tax) / 100);
                        $discount_amount = ceil($discount_amount_excluding_tax + $discount_amount_tax);

                        $obsolescence_rate = $item['obsolescence_rate'];
                        $obsolescence_amount_excluding_tax = ceil(($item['obsolescence_rate'] * ($item['amount'] - $discount_amount_excluding_tax)) / 100);
                        $obsolescence_amount_tax = ceil((config('services.settings.tax_rate') * $obsolescence_amount_excluding_tax) / 100);
                        $obsolescence_amount = ceil($obsolescence_amount_excluding_tax + $obsolescence_amount_tax);
        
                        $recovery_amount_excluding_tax = $item['recovery_amount'];
                        $recovery_amount_tax = 0;
                        $recovery_amount = ceil($recovery_amount_excluding_tax + $recovery_amount_tax);

                        $new_amount_excluding_tax = $item['amount'] - ($obsolescence_amount_excluding_tax + $discount_amount_excluding_tax);
                        if($assignment->expertise_type_id == ExpertiseType::where('code', ExpertiseTypeEnum::EVALUATION)->first()->id || $assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()->id){
                            $new_amount_tax = 0;
                        } else {
                            $new_amount_tax = ceil((config('services.settings.tax_rate') * $new_amount_excluding_tax) / 100);
                        }
                        $new_amount = ceil($new_amount_excluding_tax + $new_amount_tax);

                        $exist_shockWork = ShockWork::where('shock_id', $shock->id)->where('supply_id', $item['supply_id'])->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->first();
                        if($exist_shockWork){
                            $shockWork = $exist_shockWork;
                            $shockWork->update([
                                'supply_id' => $item['supply_id'] ?? null,
                                'disassembly' => $item['disassembly'] ?? false,
                                'replacement' => $item['replacement'] ?? false,
                                'repair' => $item['repair'] ?? false,
                                'paint' => $item['paint'] ?? false,
                                'obsolescence' => $item['obsolescence'] ?? false,
                                'control' => $item['control'] ?? false,
                                'in_order' => $item['in_order'] ?? false,
                                'comment' => $item['comment'],
                                'amount' =>  $item['amount'],
                                'position' =>  $shock_work_position,
                                'obsolescence_rate' => $obsolescence_rate,
                                'obsolescence_amount_excluding_tax' => $obsolescence_amount_excluding_tax,
                                'obsolescence_amount_tax' => $obsolescence_amount_tax,
                                'obsolescence_amount' => $obsolescence_amount,
                                'recovery_amount_excluding_tax' => $recovery_amount_excluding_tax,
                                'recovery_amount_tax' => $recovery_amount_tax,
                                'recovery_amount' => $recovery_amount,
                                'discount' => $discount,
                                'discount_amount_excluding_tax' => $discount_amount_excluding_tax,
                                'discount_amount_tax' => $discount_amount_tax,
                                'discount_amount' => $discount_amount,
                                'new_amount_excluding_tax' => $new_amount_excluding_tax,
                                'new_amount_tax' => $new_amount_tax,
                                'new_amount' => $new_amount,
                                'updated_by' => auth()->user()->id,
                            ]);
                        } else {
                            $shockWork = ShockWork::create([
                                'shock_id' => $shock->id,
                                'supply_id' => $item['supply_id'] ?? null,
                                'disassembly' => $item['disassembly'] ?? false,
                                'replacement' => $item['replacement'] ?? false,
                                'repair' => $item['repair'] ?? false,
                                'paint' => $item['paint'] ?? false,
                                'obsolescence' => $item['obsolescence'] ?? false,
                                'control' => $item['control'] ?? false,
                                'in_order' => $item['in_order'] ?? false,
                                'comment' => $item['comment'],
                                'amount' =>  $item['amount'],
                                'position' =>  $shock_work_position,
                                'obsolescence_rate' => $obsolescence_rate,
                                'obsolescence_amount_excluding_tax' => $obsolescence_amount_excluding_tax,
                                'obsolescence_amount_tax' => $obsolescence_amount_tax,
                                'obsolescence_amount' => $obsolescence_amount,
                                'recovery_amount_excluding_tax' => $recovery_amount_excluding_tax,
                                'recovery_amount_tax' => $recovery_amount_tax,
                                'recovery_amount' => $recovery_amount,
                                'discount' => $discount,
                                'discount_amount_excluding_tax' => $discount_amount_excluding_tax,
                                'discount_amount_tax' => $discount_amount_tax,
                                'discount_amount' => $discount_amount,
                                'new_amount_excluding_tax' => $new_amount_excluding_tax,
                                'new_amount_tax' => $new_amount_tax,
                                'new_amount' => $new_amount,
                                'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                                'created_by' => auth()->user()->id,
                                'updated_by' => auth()->user()->id,
                            ]);
                        }
                        $shock_work_position++;
                    }
        
                }

                $nb_paint = ShockWork::where(['shock_id' => $shock->id, 'paint' => 1])->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('paint');

                $total_nb_hours = 0;
                $all_paint = false;
    
                if(count($data['workforces']) > 0){
                    $workforce_position = 1;
                    foreach ($data['workforces'] as $item) {
                        if($item['workforce_type_id'] == WorkforceType::where('code', WorkforceTypeEnum::PAINT)->first()->id){
                            if($nb_paint == 0){
                                $total = 0;
                            } else {
                                if($nb_paint == 1){
                                    $painting_price = PaintingPrice::where(['hourly_rate_id' => HourlyRate::where('id', $data['hourly_rate_id'])->first()->id, 'paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::ONE)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first();
                                } elseif($nb_paint == 2){
                                    $painting_price = PaintingPrice::where(['hourly_rate_id' => HourlyRate::where('id', $data['hourly_rate_id'])->first()->id, 'paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::TWO)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first();
                                } else {
                                    $painting_price = PaintingPrice::where(['hourly_rate_id' => HourlyRate::where('id', $data['hourly_rate_id'])->first()->id, 'paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::THREE)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first();
                                } 
                                
                                if ($item['all_paint'] == true) {
                                    $all_paint = true;
                                    $painting_price = PaintingPrice::where(['hourly_rate_id' => HourlyRate::where('id', $data['hourly_rate_id'])->first()->id, 'paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::ALL)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first();
                                }

                                $total = $painting_price ? $item['nb_hours'] * $painting_price->param_1 + $painting_price->param_2 : 0;
                            }
                        } else {
                            $total = $item['nb_hours'] * HourlyRate::where(['id' => $data['hourly_rate_id'], 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first()->value;
                        }

                        $amount_excluding_tax = ceil($total - ($total * $item['discount'] / 100));
                        if($data['with_tax'] == false){
                            $amount_tax = 0;
                        } else {
                            $amount_tax = ceil((config('services.settings.tax_rate') * $amount_excluding_tax) / 100);
                        }
                        $amount = ceil($amount_excluding_tax + $amount_tax);
            
                        $workforce = Workforce::create([
                            'shock_id' => $shock->id,
                            'workforce_type_id' => $item['workforce_type_id'],
                            'nb_hours' => $item['nb_hours'],
                            'work_fee' => ceil(HourlyRate::where(['id' => $data['hourly_rate_id'], 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first()->value),
                            'with_tax' => $data['with_tax'],
                            'discount' => $item['discount'],
                            'all_paint' => $item['all_paint'],
                            'amount_excluding_tax' => $amount_excluding_tax,
                            'amount_tax' => $amount_tax,
                            'amount' => $amount,
                            'position' =>  $workforce_position,
                            'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                            'created_by' => auth()->user()->id,
                            'updated_by' => auth()->user()->id,
                        ]);

                        $total_nb_hours += $item['nb_hours'];
                        $workforce_position++;
                    }
                }

                $total_in_order_amount_excluding_tax = ShockWork::where('shock_id', $shock->id)->where('in_order', true)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('new_amount_excluding_tax');
                $total_in_order_amount_tax = ShockWork::where('shock_id', $shock->id)->where('in_order', true)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('new_amount_tax');
                $total_in_order_amount = ShockWork::where('shock_id', $shock->id)->where('in_order', true)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('new_amount');
    
                $total_obsolescence_amount_excluding_tax = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('obsolescence_amount_excluding_tax');
                $total_obsolescence_amount_tax = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('obsolescence_amount_tax');
                $total_obsolescence_amount = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('obsolescence_amount');
    
                $total_recovery_amount_excluding_tax = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('recovery_amount_excluding_tax');
                $total_recovery_amount_tax = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('recovery_amount_tax');
                $total_recovery_amount = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('recovery_amount');
    
                $total_discount_amount_excluding_tax = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('discount_amount_excluding_tax');
                $total_discount_amount_tax = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('discount_amount_tax');
                $total_discount_amount = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('discount_amount');
    
                $total_new_amount_excluding_tax = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('new_amount_excluding_tax');
                $total_new_amount_tax = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('new_amount_tax');
                $total_new_amount = ShockWork::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('new_amount');
    
                $total_workforce_amount_excluding_tax = Workforce::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount_excluding_tax');
                $total_workforce_amount_tax = Workforce::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount_tax');
                $total_workforce_amount = Workforce::where('shock_id', $shock->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount');
    
                if($nb_paint == 0){
                    $paint_product_price = null;
                } else {
                    if($nb_paint == 1){
                        $paint_product_price = PaintProductPrice::where(['paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::ONE)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first()->value ?? 0;
                    } elseif($nb_paint == 2){
                        $paint_product_price = PaintProductPrice::where(['paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::TWO)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first()->value ?? 0;
                    } else {
                        $paint_product_price = PaintProductPrice::where(['paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::THREE)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first()->value ?? 0;
                    } 
                    
                    if ($all_paint) {
                        $paint_product_price = PaintProductPrice::where(['paint_type_id' => $data['paint_type_id'], 'number_paint_element_id' => NumberPaintElement::where('value', NumberPaintElementEnum::ALL)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->first()->value  ?? 0;
                    }
                }
    
                $total_paint_product_amount_excluding_tax = $paint_product_price ? $paint_product_price * Workforce::where(['shock_id' => $shock->id, 'workforce_type_id' => WorkforceType::where('code', WorkforceTypeEnum::PAINT)->first()->id, 'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id])->sum('nb_hours') : 0;
                $total_paint_product_amount_tax = (config('services.settings.tax_rate') * $total_paint_product_amount_excluding_tax) / 100;
                $total_paint_product_amount = $total_paint_product_amount_excluding_tax + $total_paint_product_amount_tax;
    
                $total_small_supply_amount_excluding_tax = ($total_new_amount_excluding_tax + $total_workforce_amount_excluding_tax + $total_paint_product_amount_excluding_tax + $total_recovery_amount_excluding_tax) * (config('services.settings.small_supply_rate') / 100);
                $total_small_supply_amount_tax = (config('services.settings.tax_rate') * $total_small_supply_amount_excluding_tax) / 100;
                $total_small_supply_amount = $total_small_supply_amount_excluding_tax + $total_small_supply_amount_tax;
    
                $shock->update([
                    'shock_work_in_order_amount_excluding_tax' => ceil($total_in_order_amount_excluding_tax),
                    'shock_work_in_order_amount_tax' => ceil($total_in_order_amount_tax),
                    'shock_work_in_order_amount' => ceil($total_in_order_amount),
                    'shock_work_obsolescence_amount_excluding_tax' => ceil($total_obsolescence_amount_excluding_tax),
                    'shock_work_obsolescence_amount_tax' => ceil($total_obsolescence_amount_tax),
                    'shock_work_obsolescence_amount' => ceil($total_obsolescence_amount),
                    'shock_work_recovery_amount_excluding_tax' => ceil($total_recovery_amount_excluding_tax),
                    'shock_work_recovery_amount_tax' => ceil($total_recovery_amount_tax), 
                    'shock_work_recovery_amount' => ceil($total_recovery_amount),
                    'shock_work_discount_amount_excluding_tax' => ceil($total_discount_amount_excluding_tax),
                    'shock_work_discount_amount_tax' => ceil($total_discount_amount_tax),
                    'shock_work_discount_amount' => ceil($total_discount_amount),
                    'shock_work_new_amount_excluding_tax' => ceil($total_new_amount_excluding_tax),
                    'shock_work_new_amount_tax' => ceil($total_new_amount_tax),
                    'shock_work_new_amount' => ceil($total_new_amount),
                    'workforce_amount_excluding_tax' => ceil($total_workforce_amount_excluding_tax),
                    'workforce_amount_tax' => ceil($total_workforce_amount_tax),
                    'workforce_amount' => ceil($total_workforce_amount),
                    'paint_product_amount_excluding_tax' => ceil($total_paint_product_amount_excluding_tax),
                    'paint_product_amount_tax' => ceil($total_paint_product_amount_tax),
                    'paint_product_amount' => ceil($total_paint_product_amount),
                    'small_supply_amount_excluding_tax' => ceil($total_small_supply_amount_excluding_tax),
                    'small_supply_amount_tax' => ceil($total_small_supply_amount_tax),
                    'small_supply_amount' => ceil($total_small_supply_amount),
                    'amount_excluding_tax' => ceil($total_new_amount_excluding_tax + $total_workforce_amount_excluding_tax + $total_paint_product_amount_excluding_tax + $total_small_supply_amount_excluding_tax + $total_recovery_amount_excluding_tax),
                    'amount_tax' => ceil($total_new_amount_tax + $total_workforce_amount_tax + $total_paint_product_amount_tax + $total_small_supply_amount_tax + $total_recovery_amount_tax),
                    'amount' => ceil($total_new_amount + $total_workforce_amount + $total_paint_product_amount + $total_small_supply_amount + $total_recovery_amount),
                ]);
            }
    
            $total_shock_amount_excluding_tax = ceil(Shock::where('assignment_id', $assignment->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount_excluding_tax'));
            $total_shock_amount_tax = ceil(Shock::where('assignment_id', $assignment->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount_tax'));
            $total_shock_amount = ceil(Shock::where('assignment_id', $assignment->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount'));
        }

        $other_costs = $request->get('other_costs');

        if(count($other_costs) > 0){
            foreach ($other_costs as $data) {
                $other_cost = OtherCost::create([
                    'assignment_id' => $assignment->id,
                    'other_cost_type_id' => $data['other_cost_type_id'],
                    'amount_excluding_tax' => ceil($data['amount']),
                    'amount_tax' => 0,
                    'amount' => ceil($data['amount']),
                    'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]);
            }
        }

        $total_other_cost_amount_excluding_tax = OtherCost::where('assignment_id', $assignment->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount_excluding_tax');
        $total_other_cost_amount_tax = OtherCost::where('assignment_id', $assignment->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount_tax');
        $total_other_cost_amount = OtherCost::where('assignment_id', $assignment->id)->where('status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)->sum('amount');

        $total_amount_excluding_tax = $total_shock_amount_excluding_tax + $total_other_cost_amount_excluding_tax;
        $total_amount_tax = $total_shock_amount_tax + $total_other_cost_amount_tax;
        $total_amount = $total_shock_amount + $total_other_cost_amount;

        if($vehicle->vehicleGenre && $vehicle->vehicleEnergy){
            $less_value_work = $total_amount;
            $special_accessory = $request->special_accessory ?? 0;
            $market_incidence = ceil($result->vehicle_new_value * $request->market_incidence_rate / 100);

            if($kilometric_incidence > ($theorical_vehicle_market_value / 2)){
                $kilometric_incidence = $theorical_vehicle_market_value / 2;
            }

            if($assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()->id){
                $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence - $less_value_work + $special_accessory : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence - $less_value_work + $special_accessory;
            } else {
                $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence;
            }

            $depreciation_rate = $result->vehicle_new_value > 0 ? number_format(100 - ($vehicle_market_value * 100 / $result->vehicle_new_value), 2, ',', '') : 0;
            $depreciation_rate = floatval(str_replace(',', '.', $depreciation_rate));

            if($request->evaluations){
                $evaluations = [
                    'vehicle_age' => $request->evaluations['vehicle_age'],
                    'diff_year' => $request->evaluations['year_diff'],
                    'diff_month' => $request->evaluations['month_diff'],
                    'theorical_depreciation_rate' => $request->evaluations['theorical_depreciation_rate'],
                    'theorical_vehicle_market_value' => $request->evaluations['theorical_vehicle_market_value'],
                    'market_incidence_rate' => $request->evaluations['market_incidence_rate'],
                    'less_value_work' => $request->evaluations['less_value_work'],
                    'special_accessory' => $request->evaluations['special_accessory'] ?? 0,
                    'is_up' => $request->evaluations['is_up'],
                    'kilometric_incidence' => $request->evaluations['kilometric_incidence'],
                    'market_incidence' => $request->evaluations['market_incidence'],
                    'depreciation_rate' => $request->evaluations['depreciation_rate'],
                    'vehicle_market_value' => $request->evaluations['vehicle_market_value'],
                    'vehicle_market_value_rounded' => $request->evaluations['vehicle_market_value_rounded'],
                ];
            } else {
                $evaluations = [
                    'vehicle_age' => $result->vehicle_age,
                    'diff_year' => $result->year_diff,
                    'diff_month' => $result->month_diff,
                    'theorical_depreciation_rate' => $theorical_depreciation_rate,
                    'theorical_vehicle_market_value' => $theorical_vehicle_market_value,
                    'market_incidence_rate' => $request->market_incidence_rate,
                    'less_value_work' => $less_value_work,
                    'special_accessory' => $special_accessory,
                    'is_up' => $is_up,
                    'kilometric_incidence' => $kilometric_incidence,
                    'market_incidence' => $market_incidence,
                    'depreciation_rate' => $depreciation_rate,
                    'vehicle_market_value' => $vehicle_market_value,
                    'vehicle_market_value_rounded' =>  $vehicle_market_value,
                ];
            }
        }

        $work_duration = null;
        $work_duration_service = app(WorkDurationService::class);
        $work_duration = $work_duration_service->calculateWorkDuration($total_nb_hours);

        $assignment->update([
            'general_state_id' => $request->general_state_id,
            'claim_nature_id' => $request->claim_nature_id,
            'technical_conclusion_id' => $request->technical_conclusion_id,
            'seen_before_work_date' => $request->seen_before_work_date,
            'seen_during_work_date' => $request->seen_during_work_date,
            'seen_after_work_date' => $request->seen_after_work_date,
            'contact_date' => $request->contact_date,
            'expertise_place' => $request->expertise_place,
            'assured_value' => $request->assured_value,
            'salvage_value' => $request->salvage_value,
            'vehicle_mileage' => $request->mileage,
            'vehicle_new_market_value_option' => strtolower($request->vehicle_new_market_value_option),
            'new_market_value' => $request->new_market_value,
            'depreciation_rate' => $request->depreciation_rate,
            'market_value' => $request->market_value,
            'report_remark_id' => $request->report_remark_id,
            'expert_report_remark' => $request->expert_report_remark,
            'instructions' => $request->instructions,
            'work_duration' => $work_duration,
            'shock_amount_excluding_tax' => $total_shock_amount_excluding_tax,
            'shock_amount_tax' => $total_shock_amount_tax,
            'shock_amount' => $total_shock_amount,
            'other_cost_amount_excluding_tax' => $total_other_cost_amount_excluding_tax,
            'other_cost_amount_tax' => $total_other_cost_amount_tax,
            'other_cost_amount' => $total_other_cost_amount,
            'total_amount_excluding_tax' => $total_amount_excluding_tax,
            'total_amount_tax' => $total_amount_tax,
            'total_amount' => $total_amount,
            'evaluations' => $vehicle->vehicleGenre && $vehicle->vehicleEnergy ? json_encode($evaluations) : null,
            'status_id' => Status::where('code', StatusEnum::IN_EDITING)->first()->id,
            'updated_by' => auth()->user()->id,
            'edited_by' => auth()->user()->id,
            'edited_at' => Carbon::now(),
        ]);

        try {
            dispatch(new GenerateExpertiseReportPdfJob($assignment));
        } catch (\Exception $e) {
            Log::error($e);
        }
            
        return $this->responseCreated('Opération effectuée avec succès ', new AssignmentResource($assignment));
    }

    /**
     * Créer une fiche de travaux
     *
     * @authenticated
     */
    public function create_work_sheet(Request $request, $id)
    {
        $assignment = Assignment::where('id', $id)->firstOrFail();

        // if($assignment->status_id != Status::where('code', StatusEnum::OPENED)->first()->id && $assignment->status_id != Status::where('code', StatusEnum::REALIZED)->first()->id){
        //     return $this->responseUnprocessable("Impossible de créer la fiche de travaux.", null);
        // }

        // if($assignment->work_sheet_established_at){
        //     return $this->responseUnprocessable("La fiche d'expertise est déjà établie.", null);
        // }

        if($request->claim_number && Assignment::where('claim_number', $request->claim_number)->where(function($query){
            $query->where('status_id', Status::where('code', StatusEnum::OPENED)->first()->id)
                ->orWhere('status_id', Status::where('code', StatusEnum::REALIZED)->first()->id)
                ->orWhere('status_id', Status::where('code', StatusEnum::EDITED)->first()->id)
                ->orWhere('status_id', Status::where('code', StatusEnum::VALIDATED)->first()->id);
        })->count() > 0){
            return $this->responseUnprocessable('Le numéro de sinistre existe déjà pour un dossier ouvert, réalisé, rédigé ou validé');
        }

        $exist_client = Client::where('name', 'like', '%'.$request->name.'%')->first();
        $exist_insurer = Entity::where('entity_type_id', EntityType::where('code', EntityTypeEnum::INSURER)->first()->id)->where('name', 'like', '%'.$request->name.'%')->first();

        if($exist_client){
            $client = $exist_client;
        } else {
            $client = Client::create(
                [
                    'name' => $request->client_name,
                    'phone_1' => $request->client_phone,
                    'email' => $request->client_email,
                    'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]
            );
        }

        if($exist_insurer){
            $insurer = $exist_insurer;
        } else {
            $insurer = Entity::create(
                [
                    'name' => $request->insurer_name,
                    'phone_1' => $request->insurer_phone,
                    'email' => $request->insurer_email,
                    'entity_type_id' => EntityType::where('code', EntityTypeEnum::INSURER)->first()->id,
                    'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]
            );
        }

        $shocks = $request->get('shock_points');

        if(count($shocks) > 0){
            $shockPosition = 1;
            foreach ($shocks as $data) {
                $shock = Shock::create([
                    'assignment_id' => $assignment->id,
                    'shock_point_id' => $data['shock_point_id'],
                    'paint_type_id' => PaintType::where('code', PaintTypeEnum::ORDINARY)->first()->id,
                    'hourly_rate_id' => HourlyRate::where('value', HourlyRateEnum::ONE)->first()->id,
                    'with_tax' => 0,
                    'position' => $shockPosition,
                    'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]);

                if(count($data['works']) > 0){
                    $shockWorkPosition = 1;
                    foreach ($data['works'] as $item) {
                        $shockWork = ShockWork::create([
                            'shock_id' => $shock->id,
                            'supply_id' => $item['designation_id'],
                            'disassembly' => $item['disassembly'],
                            'replacement' => $item['replacement'],
                            'repair' => $item['repair'],
                            'paint' => $item['paint'],
                            'obsolescence_rate' => 0,
                            'obsolescence_amount_excluding_tax' => 0,
                            'obsolescence_amount_tax' => 0,
                            'obsolescence_amount' => 0,
                            'recovery_amount' => 0,
                            'recovery_amount_excluding_tax' => 0,
                            'recovery_amount_tax' => 0,
                            'recovery_amount' => 0,
                            'new_amount_excluding_tax' => 0,
                            'new_amount_tax' => 0,
                            'new_amount' => 0,
                            'position' => $shockWorkPosition,
                            'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                            'created_by' => auth()->user()->id,
                            'updated_by' => auth()->user()->id,
                        ]);
                        $shockWorkPosition++;
                    }
        
                }
                $shockPosition++;
            }
        }

        $status_id = Status::where('code', StatusEnum::IN_EDITING)->first()->id;
        $required_for_repairer_quote_validation = 0;
        if($assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()->id){
            // $status_id = Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE)->first()->id;
            // $required_for_repairer_quote_validation = 1;
        }

        $assignment->update([
            // 'repairer_id' => $request->repairer_id ?? $assignment->repairer_id,
            // 'client_id' => $client?->id ?? $assignment->client_id,
            // 'insurer_id' => $insurer?->id ?? $assignment->insurer_id,
            // 'vehicle_id' => $request->vehicle_id ?? $assignment->vehicle_id,
            'vehicle_mileage' => $request->vehicle_mileage ?? $assignment->vehicle_mileage,
            'policy_number' => $request->policy_number ?? $assignment->policy_number,

            'directed_by' => auth()->user()->id,
            'realized_by' => auth()->user()->id,
            'realized_at' => Carbon::now(),

            'work_sheet_established_by' => auth()->user()->id,
            'work_sheet_established_at' => Carbon::now(),
            // 'required_for_repairer_quote_validation' => $required_for_repairer_quote_validation,
            'status_id' => $status_id,
            
            'work_sheet_remark_id' => $request->work_sheet_remark_id,
            'expert_work_sheet_remark' => $request->expert_work_sheet_remark,
            'claim_number' => $request->claim_number ?? $assignment->claim_number,
            'weproov_reference' => $request->weproov_reference ?? $assignment->weproov_reference,
            'emails' => json_encode($request->emails),
            'repairer_signature' => $request->repairer_signature,
            'customer_signature' => $request->customer_signature,
            'work_sheet_established_by' => auth()->user()->id,
            'work_sheet_established_at' => Carbon::now(),
            'updated_by' => auth()->user()->id,
        ]);

        try {
            dispatch(new GenerateWorkSheetPdfJob($assignment, false));
        } catch (\Exception $e) {
            Log::error($e);
        }

        return $this->responseCreated('Opération effectuée avec succès', new AssignmentResource($assignment));

    }

    /**
     * Valider la fiche de travaux d'un dossier par l'expert
     *
     * @authenticated
     */
    public function validateWorkSheetByExpert(ValidateWorkSheetByExpertAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::with('shocks:id,quote_validated', 'shocks.shockWorks:id,quote_validated', 'shocks.workforces:id,quote_validated')->findOrFail($id);

        $status_id = Status::where('code', StatusEnum::IN_EDITING)->first()->id;
        $required_for_repairer_quote_validation = 0;
        if($request->required_for_repairer_quote_validation == 1){
            // $status_id = Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE)->first()->id;
            // $required_for_repairer_quote_validation = 1;
        }

        $assignment->update([
            'work_sheet_established_by' => auth()->user()->id,
            'work_sheet_established_at' => Carbon::now(),
            // 'required_for_repairer_quote_validation' => $required_for_repairer_quote_validation,
            'status_id' => $status_id,
            'updated_by' => auth()->user()->id,
        ]);

        dispatch(new GenerateWorkSheetPdfJob($assignment, true));
        // dispatch(new SendAssignmentActionMailJob($assignment));

        return $this->responseSuccess('Fiche de travaux validée avec succès', new AssignmentResource($assignment));
    }

    /**
     * Renvoyer une fiche de travaux
     *
     * @authenticated
     */
    public function resend_work_sheet(Request $request, $id)
    {
        $assignment = Assignment::where('id', $id)->firstOrFail();

        $assignment->update([
            'emails' => json_encode($request->emails),
            'updated_by' => auth()->user()->id,
        ]);

        dispatch(new GenerateWorkSheetPdfJob($assignment, false));

        return $this->responseCreated('Opération effectuée avec succès', new AssignmentResource($assignment));

    }

    /**
     * Ajouter une photo à la fiche de travaux
     *
     * @authenticated
     */
    public function add_work_sheet_photo(Request $request, $id)
    {
        $assignment = Assignment::where('id', $id)->firstOrFail();

        // if($assignment->status_id != Status::where('code', StatusEnum::OPENED)->first()->id && $assignment->status_id != Status::where('code', StatusEnum::REALIZED)->first()->id){
        //     return $this->responseUnprocessable("Impossible de créer la fiche de travaux", null);
        // }

        // if($assignment->work_sheet_established_at){
        //     return $this->responseUnprocessable("La fiche d'expertise est déjà établie.", null);
        // }

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
                    // $name = 'IMG_BP'.$today.'_'.$count.'.'.$file->getClientOriginalExtension();
                    $name = 'IMG_BP'.$today.'_'.$count.'.png';
                    $file->move(public_path("storage/photos/work-sheet/{$assignment->reference}"), $name);

                    $photo = Photo::create([
                        'name' => $name,
                        'is_cover' => false,
                        'assignment_id' => $assignment->id,
                        'photo_type_id' => PhotoType::where('code', PhotoTypeEnum::WORK_SHEET)->first()->id,
                        'status_id' => Status::where('code', StatusEnum::ACTIVE)->first()->id,
                        'created_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id,
                    ]);
                }
            }
        }

        return $this->responseCreated('Opération effectuée avec succès', new AssignmentResource($assignment));

    }

    /**
     * Afficher un dossier
     *
     * @authenticated
     */
    public function show($id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->load([
            // 'status', 'vehicle', 'claimNature', 'vehicle.brand', 'vehicle.vehicleModel', 'vehicle.color', 'vehicle.bodywork', 'insurer', 'additionalInsurer', 'repairers', 'client', 'assignmentType', 'expertiseType', 'generalState', 'technicalConclusion', 'documentTransmitted', 'createdBy', 'updatedBy', 'deletedBy', 'closedBy', 'cancelledBy', 'editedBy', 'realizedBy', 'referenceUpdatedBy', 'directedBy', 'workSheetEstablishedBy', 'validatedBy', 'unvalidatedBy', 'payments', 'invoices', 'openedBy', 
            // 'quoteValidatedByRepairerBy', 'quoteValidatedByExpertBy', 'quoteUnvalidatedByRepairerBy', 'quoteUnvalidatedByExpertBy', 'validatedByRepairerBy', 'validatedByExpertBy', 'unvalidatedByRepairerBy', 'unvalidatedByExpertBy', 'agreementForWorkSubjectToConditionsBy', 'paidBy', 'unpaidBy',
            'shocks' => function($query) {
                $query->orderBy('position', 'asc');
            },
            'shocks.shockPoint', 'shocks.shockWorks', 'shocks.shockWorks.supply', 'shocks.workforces', 'shocks.workforces.workforceType', 'shocks.paintType', 'shocks.hourlyRate', 'shocks.status', 'otherCosts', 'ascertainments', 'ascertainments.ascertainmentType', 'receipts', 'receipts.receiptType', 'status', 'vehicle', 'claimNature', 'vehicle.brand', 'vehicle.vehicleModel', 'vehicle.color', 'vehicle.bodywork', 'insurer', 'additionalInsurer', 'repairers', 'client', 'assignmentType', 'expertiseType', 'generalState', 'technicalConclusion', 'documentTransmitted', 'createdBy', 'updatedBy', 'deletedBy', 'closedBy', 'cancelledBy', 'editedBy', 'realizedBy', 'referenceUpdatedBy', 'directedBy', 'workSheetEstablishedBy', 'validatedBy', 'unvalidatedBy', 'payments', 'invoices', 'openedBy', 
            'quoteValidatedByRepairerBy', 'quoteValidatedByExpertBy', 'quoteUnvalidatedByRepairerBy', 'quoteUnvalidatedByExpertBy', 'validatedByRepairerBy', 'validatedByExpertBy', 'unvalidatedByRepairerBy', 'unvalidatedByExpertBy', 'agreementForWorkSubjectToConditionsBy', 'paidBy', 'unpaidBy',
            'shocks.shockWorks' => function($query) {
                $query->orderBy('position', 'asc');
            },
            'shocks.workforces' => function($query) {
                $query->orderBy('position', 'asc');
            },
            'shocks.workforces' => function($query) {
                $query->orderBy('position', 'asc');
            },
            'receipts' => function($query) {
                $query->orderBy('id', 'asc');
            },
            'receipts.receiptType' => function($query) {
                $query->orderBy('id', 'asc');
            },
            'ascertainments' => function($query) {
                $query->orderBy('id', 'asc');
            },
            'ascertainments.ascertainmentType' => function($query) {
                $query->orderBy('id', 'asc');
            },
        ]);
        return $this->responseSuccess(null, new AssignmentResource($assignment));
    }

    /**
     * Mettre à jour un dossier ouvert
     *
     * @authenticated
     */
    public function update(UpdateAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        if($request->claim_number && Assignment::where('claim_number', $request->claim_number)->where(function($query){
            $query->where('status_id', Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id)
                ->orWhere('status_id', Status::where('code', StatusEnum::OPENED)->first()->id)
                ->orWhere('status_id', Status::where('code', StatusEnum::REALIZED)->first()->id)
                ->orWhere('status_id', Status::where('code', StatusEnum::EDITED)->first()->id)
                ->orWhere('status_id', Status::where('code', StatusEnum::VALIDATED)->first()->id);
        })->count() > 1){
            return $this->responseUnprocessable('Le numéro de sinistre existe déjà pour un dossier ouvert, réalisé, rédigé ou validé');
        }

        $auth_user = User::with('currentRole')->where('id', auth()->user()->id)->first();
        
        // if($auth_user->currentRole->name == RoleEnum::SYSTEM_ADMIN->value || $auth_user->currentRole->name == RoleEnum::ADMIN->value || $auth_user->currentRole->name == RoleEnum::EXPERT_MANAGER->value || $auth_user->currentRole->name == RoleEnum::VALIDATOR->value || $auth_user->currentRole->name == RoleEnum::CEO->value){
            
        // }

        $insurer_id = $request->insurer_id;
        // $broker_id = $request->broker_id;
        $additional_insurer_id = $request->additional_insurer_id;
        $vehicle_id = $request->vehicle_id;
        $assignment_type_id = $request->assignment_type_id;
        $expertise_type_id = $request->expertise_type_id;

        if($request->assignment_type_id != $assignment->assignment_type_id || $request->insurer_id != $assignment->insurer_id  && $assignment->status_id != Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id){
            $now = Carbon::now();
            $annee = date("Y");
            $mois_jour_heure = date("mdH");
            $time = date("is");
            $today = $annee.'_'.$mois_jour_heure.'_'.$time;

            $year = substr($annee, -2);
            $month = date("m");
            $suffix = $month.'-'.$year.'-BCA';
            $reference = 'D'.$today;

            $insurer = Entity::find($request->insurer_id);

            $last_assignment = Assignment::where('reference_updated_at', 'like', Carbon::now()->format('Y-m').'%')->where('assignment_type_id', $request->assignment_type_id)->where('status_id','!=', Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id);
            if(!$last_assignment){
                $last_assignment = Assignment::where('created_at', 'like', Carbon::now()->format('Y-m').'%')->where('assignment_type_id', $request->assignment_type_id)->where('status_id','!=', Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id);
            }
            
            if(AssignmentTypeEnum::INSURER->value && $request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()->id){
                $latest_assignment = Assignment::where('reference_updated_at', 'like', Carbon::now()->format('Y-m').'%')->where('assignment_type_id', $request->assignment_type_id)->where('status_id','!=', Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id)->where(function($query) use ($request, $insurer){
                                        $query->where(['insurer_id' => $insurer->id])->orWhere(['broker_id' => $request->insurer_id]);
                                    })->latest('reference_updated_at')->first();

                if($latest_assignment && $insurer->entity_type_id == EntityType::where('code', EntityTypeEnum::BROKER)->first()->id){
                    $last_assignment = $latest_assignment;
                } else {
                    $last_assignment = $last_assignment->where(['broker_id' => null])->where(function($query) use ($request, $insurer){
                                        $query->where(['insurer_id' => $insurer->id])->orWhere(['broker_id' => $request->insurer_id]);
                                    })->latest('reference_updated_at')->first();
                }
            } else {
                $last_assignment = $last_assignment->latest('reference_updated_at')->first();
            }

            if($last_assignment){
                $prefix = explode("-", $last_assignment->reference)[0];
                $number = preg_match_all('/\d+/', $prefix, $matches);
                $number = $matches[0][0];
                $number = $number + 1;
                $prefix = preg_split('/\d+/', $prefix);
                if(mb_strlen($prefix[0]) == 1){
                    $prefix = $prefix[0][0];
                } else {
                    $prefix = $prefix[0];
                }
                $length = mb_strlen($number);
                if($length == 1){
                    $reference = $prefix.'0000'.$number.'-'.$suffix;
                } else if($length == 2){
                    $reference = $prefix.'000'.$number.'-'.$suffix;
                } else if($length == 3){
                    $reference = $prefix.'00'.$number.'-'.$suffix;
                } else if($length == 4){
                    $reference = $prefix.'0'.$number.'-'.$suffix;
                } else {
                    $reference = $prefix.$number.'-'.$suffix;
                }

                if(Assignment::where('reference', $reference)->exists()){
                    $assignment = Assignment::where('reference', $reference)->first();
                    
                    $prefix = explode("-", $assignment->reference)[0];
                    $number = preg_match_all('/\d+/', $prefix, $matches);
                    $number = $matches[0][0];
                    $number = $number + 1;
                    $prefix = preg_split('/\d+/', $prefix);
                    if(mb_strlen($prefix[0]) == 1){
                        $prefix = $prefix[0][0];
                    } else {
                        $prefix = $prefix[0];
                    }
                    $length = mb_strlen($number);
                    if($length == 1){
                        $reference = $prefix.'0000'.$number.'-'.$suffix;
                    } else if($length == 2){
                        $reference = $prefix.'000'.$number.'-'.$suffix;
                    } else if($length == 3){
                        $reference = $prefix.'00'.$number.'-'.$suffix;
                    } else if($length == 4){
                        $reference = $prefix.'0'.$number.'-'.$suffix;
                    } else {
                        $reference = $prefix.$number.'-'.$suffix;
                    }
                }
            } else {
                if($request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()?->id && $assignment->status_id != Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id){
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::NSIA)->first()?->id){
                        $reference = 'G00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::AXA)->first()?->id){
                        $reference = 'B00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::SANLAM_ALLIANZ)->first()?->id){
                        $reference = 'K00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::GNA)->first()?->id){
                        $reference = 'D00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::ATLANTIQUE)->first()?->id){
                        $reference = 'E00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::COMAR)->first()?->id){
                        $reference = 'C00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::AFG)->first()?->id){
                        $reference = 'AF00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::ATLANTA)->first()?->id){
                        $reference = 'A00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::ACTIVA)->first()?->id){
                        $reference = 'L00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::SIDAM)->first()?->id){
                        $reference = 'F00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::SUNU)->first()?->id){
                        $reference = 'S00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::MATCA)->first()?->id){
                        $reference = 'M00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::SCHIBA)->first()?->id){
                        $reference = 'H00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::SONAM)->first()?->id){
                        $reference = 'H00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::LEADWAY)->first()?->id){
                        $reference = 'V00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::AMSA)->first()?->id){
                        $reference = 'P00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::ACTIVA)->first()?->id){
                        $reference = 'L00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::SERENITY)->first()?->id){
                        $reference = 'M00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::COMAR)->first()?->id){
                        $reference = 'C00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::SAAR)->first()?->id){
                        $reference = 'I00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::WAFA)->first()?->id){
                        $reference = 'W00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::CORIS)->first()?->id){
                        $reference = 'Y00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::BNICB)->first()?->id){
                        $reference = 'Z00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::SMABTP)->first()?->id){
                        $reference = 'SM00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::LOYALE)->first()?->id){
                        $reference = 'LL00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::AACI)->first()?->id){
                        $reference = 'X00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::SONAR)->first()?->id){
                        $reference = 'SO00001'.'-'.$suffix;
                    }
    
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::ASCOMA)->first()?->id){
                        $reference = 'J00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::OLEA)->first()?->id){
                        $reference = 'Q00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::WTW)->first()?->id){
                        $reference = 'N00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::FILHEL)->first()?->id){
                        $reference = 'O00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::UNAP)->first()?->id){
                        $reference = 'U00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::BAROLD)->first()?->id){
                        $reference = 'BA00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::OREOLE)->first()?->id){
                        $reference = 'OR00001'.'-'.$suffix;
                    }
                    if($request->insurer_id == Entity::where('code', EntityCodeEnum::WALLYX)->first()?->id){
                        $reference = 'WA00001'.'-'.$suffix;
                    }
    
                    // if($request->broker_id == Entity::where('code', EntityCodeEnum::ASCOMA)->first()?->id){
                    //     $reference = 'J00001'.'-'.$suffix;
                    // }
                    // if($request->broker_id == Entity::where('code', EntityCodeEnum::OLEA)->first()?->id){
                    //     $reference = 'Q00001'.'-'.$suffix;
                    // }
                    // if($request->broker_id == Entity::where('code', EntityCodeEnum::WTW)->first()?->id){
                    //     $reference = 'N00001'.'-'.$suffix;
                    // }
                    // if($request->broker_id == Entity::where('code', EntityCodeEnum::FILHEL)->first()?->id){
                    //     $reference = 'O00001'.'-'.$suffix;
                    // }
                    // if($request->broker_id == Entity::where('code', EntityCodeEnum::UNAP)->first()?->id){
                    //     $reference = 'U00001'.'-'.$suffix;
                    // }
                }

                if($request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::PARTICULAR)->first()?->id){
                    $reference = 'PC00001'.'-'.$suffix;
                }
        
                if($request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::TAXI)->first()?->id){
                    $reference = 'TA00001'.'-'.$suffix;
                }
        
                if($request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()?->id){
                    $reference = 'EV00001'.'-'.$suffix;
                }

                if($request->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::AGAINST_EXPERTISE)->first()?->id){
                    $reference = 'CE00001'.'-'.$suffix;
                }
            }

            $insurer_id = $request->insurer_id;
            $broker_id = $request->broker_id;
            $vehicle_id = $request->vehicle_id;
            $assignment_type_id = $request->assignment_type_id;
            $expertise_type_id = $request->expertise_type_id;

            if($request->assignment_type_id == $assignment->assignment_type_id && $request->assignment_type_id != AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()->id){
                $reference = $assignment->reference;
            }
        }
        
        if($assignment->status_id != Status::where('code', StatusEnum::VALIDATED)->first()->id && $assignment->status_id != Status::where('code', StatusEnum::PAID)->first()->id){
            $oldReference = $assignment->reference;
            $assignment->update([
                'reference' => $reference ?? $assignment->reference,
                'insurer_id' => $insurer_id ?? $assignment->insurer_id,
                // 'broker_id' => $broker_id ?? $assignment->broker_id,
                'additional_insurer_id' => $additional_insurer_id ?? $assignment->additional_insurer_id,
                'vehicle_id' => $vehicle_id ?? $assignment->vehicle_id,
                'client_id' => $request->client_id ?? $assignment->client_id,
                'assignment_type_id' => $assignment_type_id ?? $assignment->assignment_type_id,
                // 'document_transmitted_id' => $request->document_transmitted_id ? json_encode($request->document_transmitted_id) : json_encode($assignment->document_transmitted_id),
                'expertise_type_id' => $expertise_type_id ?? $assignment->expertise_type_id,
                'policy_number' => $request->policy_number,
                'claim_number' => $request->claim_number ?? $assignment->claim_number,
                'weproov_reference' => $request->weproov_reference ?? $assignment->weproov_reference,
                'claim_starts_at' => $request->claim_starts_at,
                'claim_ends_at' => $request->claim_ends_at,
                'expertise_date' => $request->expertise_date,
                'expertise_place' => $request->expertise_place,
                'circumstance' => $request->circumstance,
                'damage_declared' => $request->damage_declared,
                'point_noted' => $request->point_noted,
                'received_at' => $request->received_at,
                'assured_value' => $request->assured_value,
                'vehicle_mileage' => $request->vehicle_mileage,
                'observation_realizer_name' => $request->observation_realizer_name,
                'observation_realizer_phone' => $request->observation_realizer_phone,
                'observation_place' => $request->observation_place,
                'observation_date' => $request->observation_date,
                'updated_by' => auth()->user()->id,    
            ]);

            $this->syncAssignmentRepairers($assignment, $request);
    
            if($request->vehicle_mileage){
                $vehicle = Vehicle::where('id',$request->vehicle_id)->first();
                $vehicle->mileage = $request->vehicle_mileage;
                $vehicle->save();
            }

            if($assignment->status_id != Status::where('code', StatusEnum::PENDING_FOR_OPENING)->first()->id && $oldReference != $assignment->reference){
                $reference = $assignment->reference;
                $assignment->update([
                    'reference_updated_by' => auth()->user()->id,
                    'reference_updated_at' => Carbon::now(),
                ]);

                // Renommer le dossier dans storage si la référence a changé
                $oldReportFolder = public_path("storage/photos/report/{$oldReference}");
                $newReportFolder = public_path("storage/photos/report/{$assignment->reference}");
                if (file_exists($oldReportFolder) && !file_exists($newReportFolder)) {
                    rename($oldReportFolder, $newReportFolder);
                }

                $oldWorkSheetFolder = public_path("storage/photos/work-sheet/{$oldReference}");
                $newWorkSheetFolder = public_path("storage/photos/work-sheet/{$assignment->reference}");
                if (file_exists($oldWorkSheetFolder) && !file_exists($newWorkSheetFolder)) {
                    rename($oldWorkSheetFolder, $newWorkSheetFolder);
                }
            }

            try {
                dispatch(new GenerateExpertiseSheetPdfJob($assignment));
            } catch (\Exception $e) {
                Log::error($e);
            }

            return $this->responseCreated('Opération effectuée avec succès', null);
        } else {
            return $this->responseUnprocessable("Impossible de mettre à jour ce dossier, car il est déjà validé ou payé.", null);
        }           
    }

    /**
     * Mettre à jour un dossier réalisé
     *
     * @authenticated
     */
    public function updateRealize(UpdateRealizedAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        if($assignment->status_id != Status::where('code', StatusEnum::VALIDATED)->first()->id && $assignment->status_id != Status::where('code', StatusEnum::PAID)->first()->id){
            $assignment->update([
                'vehicle_mileage' => $request->mileage,
                'expertise_date' => $request->expertise_date ?? $assignment->expertise_date,
                'expertise_place' => $request->expertise_place ?? $assignment->expertise_place,
                'point_noted' => $request->point_noted ?? $assignment->point_noted,
                'directed_by' => $request->directed_by ?? $assignment->directed_by,
                // 'required_for_repairer_quote_validation' => $request->required_for_repairer_quote_validation ?? $assignment->required_for_repairer_quote_validation,
                'updated_by' => auth()->user()->id,
            ]);

            $this->syncAssignmentRepairers($assignment, $request);

            if($request->mileage){
                $vehicle = Vehicle::where('id',$assignment->vehicle_id)->first();
                $vehicle->mileage = $request->mileage;
                $vehicle->save();
            }
    
            dispatch(new GenerateExpertiseSheetPdfJob($assignment));
    
            return $this->responseCreated('Opération effectuée avec succès', new AssignmentResource($assignment));
        } else {
            return $this->responseUnprocessable("Impossible de mettre à jour ce dossier, car il est déjà payé.", null);
        }

    }

    /**
     * Mettre à jour un dossier les informations d'un dossier redirigé
     *
     * @authenticated
     */
    public function updateEdit(UpdateEditAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        if($assignment->status_id != Status::where('code', StatusEnum::VALIDATED)->first()->id && $assignment->status_id != Status::where('code', StatusEnum::PAID)->first()->id){
            $vehicle = Vehicle::with('vehicleGenre', 'vehicleEnergy')->findOrFail($assignment->vehicle_id);

            $vehicle->update([
                'new_market_value' => $request->new_market_value ?? $vehicle->new_market_value,
                'mileage' => $request->mileage ?? $vehicle->mileage,
            ]);

            if($vehicle->vehicleGenre && $vehicle->vehicleEnergy){
                $marketValueService = app(MarketValueService::class);
                $result = json_decode(json_encode($marketValueService->calculateTheoreticalMarketValue($vehicle->vehicleGenre->id, $vehicle->vehicleEnergy->id, $vehicle->new_market_value, $vehicle->mileage, $vehicle->first_entry_into_circulation_date, $assignment->expertise_date)));
                
                $kilometric_incidence = 0;
                $is_up = null;
                if ($vehicle->vehicleEnergy->code == 'VE01') {
                    $max_mileage_essence_per_month = $vehicle->vehicleGenre->max_mileage_essence_per_year / 12;
                    $kilometric_incidence = (($max_mileage_essence_per_month * $result->month_diff) - $vehicle->mileage) * 25;
                } else {
                    $max_mileage_diesel_per_month = $vehicle->vehicleGenre->max_mileage_diesel_per_year / 12;
                    $kilometric_incidence = (($max_mileage_diesel_per_month * $result->month_diff) -$vehicle->mileage) * 40;
                }

                if ($kilometric_incidence > 0) {
                    $is_up = true;
                } else {
                    $is_up = false;
                    $kilometric_incidence = -1 * $kilometric_incidence;
                }

                $expertise_date = $result->expertise_date;
                $first_entry_into_circulation_date = $result->first_entry_into_circulation_date;
                $vehicle_age = $result->vehicle_age;
                $theorical_depreciation_rate = $result->theorical_depreciation_rate;
                $theorical_vehicle_market_value = $result->theorical_vehicle_market_value;


                if($assignment->evaluations){
                    $assignment_evaluations = json_decode($assignment->evaluations);
                    $less_value_work = $assignment->total_amount;
                    $special_accessory = $assignment_evaluations->special_accessory ?? 0;
                    $market_incidence = ceil($result->vehicle_new_value * $assignment_evaluations->market_incidence_rate / 100);

                    if($kilometric_incidence > ($theorical_vehicle_market_value / 2)){
                        $kilometric_incidence = $theorical_vehicle_market_value / 2;
                    }

                    if($assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()->id){
                        $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence - $less_value_work + $special_accessory : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence - $less_value_work + $special_accessory;
                    } else {
                        $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence;
                    }
                    $depreciation_rate = $result->vehicle_new_value > 0 ? number_format(100 - ($vehicle_market_value * 100 / $result->vehicle_new_value), 2, ',', '') : 0;
                    $depreciation_rate = floatval(str_replace(',', '.', $depreciation_rate));

                    $evaluations = [
                        'vehicle_age' => $result->vehicle_age,
                        'diff_year' => $result->year_diff,
                        'diff_month' => $result->month_diff,
                        'theorical_depreciation_rate' => $theorical_depreciation_rate,
                        'theorical_vehicle_market_value' => $theorical_vehicle_market_value,
                        'market_incidence_rate' => $assignment_evaluations->market_incidence_rate ?? 0,
                        'less_value_work' => $less_value_work ?? 0,
                        'special_accessory' => $special_accessory ?? 0,
                        'is_up' => $is_up,
                        'kilometric_incidence' => $kilometric_incidence,
                        'market_incidence' => $market_incidence ?? 0,
                        'depreciation_rate' => $depreciation_rate ?? 0,
                        'vehicle_market_value' =>  ceil($result->vehicle_new_value - ($result->vehicle_new_value * $depreciation_rate / 100)),
                        'vehicle_market_value_rounded' =>  ceil($result->vehicle_new_value - ($result->vehicle_new_value * $depreciation_rate / 100)),
                    ];
                }

            }

            if($request->evaluations){
                $evaluations = [
                    'vehicle_age' => $request->evaluations['vehicle_age'],
                    'diff_year' => $request->evaluations['year_diff'],
                    'diff_month' => $request->evaluations['month_diff'],
                    'theorical_depreciation_rate' => $request->evaluations['theorical_depreciation_rate'],
                    'theorical_vehicle_market_value' => $request->evaluations['theorical_vehicle_market_value'],
                    'market_incidence_rate' => $request->evaluations['market_incidence_rate'],
                    'less_value_work' => $request->evaluations['less_value_work'],
                    'special_accessory' => $request->evaluations['special_accessory'] ?? 0,
                    'is_up' => $request->evaluations['is_up'],
                    'kilometric_incidence' => $request->evaluations['kilometric_incidence'],
                    'market_incidence' => $request->evaluations['market_incidence'],
                    'depreciation_rate' => $request->evaluations['depreciation_rate'] ?? 0,
                    'vehicle_market_value' => $request->evaluations['vehicle_market_value'],
                    'vehicle_market_value_rounded' => $request->evaluations['vehicle_market_value_rounded'] ?? $request->evaluations['vehicle_market_value'],
                ];
            }
            
            $assignment->update([
                'general_state_id' => $request->general_state_id ?? $assignment->general_state_id,
                'claim_nature_id' => $request->claim_nature_id ?? $assignment->claim_nature_id,
                'technical_conclusion_id' => $request->technical_conclusion_id ?? $assignment->technical_conclusion_id,
                'seen_before_work_date' => $request->seen_before_work_date,
                'seen_during_work_date' => $request->seen_during_work_date,
                'seen_after_work_date' => $request->seen_after_work_date,
                'contact_date' => $request->contact_date,
                'expertise_place' => $request->expertise_place,
                'assured_value' => $request->assured_value,
                'salvage_value' => $request->salvage_value,
                'vehicle_mileage' => $request->mileage,
                'vehicle_new_market_value_option' => $request->vehicle_new_market_value_option ? strtolower($request->vehicle_new_market_value_option) : $assignment->vehicle_new_market_value_option,
                'new_market_value' => $request->new_market_value,
                'depreciation_rate' => $request->depreciation_rate,
                'market_value' => $request->market_value,
                'work_duration' => $request->work_duration,
                'report_remark_id' => $request->report_remark_id,
                'expert_report_remark' => $request->expert_report_remark,
                'instructions' => $request->instructions,
                'evaluations' => $request->evaluations ? json_encode($evaluations) : $assignment->evaluations,
                'updated_by' => auth()->user()->id,
            ]);

            $updateReceiptService = app(UpdateReceiptService::class);
            $updateReceiptService->updateReceipt($assignment->id);
    
            dispatch(new GenerateExpertiseReportPdfJob($assignment));
            // dispatch(new SendAssignmentActionMailJob($assignment));
    
            return $this->responseCreated('Opération effectuée avec succès', new AssignmentResource($assignment));
        } else {
            return $this->responseUnprocessable("Impossible de mettre à jour ce dossier, car il est déjà payé.", null);
        }

    }

    /**
     * Valider le devis par le repairer
     *
     * @authenticated
     */
    public function validateQuoteByRepairer($id): JsonResponse
    {
        $assignment = Assignment::with('shocks:id,quote_validated', 'shocks.shockWorks:id,quote_validated', 'shocks.workforces:id,quote_validated')->findOrFail($id);

        if($assignment->status_id == Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE)->first()->id){
            $assignment->update([
                'quote_validated_by_repairer' => 1,
                'quote_validated' => $assignment->quote_validated_by_expert == 1 ? 1 : 0,
                'quote_validated_by_repairer_by' => auth()->user()->id,
                'quote_validated_by_repairer_at' => Carbon::now(),
                'status_id' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE_VALIDATION)->first()->id,
                'updated_by' => auth()->user()->id,
            ]);

            if($assignment->quote_validated_by_repairer == 1 && $assignment->quote_validated_by_expert == 1){
                Shock::where('assignment_id', $assignment->id)->update([
                    'quote_validated' => 1,
                ]);
    
                ShockWork::whereIn('shock_id', $assignment->shocks->pluck('id'))->update([
                    'quote_validated' => 1,
                ]);
                
                Workforce::whereIn('shock_id',$assignment->shocks->pluck('id'))->update([
                    'quote_validated' => 1,
                ]);
            }
            // dispatch(new SendAssignmentActionMailJob($assignment));
            return $this->responseSuccess('Dossier validé par le repairer avec succès', new AssignmentResource($assignment));
        } else {
            return $this->responseUnprocessable("Le dossier n'est pas en attente de la facture du réparateur.", null);
        }
    }

    /**
     * Dévalider le devis par le repairer
     *
     * @authenticated
     */
    public function unvalidateQuoteByRepairer(UnvalidateQuoteAssignmentByRepairerRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        if($assignment->status_id == Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE_VALIDATION)->first()->id){
            $assignment->update([
                'quote_validated_by_repairer' => 0,
                'quote_validated' => 0,
                'quote_unvalidated_by_repairer_by' => auth()->user()->id,
                'quote_unvalidated_by_repairer_at' => Carbon::now(),
                'quote_unvalidation_by_repairer_reason' => $request->quote_unvalidation_by_repairer_reason,
                'status_id' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE)->first()->id,
                'updated_by' => auth()->user()->id,
            ]);
            return $this->responseSuccess('Dossier dévalidé par l\'expert avec succès', new AssignmentResource($assignment));
        } else {
            return $this->responseUnprocessable("Le dossier n'est pas en attente de validation de la facture du réparateur.", null);
        }
    }

    /**
     * Valider le devis par l'expert
     *
     * @authenticated
     */
    public function validateQuoteByExpert($id): JsonResponse
    {
        $assignment = Assignment::with('shocks:id,quote_validated', 'shocks.shockWorks:id,quote_validated', 'shocks.workforces:id,quote_validated')->findOrFail($id);

        if($assignment->status_id == Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE)->first()->id || $assignment->status_id == Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE_VALIDATION)->first()->id){
            $assignment->update([
                'quote_validated_by_expert' => 1,
                'agreement_for_work_subject_to_conditions' => 0,
                'quote_validated' => $assignment->quote_validated_by_repairer == 1 ? 1 : 0,
                'quote_validated_by_expert_by' => auth()->user()->id,
                'quote_validated_by_expert_at' => Carbon::now(),
                'status_id' => Status::where('code', StatusEnum::IN_EDITING)->first()->id,
                'updated_by' => auth()->user()->id,
            ]);

            
            if($assignment->quote_validated_by_repairer == 1 && $assignment->quote_validated_by_expert == 1){
                Shock::where('assignment_id', $assignment->id)->update([
                    'quote_validated' => 1,
                ]);
    
                ShockWork::whereIn('shock_id', $assignment->shocks->pluck('id'))->update([
                    'quote_validated' => 1,
                ]);
                
                Workforce::whereIn('shock_id',$assignment->shocks->pluck('id'))->update([
                    'quote_validated' => 1,
                ]);
            }
            // dispatch(new SendAssignmentActionMailJob($assignment));
    
            return $this->responseSuccess('Devis validé par l\'expert avec succès', new AssignmentResource($assignment));
        } else {
            return $this->responseUnprocessable("Le devis n'est pas en attente de validation par l'expert.", null);
        }
    }

    /**
     * Valider le devis par l'expert sous réserve de conditions
     *
     * @authenticated
     */
    public function validateQuoteWithConditionsByExpert(ValidateAssignmentQuoteWithConditionsRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::with('shocks:id,quote_validated', 'shocks.shockWorks:id,quote_validated', 'shocks.workforces:id,quote_validated')->findOrFail($id);

        if($assignment->status_id == Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE)->first()->id || $assignment->status_id == Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE_VALIDATION)->first()->id){
            $assignment->update([
                'quote_validated_by_expert' => 1,
                'agreement_for_work_subject_to_conditions' => 1,
                'conditions' => $request->conditions,
                'quote_validated_by_expert_by' => auth()->user()->id,
                'quote_validated_by_expert_at' => Carbon::now(),
                'agreement_for_work_subject_to_conditions_by' => auth()->user()->id,
                'agreement_for_work_subject_to_conditions_at' => Carbon::now(),
                'status_id' => Status::where('code', StatusEnum::IN_EDITING)->first()->id,
                'updated_by' => auth()->user()->id,
            ]);
            // dispatch(new SendAssignmentActionMailJob($assignment));
    
            return $this->responseSuccess('Devis validé par l\'expert avec succès', new AssignmentResource($assignment));
        } else {
            return $this->responseUnprocessable("Le devis n'est pas en attente de validation par l'expert.", null);
        }
    }

    /**
     * Dévalider le devis par l'expert
     *
     * @authenticated
     */
    public function unvalidateQuoteByExpert(UnvalidateQuoteAssignmentByExpertRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $status_id = Status::where('code', StatusEnum::REALIZED)->first()->id;
        $required_for_repairer_quote_validation = 0;
        // if($assignment->required_for_repairer_quote_validation == 1){
        //     $status_id = Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE_VALIDATION)->first()->id,
        //     $required_for_repairer_quote_validation = 1;
        // }

        $assignment->update([
            'quote_validated_by_expert' => 0,
            'quote_validated' => 0,
            'agreement_for_work_subject_to_conditions' => 0,
            'quote_unvalidated_by_expert_by' => auth()->user()->id,
            'quote_unvalidated_by_expert_at' => Carbon::now(),
            'quote_unvalidation_by_expert_reason' => $request->quote_unvalidation_by_expert_reason,
            'required_for_repairer_quote_validation' => $required_for_repairer_quote_validation,
            'status_id' => $status_id,
            'updated_by' => auth()->user()->id,
        ]);
        return $this->responseSuccess('Dossier dévalidé par l\'expert avec succès', new AssignmentResource($assignment));

        // if($assignment->status_id == Status::where('code', StatusEnum::IN_EDITING)->first()->id){
        //     $assignment->update([
        //         'quote_validated_by_expert' => 0,
        //         'quote_validated' => 0,
        //         'agreement_for_work_subject_to_conditions' => 0,
        //         'quote_unvalidated_by_expert_by' => auth()->user()->id,
        //         'quote_unvalidated_by_expert_at' => Carbon::now(),
        //         'quote_unvalidation_by_expert_reason' => $request->quote_unvalidation_by_expert_reason,
        //         'status_id' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_QUOTE)->first()->id,
        //         'updated_by' => auth()->user()->id,
        //     ]);
        //     return $this->responseSuccess('Dossier dévalidé par l\'expert avec succès', new AssignmentResource($assignment));
        // } else {
        //     return $this->responseUnprocessable("Le dossier n'est pas encore en en attente de validation du devis.", null);
        // }
    }

    /**
     * Valider la rédaction d'un dossier
     *
     * @authenticated
     */
    public function validateEdition($id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        if($assignment->agreement_for_work_subject_to_conditions == 1){
            return $this->responseUnprocessable("Le devis du dossier n'est pas validé definitivement par l'expert, veuillez le valider avant de valider la rédaction.", null);
        }

        $assignment->update([
            'edition_validated' => 1,
            'edition_validated_by' => auth()->user()->id,
            'edition_validated_at' => Carbon::now(),
            'edited_by' => auth()->user()->id,
            'edited_at' => Carbon::now(),
            'status_id' => Status::where('code', StatusEnum::IN_FIRST_CORRECTION)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        dispatch(new GenerateExpertiseReportPdfJob($assignment));

        return $this->responseSuccess('Dossier validé avec succès', new AssignmentResource($assignment));
    }

    /**
     * Dévalider la rédaction d'un dossier
     *
     * @authenticated
     */
    public function unvalidateEdition(UnvalidateAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $assignment->update([
            'edition_validated' => 0,
            'edition_unvalidated_by' => auth()->user()->id,
            'edition_unvalidated_at' => Carbon::now(),
            'edition_unvalidation_reason' => $request->edition_unvalidation_reason,
            'status_id' => Status::where('code', StatusEnum::FOR_CORRECTION)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        dispatch(new GenerateExpertiseReportPdfJob($assignment));

        return $this->responseSuccess('Dossier dévalidé avec succès', new AssignmentResource($assignment));
    }

    /**
     * Valider la première correction d'un dossier
     *
     * @authenticated
     */
    public function validateFirstCorrection($id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        if($assignment->agreement_for_work_subject_to_conditions == 1){
            return $this->responseUnprocessable("Le devis du dossier n'est pas validé definitivement par l'expert, veuillez le valider avant de valider la rédaction.", null);
        }

        $assignment->update([
            'first_correction_validated' => 1,
            'first_correction_validated_by' => auth()->user()->id,
            'first_correction_validated_at' => Carbon::now(),
            'status_id' => Status::where('code', StatusEnum::IN_SECOND_CORRECTION)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        dispatch(new GenerateExpertiseReportPdfJob($assignment));

        return $this->responseSuccess('Première correction validée avec succès', new AssignmentResource($assignment));
    }

    /**
     * Dévalider la première correction d'un dossier
     *
     * @authenticated
     */
    public function unvalidateFirstCorrection(UnvalidateFirstCorrectionAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $assignment->update([
            'first_correction_validated' => 0,
            'first_correction_unvalidated_by' => auth()->user()->id,
            'first_correction_unvalidated_at' => Carbon::now(),
            'first_correction_unvalidation_reason' => $request->first_correction_unvalidation_reason,
            'status_id' => Status::where('code', StatusEnum::FOR_CORRECTION)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        return $this->responseSuccess('Dossier dévalidé avec succès', new AssignmentResource($assignment));
    }

    /**
     * Valider la seconde correction d'un dossier
     *
     * @authenticated
     */
    public function validateSecondCorrection(ValidateSecondCorrectionAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        if($assignment->agreement_for_work_subject_to_conditions == 1){
            return $this->responseUnprocessable("Le devis du dossier n'est pas validé definitivement par l'expert, veuillez le valider avant de valider la rédaction.", null);
        }

        $status_id = Status::where('code', StatusEnum::VALIDATED)->first()->id;
        $required_for_repairer_validation = 0;
        // if($assignment->required_for_repairer_quote_validation == 1){
        //     $status_id = Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_VALIDATION)->first()->id;
        //     $required_for_repairer_validation = 1;
        // }

        // if($assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()->id){
        //     $status_id = Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_VALIDATION)->first()->id;
        // } else {
        //     $status_id = Status::where('code', StatusEnum::VALIDATED)->first()->id;
        // }

        $assignment->update([
            'is_validated' => 1,
            'validated_by' => auth()->user()->id,
            'validated_at' => Carbon::now(),
            'second_correction_validated' => 1,
            'second_correction_validated_by' => auth()->user()->id,
            'second_correction_validated_at' => Carbon::now(),
            'required_for_repairer_validation' => $required_for_repairer_validation,
            'status_id' => $status_id,
            'updated_by' => auth()->user()->id,
        ]);

        dispatch(new GenerateExpertiseReportPdfJob($assignment));
        // dispatch(new SendAssignmentActionMailJob($assignment));

        return $this->responseSuccess('Première correction validée avec succès'.$status_id, new AssignmentResource($assignment));
    }

    /**
     * Dévalider la seconde correction d'un dossier
     *
     * @authenticated
     */
    public function unvalidateSecondCorrection(UnvalidateSecondCorrectionAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $assignment->update([
            'second_correction_validated' => 0,
            'second_correction_unvalidated_by' => auth()->user()->id,
            'second_correction_unvalidated_at' => Carbon::now(),
            'second_correction_unvalidation_reason' => $request->second_correction_unvalidation_reason,
            'status_id' => Status::where('code', StatusEnum::FOR_CORRECTION)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        dispatch(new GenerateExpertiseReportPdfJob($assignment));

        return $this->responseSuccess('Dossier dévalidé avec succès', new AssignmentResource($assignment));
    }

    /**
     * Valider un dossier par le repairer
     *
     * @authenticated
     */
    public function validateByRepairer($id): JsonResponse
    {
        $assignment = Assignment::with('shocks:id,quote_validated', 'shocks.shockWorks:id,quote_validated', 'shocks.workforces:id,quote_validated')->findOrFail($id);

        if($assignment->status_id == Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_VALIDATION)->first()->id){
            $assignment->update([
                'is_validated_by_repairer' => 1,
                'is_validated' => $assignment->is_validated_by_expert == 1 ? 1 : 0,
                'validated_by_repairer_by' => auth()->user()->id,
                'validated_by_repairer_at' => Carbon::now(),
                'status_id' => Status::where('code', StatusEnum::PENDING_FOR_EXPERT_VALIDATION)->first()->id,
                'updated_by' => auth()->user()->id,
            ]);
    
            if($assignment->is_validated_by_expert == 1 && $assignment->is_validated_by_repairer == 1){
                Shock::where('assignment_id', $assignment->id)->update([
                    'is_validated' => 1,
                ]);
            }
            // dispatch(new SendAssignmentActionMailJob($assignment));

            return $this->responseSuccess('Dossier validé par le repairer avec succès', new AssignmentResource($assignment));
        } else {
            return $this->responseUnprocessable("Le dossier n'est pas en attente de la facture du réparateur.", null);
        }

        
    }

    /**
     * Dévalider un dossier par le repairer
     *
     * @authenticated
     */
    public function unvalidateByRepairer(UnvalidateAssignmentByRepairerRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $assignment->update([
            'is_validated_by_expert' => 0,
            'is_validated' => 0,
            'unvalidated_by_expert_by' => auth()->user()->id,
            'unvalidated_by_expert_at' => Carbon::now(),
            'unvalidation_by_repairer_reason' => $request->unvalidation_by_repairer_reason,
            'status_id' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_VALIDATION)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);
        return $this->responseSuccess('Dossier dévalidé par l\'expert avec succès', new AssignmentResource($assignment));

        // if($assignment->status_id == Status::where('code', StatusEnum::PENDING_FOR_EXPERT_VALIDATION)->first()->id){
        //     $assignment->update([
        //         'is_validated_by_expert' => 0,
        //         'is_validated' => 0,
        //         'unvalidated_by_expert_by' => auth()->user()->id,
        //         'unvalidated_by_expert_at' => Carbon::now(),
        //         'unvalidation_by_repairer_reason' => $request->unvalidation_by_repairer_reason,
        //         'status_id' => Status::where('code', StatusEnum::PENDING_FOR_REPAIRER_VALIDATION)->first()->id,
        //         'updated_by' => auth()->user()->id,
        //     ]);
        //     return $this->responseSuccess('Dossier dévalidé par l\'expert avec succès', new AssignmentResource($assignment));
        // } else {
        //     return $this->responseUnprocessable("Le dossier n'est pas en attente de validation de la facture du réparateur.", null);
        // }
    }

    /**
     * Valider un dossier par l'expert
     *
     * @authenticated
     */
    public function validateByExpert($id): JsonResponse
    {
        $assignment = Assignment::with('shocks:id,is_validated', 'shocks.shockWorks:id,quote_validated', 'shocks.workforces:id,quote_validated')->findOrFail($id);

        if($assignment->status_id == Status::where('code', StatusEnum::IN_EDITING)->first()->id || $assignment->status_id == Status::where('code', StatusEnum::PENDING_FOR_EXPERT_VALIDATION)->first()->id){
            $assignment->update([
                'is_validated_by_expert' => 1,
                'is_validated' => $assignment->is_validated_by_repairer == 1 ? 1 : 0,
                'validated_by_expert_by' => auth()->user()->id,
                'validated_by_expert_at' => Carbon::now(),
                'status_id' => Status::where('code', StatusEnum::VALIDATED)->first()->id,
                'updated_by' => auth()->user()->id,
            ]);
    
            if($assignment->is_validated_by_expert == 1 && $assignment->is_validated_by_repairer == 1){
                Shock::where('assignment_id', $assignment->id)->update([
                    'is_validated' => 1,
                ]);
            }

            $receipt_amount = Receipt::where('assignment_id', $assignment->id)->sum('amount');
            $payment_amount = Payment::where('assignment_id', $assignment->id)->sum('amount');
            if($receipt_amount > 0){
                if($receipt_amount <= $payment_amount){
                    $assignment->update([
                        'status_id' => Status::where('code', StatusEnum::PAID)->first()->id,
                    ]);
                } 
            }

            dispatch(new GenerateExpertiseReportPdfJob($assignment));
            // dispatch(new SendAssignmentActionMailJob($assignment));
    
            return $this->responseSuccess('Dossier validé par l\'expert avec succès', new AssignmentResource($assignment));
        } else {
            return $this->responseUnprocessable("Le dossier n'est pas en attente de facture du réparateur ou en attente de validation de facture par le repairer.", null);
        }
    }

    /**
     * Dévalider un dossier par l'expert
     *
     * @authenticated
     */
    public function unvalidateByExpert(UnvalidateAssignmentByExpertRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        if($assignment->status_id == Status::where('code', StatusEnum::VALIDATED)->first()->id){
            $status_id = Status::where('code', StatusEnum::IN_EDITING)->first()->id;
            // if($assignment->required_for_repairer_quote_validation == 1){
            //     $status_id = Status::where('code', StatusEnum::PENDING_FOR_EXPERT_VALIDATION)->first()->id;
            // } else {
            //     $status_id = Status::where('code', StatusEnum::IN_EDITING)->first()->id;
            // }
            $assignment->update([
                'is_validated_by_expert' => 0,
                'is_validated' => 0,
                'unvalidated_by_expert_by' => auth()->user()->id,
                'unvalidated_by_expert_at' => Carbon::now(),
                'unvalidation_by_expert_reason' => $request->unvalidation_by_expert_reason,
                'status_id' => $status_id,
                'updated_by' => auth()->user()->id,
            ]);

            dispatch(new GenerateExpertiseReportPdfJob($assignment));

            return $this->responseSuccess('Dossier dévalidé par l\'expert avec succès', new AssignmentResource($assignment));
        } else {
            return $this->responseUnprocessable("Le dossier n'est pas encore validé par le repairer.", null);
        }
    }

    /**
     * Dévalider un dossier
     *
     * @authenticated
     */
    public function unvalidate($id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        if($assignment->status_id == Status::where('code', StatusEnum::VALIDATED)->first()->id){
            $status_id = Status::where('code', StatusEnum::IN_EDITING)->first()->id;
            // if($assignment->required_for_repairer_quote_validation == 1){
            //     $status_id = Status::where('code', StatusEnum::PENDING_FOR_EXPERT_VALIDATION)->first()->id;
            // } else {
            //     $status_id = Status::where('code', StatusEnum::IN_EDITING)->first()->id;
            // }
            $assignment->update([
                'is_validated_by_expert' => 0,
                'is_validated' => 0,
                'unvalidated_by_expert_by' => auth()->user()->id,
                'unvalidated_by_expert_at' => Carbon::now(),
                'status_id' => $status_id,
                'updated_by' => auth()->user()->id,
            ]);

            dispatch(new GenerateExpertiseReportPdfJob($assignment));

            return $this->responseSuccess('Dossier dévalidé par l\'expert avec succès', new AssignmentResource($assignment));
        } else {
            return $this->responseUnprocessable("Le dossier n'est pas encore validé par le repairer.", null);
        }
    }

    /**
     * Clôturer un dossier
     *
     * @authenticated
     */
    public function close(CloseAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $assignment->update([
            'closed_by' => auth()->user()->id,
            'closed_at' => Carbon::now(),
            'closing_reason' => $request->closing_reason,
            'status_id' => Status::where('code', StatusEnum::CLOSED)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        return $this->responseSuccess('Dossier clôturé avec succès', new AssignmentResource($assignment));
    }

    /**
     * Marquer un dossier comme payé
     *
     * @authenticated
     */
    public function markAsPaid($id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $assignment->update([
            'paid_by' => auth()->user()->id,
            'paid_at' => Carbon::now(),
            'status_id' => Status::where('code', StatusEnum::PAID)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        dispatch(new GenerateExpertiseReportPdfJob($assignment));

        return $this->responseSuccess('Dossier marqué comme payé avec succès', new AssignmentResource($assignment));
    }

    /**
     * Marquer un dossier comme non payé
     *
     * @authenticated
     */
    public function markAsUnpaid(MarkAsUnpaidAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $assignment->update([
            'unpaid_by' => auth()->user()->id,
            'unpaid_at' => Carbon::now(),
            'unpaid_reason' => $request->unpaid_reason,
            'status_id' => Status::where('code', StatusEnum::VALIDATED)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        dispatch(new GenerateExpertiseReportPdfJob($assignment));

        return $this->responseSuccess('Dossier marqué comme impayé avec succès', new AssignmentResource($assignment));
    }

    /**
     * Classer un dossier sans suite
     *
     * @authenticated
     */
    public function classifyWithoutFurtherAction($id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $assignment->update([
            'classified_without_further_action_by' => auth()->user()->id,
            'classified_without_further_action_at' => Carbon::now(),
            'status_id' => Status::where('code', StatusEnum::CLASSIFIED_WITHOUT_FURTHER_ACTION)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        return $this->responseSuccess('Dossier classé sans suite avec succès', new AssignmentResource($assignment));
    }

    /**
     * Annuler un dossier
     *
     * @authenticated
     */
    public function cancel(CancelAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $assignment->update([
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_by' => auth()->user()->id,
            'cancelled_at' => Carbon::now(),
            'status_id' => Status::where('code', StatusEnum::CANCELLED)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        return $this->responseSuccess('Dossier annulé avec succès', new AssignmentResource($assignment));
    }

    /**
     * Générer une un rapport d'expertise
     *
     * @authenticated
     */
    public function generate($id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $vehicle = Vehicle::with('vehicleGenre', 'vehicleEnergy')->findOrFail($assignment->vehicle_id);

        if($vehicle->vehicleGenre && $vehicle->vehicleEnergy){
            $marketValueService = app(MarketValueService::class);
            $result = json_decode(json_encode($marketValueService->calculateTheoreticalMarketValue($vehicle->vehicleGenre->id, $vehicle->vehicleEnergy->id, $vehicle->new_market_value, $vehicle->mileage, $vehicle->first_entry_into_circulation_date, $assignment->expertise_date)));
            
            $kilometric_incidence = 0;
            $is_up = null;
            if ($vehicle->vehicleEnergy->code == 'VE01') {
                $max_mileage_essence_per_month = $vehicle->vehicleGenre->max_mileage_essence_per_year / 12;
                $kilometric_incidence = (($max_mileage_essence_per_month * $result->month_diff) - $vehicle->mileage) * 25;
            } else {
                $max_mileage_diesel_per_month = $vehicle->vehicleGenre->max_mileage_diesel_per_year / 12;
                $kilometric_incidence = (($max_mileage_diesel_per_month * $result->month_diff) -$vehicle->mileage) * 40;
            }

            if ($kilometric_incidence > 0) {
                $is_up = true;
            } else {
                $is_up = false;
                $kilometric_incidence = -1 * $kilometric_incidence;
            }

            $expertise_date = $result->expertise_date;
            $first_entry_into_circulation_date = $result->first_entry_into_circulation_date;
            $vehicle_age = $result->vehicle_age;
            $theorical_depreciation_rate = $result->theorical_depreciation_rate;
            $theorical_vehicle_market_value = $result->theorical_vehicle_market_value;


            if($assignment->evaluations){
                $assignment_evaluations = json_decode($assignment->evaluations);
                $less_value_work = $assignment->total_amount;
                $special_accessory = $assignment_evaluations->special_accessory ?? 0;
                $market_incidence = ceil($result->vehicle_new_value * $assignment_evaluations->market_incidence_rate / 100);

                if($kilometric_incidence > ($theorical_vehicle_market_value / 2)){
                    $kilometric_incidence = $theorical_vehicle_market_value / 2;
                }

                if($assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()->id){
                    $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence - $less_value_work + $special_accessory : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence - $less_value_work + $special_accessory;
                } else {
                    $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence;
                }
                $depreciation_rate = $result->vehicle_new_value > 0 ? number_format(100 - ($vehicle_market_value * 100 / $result->vehicle_new_value), 2, ',', '') : 0;
                $depreciation_rate = floatval(str_replace(',', '.', $depreciation_rate));

                $evaluations = [
                    'vehicle_age' => $result->vehicle_age,
                    'diff_year' => $result->year_diff,
                    'diff_month' => $result->month_diff,
                    'theorical_depreciation_rate' => $theorical_depreciation_rate,
                    'theorical_vehicle_market_value' => $theorical_vehicle_market_value,
                    'market_incidence_rate' => $assignment_evaluations->market_incidence_rate ?? 0,
                    'less_value_work' => $less_value_work ?? 0,
                    'special_accessory' => $special_accessory ?? 0,
                    'is_up' => $is_up,
                    'kilometric_incidence' => $kilometric_incidence,
                    'market_incidence' => $market_incidence ?? 0,
                    'depreciation_rate' => $depreciation_rate ?? 0,
                    'vehicle_market_value' =>  ceil($result->vehicle_new_value - ($result->vehicle_new_value * $depreciation_rate / 100)),
                    'vehicle_market_value_rounded' =>  ceil($result->vehicle_new_value - ($result->vehicle_new_value * $depreciation_rate / 100)),
                ];

                // $assignment->update([
                //     'evaluations' => json_encode($evaluations),
                // ]);
            }
        }

        $now = now();
        $generated_at = Carbon::parse($assignment->generated_at);
        $generate_time = $generated_at->addSeconds(10);
        $generate_time = $generate_time->diffInSeconds($now);

        if(!$assignment->generated_at || ($assignment->generated_at && $generate_time > 5)){
            if($assignment->status_id == Status::where('code', StatusEnum::EDITED)->first()->id || $assignment->status_id == Status::where('code', StatusEnum::VALIDATED)->first()->id || $assignment->status_id == Status::where('code', StatusEnum::PAID)->first()->id){
                $total_nb_hours = Workforce::join('shocks', 'workforces.shock_id', '=', 'shocks.id')
                                ->join('assignments', 'shocks.assignment_id', '=', 'assignments.id')
                                ->select('workforces.nb_hours')
                                ->where('shocks.status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)
                                ->where('workforces.status_id', Status::where('code', StatusEnum::ACTIVE)->first()->id)
                                ->where('assignments.id', $assignment->id)
                                ->sum('workforces.nb_hours');
    
                $work_duration = null;
                $work_duration_service = app(WorkDurationService::class);
                $work_duration = $work_duration_service->calculateWorkDuration($total_nb_hours);
    
                $vehicle = Vehicle::with('vehicleGenre', 'vehicleEnergy')->findOrFail($assignment->vehicle_id);
    
                $assignment->update([
                    'work_duration' => $work_duration,
                ]);
    
                // dispatch(new GenerateExpertiseReportPdfJob($assignment));
            }

            dispatch(new GenerateExpertiseReportPdfJob($assignment));
    
            dispatch(new GenerateExpertiseSheetPdfJob($assignment));
            dispatch(new GenerateWorkSheetPdfJob($assignment, true));

            // dispatch(new SendAssignmentActionMailJob($assignment));

            $assignment->update([
                'generated_at' => Carbon::now(), 
            ]);
        } else {
            // return $this->responseUnprocessable("Le dossier a déjà été généré. Réessayer dans ".ceil(5 - $generate_time)." secondes.", null);
        }

        return $this->responseSuccess('Rapport d\'expertise généré avec succès', new AssignmentResource($assignment));
    }

    /**
     * Calculer le taux de dépréciation d'un dossier
     *
     * @authenticated
     */
    public function calculate_depreciation_rate($id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $vehicle = Vehicle::with('vehicleGenre', 'vehicleEnergy')->findOrFail($assignment->vehicle_id);

        if($vehicle->vehicleGenre && $vehicle->vehicleEnergy && $vehicle->new_market_value > 0 && $vehicle->mileage > 0 && $vehicle->first_entry_into_circulation_date && $assignment->expertise_date){
            $marketValueService = app(MarketValueService::class);
            $result = json_decode(json_encode($marketValueService->calculateTheoreticalMarketValue($vehicle->vehicleGenre->id, $vehicle->vehicleEnergy->id, $vehicle->new_market_value, $vehicle->mileage, $vehicle->first_entry_into_circulation_date, $assignment->expertise_date)));
            
            $kilometric_incidence = 0;
            $is_up = null;
            if ($vehicle->vehicleEnergy->code == 'VE01') {
                $max_mileage_essence_per_month = $vehicle->vehicleGenre->max_mileage_essence_per_year / 12;
                $kilometric_incidence = (($max_mileage_essence_per_month * $result->month_diff) - $vehicle->mileage) * 25;
            } else {
                $max_mileage_diesel_per_month = $vehicle->vehicleGenre->max_mileage_diesel_per_year / 12;
                $kilometric_incidence = (($max_mileage_diesel_per_month * $result->month_diff) -$vehicle->mileage) * 40;
            }

            if ($kilometric_incidence > 0) {
                $is_up = true;
            } else {
                $is_up = false;
                $kilometric_incidence = -1 * $kilometric_incidence;
            }

            $expertise_date = $result->expertise_date;
            $first_entry_into_circulation_date = $result->first_entry_into_circulation_date;
            $vehicle_age = $result->vehicle_age;
            $theorical_depreciation_rate = $result->theorical_depreciation_rate;
            $theorical_vehicle_market_value = $result->theorical_vehicle_market_value;


            if($assignment->evaluations){
                $assignment_evaluations = json_decode($assignment->evaluations);
                $less_value_work = $assignment->total_amount;
                $special_accessory = $assignment_evaluations->special_accessory ?? 0;
                $market_incidence = ceil($result->vehicle_new_value * $assignment_evaluations->market_incidence_rate / 100);

                if($kilometric_incidence > ($theorical_vehicle_market_value / 2)){
                    $kilometric_incidence = $theorical_vehicle_market_value / 2;
                }

                if($assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::EVALUATION)->first()->id){
                    $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence - $less_value_work + $special_accessory : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence - $less_value_work + $special_accessory;
                } else {
                    $vehicle_market_value = $is_up ? $theorical_vehicle_market_value + $market_incidence + $kilometric_incidence : $theorical_vehicle_market_value + $market_incidence - $kilometric_incidence;
                }
                $depreciation_rate = $result->vehicle_new_value > 0 ? number_format(100 - ($vehicle_market_value * 100 / $result->vehicle_new_value), 2, ',', '') : 0;
                $depreciation_rate = floatval(str_replace(',', '.', $depreciation_rate));

                $evaluations = [
                    'vehicle_age' => $result->vehicle_age,
                    'diff_year' => $result->year_diff,
                    'diff_month' => $result->month_diff,
                    'theorical_depreciation_rate' => $theorical_depreciation_rate,
                    'theorical_vehicle_market_value' => $theorical_vehicle_market_value,
                    'market_incidence_rate' => $assignment_evaluations->market_incidence_rate ?? 0,
                    'less_value_work' => $less_value_work ?? 0,
                    'special_accessory' => $special_accessory ?? 0,
                    'is_up' => $is_up,
                    'kilometric_incidence' => $kilometric_incidence,
                    'market_incidence' => $market_incidence ?? 0,
                    'depreciation_rate' => $depreciation_rate ?? 0,
                    'vehicle_market_value' =>  ceil($result->vehicle_new_value - ($result->vehicle_new_value * $depreciation_rate / 100)),
                    'vehicle_market_value_rounded' =>  ceil($result->vehicle_new_value - ($result->vehicle_new_value * $depreciation_rate / 100)),
                ];

                $assignment->update([
                    'evaluations' => json_encode($evaluations),
                ]);

                dispatch(new GenerateExpertiseReportPdfJob($assignment));

                return $this->responseSuccess('Taux de dépréciation calculé avec succès', null);

            }
        } else {
            return $this->responseUnprocessable('Données invalides pour le calcul du taux de dépréciation. Assurez-vous que le véhicule a un genre, une énergie, une valeur neuve, une kilométrage et une date d\'expertise valides.', null);
        }

    }

     /**
     * Réorganiser les chocs
     *
     * @authenticated
     */
    public function orderShocks(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);

        $shocks = $request->get('shocks');

        if(count($shocks) > 0){
            $position = 1;
            for ($i = 0; $i < count($shocks); $i++) {
                $shock = Shock::findOrFail($shocks[$i]);
                $shock->update([
                    'position' => $position,
                ]);
                $position++;
            }
        }

        dispatch(new GenerateExpertiseReportPdfJob($assignment));

        return $this->responseSuccess('Opération effectuée avec succès', $assignment);
    }

    /**
     * Réorganiser les photos
     *
     * @authenticated
     */
    public function orderPhotos(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);

        $photos = $request->get('photos');

        if(count($photos) > 0){
            $position = 1;
            for ($i = 0; $i < count($photos); $i++) {
                $photo = Photo::findOrFail($photos[$i]);
                $photo->update([
                    'position' => $position,
                ]);
                $position++;
            }
        }

        dispatch(new GenerateExpertiseReportPdfJob($assignment));

        return $this->responseSuccess('Opération effectuée avec succès', $assignment);
    }

    /**
     * Ajouter un commentaire à un dossier
     *
     * @authenticated
     */
    public function addComment(AddCommentAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $assignment->update([
            'comment' => $request->comment,
            'updated_by' => auth()->user()->id,
        ]);

        return $this->responseSuccess('Commentaire ajouté avec succès', new AssignmentResource($assignment));
    }

    /**
     * Supprimer un dossier
     *
     * @authenticated
     */
    public function destroy(DeleteAssignmentRequest $request, $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        $assignment->update([
            'deleted_by' => auth()->user()->id,
            'deleted_at' => Carbon::now(),
            'deletion_reason' => $request->deletion_reason,
            'reference' => $assignment->reference.'-OLD',
            // 'status_id' => Status::where('code', StatusEnum::DELETED)->first()->id,
            'updated_by' => auth()->user()->id,
        ]);

        $assignment->delete();

        return $this->responseSuccess('Dossier supprimé avec succès', null);
    }

    /**
     * Restaurer un dossier
     *
     * @authenticated
     */
    public function restore(RestoreDeletedAssignmentRequest $request): JsonResponse
    {
        $assignment = Assignment::withTrashed()
                    ->where('reference', $request->reference)
                    ->first();

        if($assignment){
            $assignment->restore();
            return $this->responseSuccess('Dossier restauré avec succès', null);
        } else {
            return $this->responseUnprocessable('Dossier non trouvé', null);
        }
    }

    private function syncAssignmentRepairers(Assignment $assignment, $request): void
    {
        $repairers = $request->has('repairers')
            ? $request->repairers
            : ($request->filled('repairer_id') ? [['repairer_id' => $request->repairer_id]] : null);

        $assignment->syncRepairers($repairers, auth()->id());
    }
}
