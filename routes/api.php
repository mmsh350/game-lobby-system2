<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/current-session', [GameController::class, 'getCurrentSession']);
    Route::post('/join-session', [GameController::class, 'joinSession']);
    Route::get('/leaderboard', [GameController::class, 'getLeaderboard']);
});
