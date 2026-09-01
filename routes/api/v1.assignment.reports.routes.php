<?php

use App\Http\Controllers\API\AssignmentReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->name('assignment-reports.')->group(function () {
    Route::get('/', [AssignmentReportController::class, 'index'])->name('assignment-reports.index');
    Route::post('/', [AssignmentReportController::class, 'store'])->name('assignment-reports.store');
    Route::get('/{assignment_report}', [AssignmentReportController::class, 'show'])->name('assignment-reports.show');
    Route::put('/{assignment_report}', [AssignmentReportController::class, 'update'])->name('assignment-reports.update');
    Route::delete('/{assignment_report}', [AssignmentReportController::class, 'destroy'])->name('assignment-reports.destroy');
}); 