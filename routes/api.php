<?php

use App\Http\Controllers\Api\ShowAndEventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('shows')->group(function () {

    Route::get('/', [ShowAndEventController::class, 'index']);

    Route::get('/{showId}', [ShowAndEventController::class, 'show'])
        ->where('showId', '[0-9]+');

    Route::get('/{showId}/events/{eventId}/places', [ShowAndEventController::class, 'seatLayout'])
        ->where(['showId' => '[0-9]+', 'eventId' => '[0-9]+']);

    Route::post('/{showId}/events/{eventId}/reserve', [ShowAndEventController::class, 'reserve'])
        ->where(['showId' => '[0-9]+', 'eventId' => '[0-9]+']);

});