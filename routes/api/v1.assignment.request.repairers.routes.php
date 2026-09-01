<?php

use App\Http\Controllers\API\AssignmentRequestRepairerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->name('assignment-request-repairers.')->group(function () {
    Route::get('/', [AssignmentRequestRepairerController::class, 'index'])->name('assignment-request-repairers.index');
    Route::post('/', [AssignmentRequestRepairerController::class, 'store'])->name('assignment-request-repairers.store');
    Route::get('/{assignment_request_repairer}', [AssignmentRequestRepairerController::class, 'show'])->name('assignment-request-repairers.show');
    Route::put('/{assignment_request_repairer}', [AssignmentRequestRepairerController::class, 'update'])->name('assignment-request-repairers.update');
    Route::delete('/{assignment_request_repairer}', [AssignmentRequestRepairerController::class, 'destroy'])->name('assignment-request-repairers.destroy');
});
