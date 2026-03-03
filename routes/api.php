<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelRequestController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::patch('travel-requests/{travel_request}/status', [TravelRequestController::class, 'updateStatus']);
    Route::apiResource('travel-requests', TravelRequestController::class);
});
