<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SightseeingController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ============================================================
// PUBLIC ROUTES (No authentication required)
// ============================================================

// Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'version' => '2.0',
        'endpoints' => 25,
        'api' => 'IdeaB2B Travel Platform API'
    ]);
});

Route::get('/info', function () {
    return response()->json([
        'name' => 'IdeaB2B Travel Platform',
        'version' => '1.0.0',
        'endpoints' => 25,
        'auth' => 'Token-based (Sanctum)',
        'description' => 'Complete REST API for travel itinerary management'
    ]);
});

// Authentication Routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Public content (destinations, cities, hotels for browsing)
Route::get('/destinations', [DestinationController::class, 'index']);
Route::get('/destinations/{id}', [DestinationController::class, 'show']);
Route::get('/cities', [CityController::class, 'index']);
Route::get('/cities/{id}', [CityController::class, 'show']);
Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels/{id}', [HotelController::class, 'show']);
Route::get('/sightseeings', [SightseeingController::class, 'index']);
Route::get('/sightseeings/{id}', [SightseeingController::class, 'show']);

// ============================================================
// PROTECTED ROUTES (Authentication required)
// ============================================================

Route::middleware('auth:sanctum')->group(function () {
    
    // ========== AUTHENTICATION ==========
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    
    // ========== USER MANAGEMENT ==========
    Route::apiResource('users', UserController::class);
    Route::get('users/role/{role}', [UserController::class, 'getByRole']);
    Route::put('users/{id}/status', [UserController::class, 'updateStatus']);
    
    // ========== DESTINATIONS ==========
    Route::post('/destinations', [DestinationController::class, 'store']);
    Route::put('/destinations/{id}', [DestinationController::class, 'update']);
    Route::delete('/destinations/{id}', [DestinationController::class, 'destroy']);
    
    // ========== CITIES ==========
    Route::post('/cities', [CityController::class, 'store']);
    Route::put('/cities/{id}', [CityController::class, 'update']);
    Route::delete('/cities/{id}', [CityController::class, 'destroy']);
    Route::get('destinations/{destination}/cities', [CityController::class, 'byDestination']);
    
    // ========== SIGHTSEEINGS ==========
    Route::post('/sightseeings', [SightseeingController::class, 'store']);
    Route::put('/sightseeings/{id}', [SightseeingController::class, 'update']);
    Route::delete('/sightseeings/{id}', [SightseeingController::class, 'destroy']);
    Route::get('cities/{city}/sightseeings', [SightseeingController::class, 'byCity']);
    
    // ========== HOTELS ==========
    Route::post('/hotels', [HotelController::class, 'store']);
    Route::put('/hotels/{id}', [HotelController::class, 'update']);
    Route::delete('/hotels/{id}', [HotelController::class, 'destroy']);
    Route::get('cities/{city}/hotels', [HotelController::class, 'byCity']);
    
    // ========== ITINERARIES ==========
    Route::apiResource('itineraries', ItineraryController::class);
    Route::post('itineraries/{itinerary}/items', [ItineraryController::class, 'addItem']);
    Route::delete('itineraries/{itinerary}/items/{item}', [ItineraryController::class, 'removeItem']);
    Route::post('itineraries/{itinerary}/assign-operator', [ItineraryController::class, 'assignOperator']);
    
    // ========== BOOKINGS ==========
    Route::apiResource('bookings', BookingController::class);
    Route::get('bookings/status/{status}', [BookingController::class, 'getByStatus']);
    Route::put('bookings/{id}/confirm', [BookingController::class, 'confirm']);
    Route::put('bookings/{id}/cancel', [BookingController::class, 'cancel']);
    Route::get('bookings/statistics', [BookingController::class, 'statistics']);
    
    // ========== SEARCH ==========
    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/search/destinations', [SearchController::class, 'searchDestinations']);
    Route::get('/search/cities', [SearchController::class, 'searchCities']);
    Route::get('/search/hotels', [SearchController::class, 'searchHotels']);
    Route::get('/search/sightseeings', [SearchController::class, 'searchSightseeings']);
    
    // ========== STATISTICS ==========
    Route::get('/statistics', [StatisticsController::class, 'index']);
    Route::get('/statistics/summary', [StatisticsController::class, 'summary']);
    Route::get('/statistics/users', [StatisticsController::class, 'userStats']);
    Route::get('/statistics/bookings', [StatisticsController::class, 'bookingStats']);
    Route::get('/statistics/destinations', [StatisticsController::class, 'destinationStats']);
