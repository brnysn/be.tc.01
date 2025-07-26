<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Public authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected authentication routes
Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {
    Route::post('/logout', 'logout');
    Route::post('/logout-all', 'logoutAll');
    Route::get('/profile', 'profile');
    Route::put('/profile', 'updateProfile');

});
