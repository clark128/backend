<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApplicationRequestController;
use App\Http\Controllers\MotorcycleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/motorcycles', [MotorcycleController::class, 'index']);
Route::get('/motorcycles/{motorcycle}', [MotorcycleController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // User management routes
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    // Application Request routes
    Route::get('/application-requests', [ApplicationRequestController::class, 'index']);
    Route::post('/application-requests', [ApplicationRequestController::class, 'store']);
    Route::put('/application-requests/{id}/status', [ApplicationRequestController::class, 'updateStatus']);
    Route::get('/user/application-requests', [ApplicationRequestController::class, 'userApplications']);
    Route::put('/application-requests/{id}/personal-address', [ApplicationRequestController::class, 'updatePersonalAddress']);
    Route::put('/application-requests/{id}/personal-family', [ApplicationRequestController::class, 'updatePersonalFamily']);
    Route::put('/application-requests/{id}/parental-credit', [ApplicationRequestController::class, 'updateParentalCredit']);
    Route::put('/application-requests/{id}/employment-payment', [ApplicationRequestController::class, 'updateEmploymentPaymentDetails']);
    Route::put('/application-requests/{id}/co-maker', [ApplicationRequestController::class, 'updateCoMakerDetails']);

    // Admin routes
    Route::middleware(['auth:sanctum', 'abilities:admin'])->group(function () {
        Route::put('/users/{id}', [AuthController::class, 'updateUser']);
        Route::post('/motorcycles', [MotorcycleController::class, 'store']);
        Route::put('/motorcycles/{motorcycle}', [MotorcycleController::class, 'update']);
        Route::patch('/motorcycles/{motorcycle}', [MotorcycleController::class, 'update']);
        Route::delete('/motorcycles/{motorcycle}', [MotorcycleController::class, 'destroy']);
    });

    // User routes
    Route::middleware(['auth:sanctum', 'abilities:user'])->group(function () {
        // Add user-specific routes here
    });

    // Password change route
    Route::post('/change-password', [UserController::class, 'changePassword']);
});
