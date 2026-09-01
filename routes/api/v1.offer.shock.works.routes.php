<?php

use App\Http\Controllers\API\OfferShockWorkController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->name('offer-shock-works.')->group(function () {
    Route::get('/', [OfferShockWorkController::class, 'index'])->name('offer-shock-works.index');
    Route::post('/', [OfferShockWorkController::class, 'store'])->name('offer-shock-works.store');
    Route::post('/calculate', [OfferShockWorkController::class, 'calculate'])->name('offer-shock-works.calculate');
    Route::get('/{offer_shock_work}', [OfferShockWorkController::class, 'show'])->name('offer-shock-works.show');
    Route::put('/{offer_shock_work}', [OfferShockWorkController::class, 'update'])->name('offer-shock-works.update');
    Route::delete('/{offer_shock_work}', [OfferShockWorkController::class, 'destroy'])->name('offer-shock-works.destroy');
});
