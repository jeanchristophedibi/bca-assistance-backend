<?php

namespace App\Mail;

use App\Models\Status;
use App\Enums\StatusEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\AssignmentType;
use App\Enums\AssignmentTypeEnum;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class SendAssignmentActionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $file;
    public $assignment;
    

    public function __construct($file,$assignment)
    {
        $this->file = $file;
        $this->assignment = $assignment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->assignment->status_id == Status::where('code', StatusEnum::VALIDATED)->first()->id && $this->assignment->assignment_type_id == AssignmentType::where('code', AssignmentTypeEnum::INSURER)->first()->id){
            $object = "Rapport d'expertise du véhicule immmatriculé ".$this->assignment->vehicle->license_plate." - Expert 3D";
                        return $this
                        ->subject($object)
                        ->attach(
                            $this->file
                        )
                        ->markdown('email.assignment.action');
        } else {
            $object = "Rapport d'expertise du véhicule immmatriculé ".$this->assignment->vehicle->license_plate." - Expert 3D";
                        return $this
                        ->subject($object)
                        ->markdown('email.assignment.action');
        }
    }
}
