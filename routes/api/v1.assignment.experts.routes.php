<?php

use App\Http\Controllers\API\AssignmentExpertController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->name('assignment-experts.')->group(function () {
    Route::get('/', [AssignmentExpertController::class, 'index'])->name('assignment-experts.index');
    Route::post('/', [AssignmentExpertController::class, 'store'])->name('assignment-experts.store');
    Route::get('/{assignment_expert}', [AssignmentExpertController::class, 'show'])->name('assignment-experts.show');
    Route::put('/{assignment_expert}', [AssignmentExpertController::class, 'update'])->name('assignment-experts.update');
    Route::delete('/{assignment_expert}', [AssignmentExpertController::class, 'destroy'])->name('assignment-experts.destroy');
}); 