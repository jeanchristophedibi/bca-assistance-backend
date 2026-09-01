<?php

use App\Http\Controllers\API\AssignmentRepairerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->name('assignment-repairers.')->group(function () {
    Route::get('/', [AssignmentRepairerController::class, 'index'])->name('assignment-repairers.index');
    Route::post('/', [AssignmentRepairerController::class, 'store'])->name('assignment-repairers.store');
    Route::get('/{assignment_repairer}', [AssignmentRepairerController::class, 'show'])->name('assignment-repairers.show');
    Route::put('/{assignment_repairer}', [AssignmentRepairerController::class, 'update'])->name('assignment-repairers.update');
    Route::delete('/{assignment_repairer}', [AssignmentRepairerController::class, 'destroy'])->name('assignment-repairers.destroy');
});
