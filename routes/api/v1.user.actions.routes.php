<?php

use App\Http\Controllers\API\UserActionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->name('user-actions.')->group(function () {
    Route::get('/', [UserActionController::class, 'index'])->name('index');
    Route::post('/', [UserActionController::class, 'store'])->name('store');
    Route::get('/{userAction}', [UserActionController::class, 'show'])->name('show');
    Route::put('/{userAction}', [UserActionController::class, 'update'])->name('update');
    Route::delete('/{userAction}', [UserActionController::class, 'destroy'])->name('destroy');
});
