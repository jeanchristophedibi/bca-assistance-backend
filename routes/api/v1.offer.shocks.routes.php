<?php

use App\Http\Controllers\API\OfferShockController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->name('offer-shocks.')->group(function () {
    Route::get('/', [OfferShockController::class, 'index'])->name('offer-shocks.index');
    Route::post('/', [OfferShockController::class, 'store'])->name('offer-shocks.store');
    Route::post('/store/point', [OfferShockController::class, 'storePoint'])->name('offer-shocks.store-point');
    Route::get('/{offer_shock}', [OfferShockController::class, 'show'])->name('offer-shocks.show');
    Route::put('/{offer_shock}', [OfferShockController::class, 'update'])->name('offer-shocks.update');
    Route::delete('/{offer_shock}', [OfferShockController::class, 'destroy'])->name('offer-shocks.destroy');
    Route::put('/{offer_shock}/order-shock-works', [OfferShockController::class, 'orderShockWorks'])->name('offer-shocks.order-shock-works');
    Route::put('/{offer_shock}/order-workforces', [OfferShockController::class, 'orderWorkforces'])->name('offer-shocks.order-workforces');
});
