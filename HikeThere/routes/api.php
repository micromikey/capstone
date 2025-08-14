<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TrailController;
use App\Http\Controllers\Api\LocationController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('trails')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [TrailController::class, 'index']);
        Route::get('/{trail}', [TrailController::class, 'show']);
    });
});

Route::prefix('locations')->group(function () {
    // Public route for location search (needed for trail creation form)
    Route::get('/search', [LocationController::class, 'search']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [LocationController::class, 'index']);
        Route::get('/{id}', [LocationController::class, 'show']);
    });
});
