<?php

namespace App\Jobs;

use PDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Photo;
use App\Models\Shock;
use App\Models\QrCode;
use App\Models\Status;
use App\Enums\RoleEnum;
use App\Models\Receipt;
use App\Enums\StatusEnum;
use App\Enums\ProfileEnum;
use App\Models\Assignment;
use App\Models\EntityEmail;
use Illuminate\Bus\Queueable;
use App\Models\ArticleRequest;
use App\Mail\SendWorkSheetMail;
use NumberToWords\NumberToWords;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendAssignmentActionMail;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailValidationService;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\SendAssignmentHostActionMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\SendAssignmentProviderActionMail;

class SendAssignmentActionMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $maxExceptions = 1;

    public $backoff = [20, 40, 60];

    public $timeout = 300;

    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(public Assignment $assignment)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $assignment = Assignment::with('vehicle','vehicle.brand','vehicle.vehicleModel','vehicle.color','client', 'insurer', 'additionalInsurer', 'repairers', 'client', 'assignmentType', 'expertiseType', 'generalState', 'technicalConclusion', 'documentTransmitted', 'createdBy', 'updatedBy', 'deletedBy', 'closedBy', 'cancelledBy', 'editedBy', 'realizedBy', 'referenceUpdatedBy', 'directedBy', 'workSheetEstablishedBy', 'validatedBy', 'unvalidatedBy', 'payments', 'invoices', 'openedBy', 
                            'quoteValidatedByRepairerBy', 'quoteValidatedByExpertBy', 'quoteUnvalidatedByRepairerBy', 'quoteUnvalidatedByExpertBy', 'validatedByRepairerBy', 'validatedByExpertBy', 'unvalidatedByRepairerBy', 'unvalidatedByExpertBy', 'agreementForWorkSubjectToConditionsBy','firstCorrectionValidatedBy', 'firstCorrectionUnvalidatedBy', 'secondCorrectionValidatedBy', 'secondCorrectionUnvalidatedBy', 'editionValidatedBy', 'editionUnvalidatedBy', 'cancelledBy', 'classifiedWithoutFurtherActionBy', 'paidBy', 'closedBy')
                        ->where('assignments.id', 1)
                        ->first(); 

        $file = public_path("storage/expertise_report/".$assignment->reference.".pdf");

        $user_emails = User::whereIn('current_role_id', Role::whereIn('name', [RoleEnum::ADMIN->value, RoleEnum::CEO->value, RoleEnum::EXPERT_MANAGER->value, RoleEnum::EXPERT->value])->get()->pluck('id'))->get()->pluck('email');

		// Normalise les emails en une liste de chaînes
		$userEmails = [];
		if (is_string($user_emails) && trim($user_emails) !== '') {
			$decoded = json_decode($user_emails, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				if (is_array($decoded)) {
					foreach ($decoded as $item) {
						if (is_string($item)) {
							$userEmails[] = $item;
						} elseif (is_array($item) && isset($item['email']) && is_string($item['email'])) {
							$userEmails[] = $item['email'];
						}
					}
				} elseif (is_string($decoded)) {
					$userEmails[] = $decoded;
				}
			} else {
				// fallback: liste séparée par des virgules
				$userEmails = array_filter(array_map('trim', explode(',', $user_emails)));
			}
		}

		// Vérifier si le nom de domaine des emails existe
		$emailValidationService = new EmailValidationService();
		$userEmails = $emailValidationService->validateEmails($userEmails);

        $active_status_id = Status::where('code', StatusEnum::ACTIVE)->first()->id;
        $repairer_ids = $assignment->repairers()->pluck('entities.id');
        $repairer_emails = EntityEmail::whereIn('entity_id', $repairer_ids)->where('status_id', $active_status_id)->get()->pluck('email');
        $repairerEmails = [];
		if (is_string($repairer_emails) && trim($repairer_emails) !== '') {
			$decoded = json_decode($repairer_emails, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				if (is_array($decoded)) {
					foreach ($decoded as $item) {
						if (is_string($item)) {
							$repairerEmails[] = $item;
						} elseif (is_array($item) && isset($item['email']) && is_string($item['email'])) {
							$repairerEmails[] = $item['email'];
						}
					}
				} elseif (is_string($decoded)) {
					$repairerEmails[] = $decoded;
				}
			} else {
				// fallback: liste séparée par des virgules
				$repairerEmails = array_filter(array_map('trim', explode(',', $repairer_emails)));
			}
		}
		// Vérifier si le nom de domaine des emails existe
		$emailValidationService = new EmailValidationService();
		$repairerEmails = $emailValidationService->validateEmails($repairerEmails);

        $insurer_emails = EntityEmail::where('entity_id', $assignment->insurer_id)->where('status_id', $active_status_id)->get()->pluck('email');
        $insurerEmails = [];
		if (is_string($insurer_emails) && trim($insurer_emails) !== '') {
			$decoded = json_decode($insurer_emails, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				if (is_array($decoded)) {
					foreach ($decoded as $item) {
						if (is_string($item)) {
							$insurerEmails[] = $item;
						} elseif (is_array($item) && isset($item['email']) && is_string($item['email'])) {
							$insurerEmails[] = $item['email'];
						}
					}
				} elseif (is_string($decoded)) {
					$insurerEmails[] = $decoded;
				}
			} else {
				// fallback: liste séparée par des virgules
				$insurerEmails = array_filter(array_map('trim', explode(',', $insurer_emails)));
			}
		}
		// Vérifier si le nom de domaine des emails existe
		$emailValidationService = new EmailValidationService();
		$insurerEmails = $emailValidationService->validateEmails($insurerEmails);

        try {
            if(count($userEmails) > 0){
                if($assignment->status->code == StatusEnum::PENDING_FOR_REPAIRER_QUOTE->value || $assignment->status->code == StatusEnum::PENDING_FOR_REPAIRER_QUOTE_VALIDATION->value || $assignment->status->code == StatusEnum::PENDING_FOR_REPAIRER_VALIDATION->value || $assignment->status->code == StatusEnum::VALIDATED->value){
                    // Mail::to($userEmails)->cc('brahimafane06@gmail.com')->send(new SendAssignmentHostActionMail($file,$assignment));
                    Mail::to('brahimafane06@gmail.com')->send(new SendAssignmentHostActionMail($file,$assignment));
                }
                Mail::to('brahimafane06@gmail.com')->send(new SendAssignmentHostActionMail($file,$assignment));
            }
            if(count($repairerEmails) > 0){
                if($assignment->status->code == StatusEnum::PENDING_FOR_REPAIRER_QUOTE->value || $assignment->status->code == StatusEnum::IN_EDITING->value || $assignment->status->code == StatusEnum::PENDING_FOR_REPAIRER_VALIDATION->value || $assignment->status->code == StatusEnum::VALIDATED->value){
                    Mail::to($repairerEmails)->cc('brahimafane06@gmail.com')->send(new SendAssignmentProviderActionMail($file,$assignment));
                }
                Mail::to($repairerEmails)->cc('brahimafane06@gmail.com')->send(new SendAssignmentProviderActionMail($file,$assignment));
            }
            if(count($insurerEmails) > 0){
                if($assignment->status->code == StatusEnum::PENDING_FOR_REPAIRER_QUOTE->value || $assignment->status->code == StatusEnum::IN_EDITING->value || $assignment->status->code == StatusEnum::PENDING_FOR_REPAIRER_VALIDATION->value || $assignment->status->code == StatusEnum::VALIDATED->value){
                    Mail::to($insurerEmails)->cc('brahimafane06@gmail.com')->send(new SendAssignmentInsurerActionMail($file,$assignment));
                }
                Mail::to($insurerEmails)->cc('brahimafane06@gmail.com')->send(new SendAssignmentInsurerActionMail($file,$assignment));
            }
            Mail::to('brahimafane06@gmail.com')->send(new SendAssignmentHostActionMail($file,$assignment));
            Mail::to('brahimafane06@gmail.com')->send(new SendAssignmentProviderActionMail($file,$assignment));
        } catch (\Exception $e) {
            Log::error($e);
        }
    }
}
