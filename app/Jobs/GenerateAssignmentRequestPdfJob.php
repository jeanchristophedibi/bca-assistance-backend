<?php

namespace App\Jobs;

use PDF;
use App\Models\AssignmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class GenerateAssignmentRequestPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $maxExceptions = 1;

    public $backoff = [20, 40, 60];

    public $timeout = 300;

    public $deleteWhenMissingModels = true;

    public function __construct(public AssignmentRequest $_assignmentRequest)
    {
        //
    }

    public function handle(): void
    {
        $assignmentRequest = AssignmentRequest::with([
            'client',
            'vehicle.brand',
            'vehicle.vehicleModel',
            'vehicle.color',
            'vehicle.vehicleEnergy',
            'repairers',
            'status',
            'photos',
            'createdBy',
        ])->findOrFail($this->_assignmentRequest->id);

        $logoPath = base_path('public/images/logo_eg.jpg');
        $logo = null;
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logo = 'data:image/'.$type.';base64,'.base64_encode(file_get_contents($logoPath));
        }

        $directory = public_path('storage/assignment_request');
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $pdf = PDF::loadView('assignment_request.index', compact('assignmentRequest', 'logo'));
        $pdf->set_option('isHtml5ParserEnabled', false);
        $pdf->set_option('isRemoteEnabled', true);
        $pdf->setOptions(['defaultFont' => 'sans-serif']);
        $pdf->save($directory.'/'.$assignmentRequest->reference.'.pdf');
    }
}
