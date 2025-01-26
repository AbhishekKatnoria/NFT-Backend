<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckBearerToken;
use App\Http\Controllers\Api\{
    UserController,
    CompanyController,
    SuperAdminController,
};

// Super Admin Routes
Route::controller(SuperAdminController::class)->group(function () {
    Route::post('create-super-admin', 'createSuperAdmin');
});

// Users Routes
Route::controller(UserController::class)->group(function () {
    Route::post('login', 'loginUser');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout', [UserController::class, 'userLogoutRoute']);
});

// Company Routes
Route::middleware(CheckBearerToken::class)->group(function () {
    
    Route::get('profile', [UserController::class, 'userProfileRoute']);
    Route::controller(CompanyController::class)->group(function () {
        Route::post('create-company', 'routeCreateCompany');
    });
});
