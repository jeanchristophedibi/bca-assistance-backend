<?php

use App\Http\Controllers\API\EntityEmailController;
use Illuminate\Support\Facades\Route;

/**
 * EntityEmails
 */
Route::middleware(['auth:sanctum'])->name('entity-emails.')->group(function () {
    Route::controller(EntityEmailController::class)->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('{id}', 'show')->name('show');
        Route::put('{id}', 'update')->name('update');
        Route::put('{id}/enable', 'enable')->name('enable');
        Route::put('{id}/disable', 'disable')->name('disable');
        Route::delete('{id}', 'destroy')->name('destroy');
    });
});
