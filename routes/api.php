<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Public authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public product routes (read-only)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Protected authentication routes
Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {
    Route::post('/logout', 'logout');
    Route::post('/logout-all', 'logoutAll');
    Route::get('/profile', 'profile');
    Route::put('/profile', 'updateProfile');
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    // Authenticated Admin routes
    Route::middleware('admin')->group(function () {

        // Product routes
        Route::controller(ProductController::class)->group(function () {
            // Protected product routes (require authentication)
            Route::post('/products', 'store');
            Route::put('/products/{product}', 'update');
            Route::delete('/products/{product}', 'destroy');

            // Soft delete management routes
            Route::get('/products/trashed/list', 'trashed');
            Route::post('/products/{id}/restore', 'restore');
            Route::delete('/products/{id}/force-delete', 'forceDelete');
        });
    });

    // Authenticated Customer routes
    Route::middleware('customer')->group(function () {
        // Customer routes
    });
});
