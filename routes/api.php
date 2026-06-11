<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', /**
 * Welcome to the Image Store API.
 */
    function () {
        return response()->json([
            'message' => 'Welcome to the Image Store API. Please login to use this service.',
        ]);
    });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Upload a new image
    Route::post('/images', [ImageController::class, 'store']);
    // List all images for the authenticated user
    Route::get('/images', [ImageController::class, 'index']);
    // Get a specific image's metadata or details
    Route::get('/images/{image}', [ImageController::class, 'show']);
    // Delete a specific image
    Route::delete('/images/{image}', [ImageController::class, 'destroy']);
});
