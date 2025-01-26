<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckBearerToken;
use App\Http\Controllers\Api\{
    UserController,
    CustomerController,
    SuperAdminController,
};

/**
 * Super Admin Routes
 * These routes are for creating and managing super admin users.
 */
Route::controller(SuperAdminController::class)->group(function () {
    Route::post('create-super-admin', 'createSuperAdmin');  // Create a new super admin
});

/**
 * User Authentication Routes
 * These routes handle user login, logout, and authentication-related operations.
 */
Route::controller(UserController::class)->group(function () {
    Route::post('login', 'loginUser');  // Login user
});

// Route for logging out the authenticated user, protected by Sanctum authentication.
Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout', [UserController::class, 'userLogoutRoute']);  // Logout the user
});

/**
 * Customer Routes
 * These routes manage customer-related functionalities.
 */
Route::controller(CustomerController::class)->group(function () {
    Route::post('create-customer', 'callCustomer');  // Create a new customer
});

/**
 * User Profile Route
 * This route allows access to the user's profile, protected by a custom Bearer Token middleware.
 */
Route::middleware(CheckBearerToken::class)->group(function () {
    Route::get('profile', [UserController::class, 'userProfileRoute']);  // Get the authenticated user's profile
});
