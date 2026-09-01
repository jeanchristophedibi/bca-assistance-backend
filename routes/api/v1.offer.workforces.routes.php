<?php

use App\Http\Controllers\API\OfferWorkforceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->name('offer-workforces.')->group(function () {
    Route::get('/', [OfferWorkforceController::class, 'index'])->name('offer-workforces.index');
    Route::post('/', [OfferWorkforceController::class, 'store'])->name('offer-workforces.store');
    Route::post('/calculate', [OfferWorkforceController::class, 'calculate'])->name('offer-workforces.calculate');
    Route::get('/{offer_workforce}', [OfferWorkforceController::class, 'show'])->name('offer-workforces.show');
    Route::put('/{offer_workforce}', [OfferWorkforceController::class, 'update'])->name('offer-workforces.update');
    Route::delete('/{offer_workforce}', [OfferWorkforceController::class, 'destroy'])->name('offer-workforces.destroy');
});
