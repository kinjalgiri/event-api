<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\EventController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Event routes
    Route::apiResource('events', EventController::class);
    Route::get('events/{event}/bookings', [EventController::class, 'bookings']);
});

// Attendee routes
Route::apiResource('attendees', AttendeeController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);

// Booking routes
Route::apiResource('bookings', BookingController::class)
    ->only(['index', 'store', 'show']);
