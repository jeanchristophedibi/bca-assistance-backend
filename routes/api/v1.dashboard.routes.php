<?php

use App\Http\Controllers\API\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->name('dashboard.')->group(function () {
    Route::get('/users', [DashboardController::class, 'users'])->name('users');
    Route::get('/assignments', [DashboardController::class, 'assignments'])->name('assignments');
    Route::get('/insurers', [DashboardController::class, 'insurers'])->name('insurers');
    // Route::get('/brokers', [DashboardController::class, 'brokers'])->name('brokers');
    Route::get('/repairers', [DashboardController::class, 'repairers'])->name('repairers');
    Route::get('/vehicles', [DashboardController::class, 'vehicles'])->name('vehicles');
    Route::get('/assignments-by-assignment-type', [DashboardController::class, 'assignmentsByAssignmentType'])->name('assignments-by-assignment-type');
    Route::get('/assignments-by-insurer', [DashboardController::class, 'assignmentsByInsurer'])->name('assignments-by-insurer');
    Route::get('/assignments-by-repairer', [DashboardController::class, 'assignmentsByRepairer'])->name('assignments-by-repairer');
    Route::get('/assignments-by-month-year-and-type', [DashboardController::class, 'assignmentsByMonthYearAndType'])->name('assignments-by-month-year-and-type');
    Route::get('/assignments-by-month-year-and-status', [DashboardController::class, 'assignmentsByMonthYearAndStatus'])->name('assignments-by-month-year-and-status');
});
