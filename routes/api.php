<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

// PUBLIC - tidak perlu token
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// PROTECTED - wajib Bearer Token
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',           [AuthController::class, 'logout']);
    Route::get('/me',                [AuthController::class, 'me']);

    Route::get('/events',            [EventController::class, 'index']);
    Route::post('/events',           [EventController::class, 'store']);
    Route::get('/events/{id}',       [EventController::class, 'show']);
    Route::post('/events/{id}/join', [EventController::class, 'join']);
});