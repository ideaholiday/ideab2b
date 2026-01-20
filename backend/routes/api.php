<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\SightseeingController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ItineraryController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Destinations
    Route::apiResource('destinations', DestinationController::class);

    // Cities
    Route::apiResource('cities', CityController::class);
    Route::get('destinations/{destination}/cities', [CityController::class, 'byDestination']);

    // Sightseeing & Transfers
    Route::apiResource('sightseeings', SightseeingController::class);
    Route::get('cities/{city}/sightseeings', [SightseeingController::class, 'byCity']);

    // Hotels
    Route::apiResource('hotels', HotelController::class);
    Route::get('cities/{city}/hotels', [HotelController::class, 'byCity']);

    // Itineraries
    Route::apiResource('itineraries', ItineraryController::class);
    Route::post('itineraries/{itinerary}/items', [ItineraryController::class, 'addItem']);
    Route::delete('itineraries/{itinerary}/items/{item}', [ItineraryController::class, 'removeItem']);
    Route::post('itineraries/{itinerary}/assign-operator', [ItineraryController::class, 'assignOperator']);
});
