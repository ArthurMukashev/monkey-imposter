<?php

use App\Http\Controllers\Api\V1\{CategoryController,
    CityController,
    CuisineTypeController,
    HealthController,
    PlaceController,
    PromoController,
    TagController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::get('places', [PlaceController::class, 'index']);
    Route::get('places/{slug}', [PlaceController::class, 'show']);
    Route::get('cities', [CityController::class, 'index']);
    Route::get('tags', [TagController::class, 'index']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('cuisine-types', [CuisineTypeController::class, 'index']);
    Route::get('promos', [PromoController::class, 'index']);
    Route::get('health', [HealthController::class, 'index']);
});
