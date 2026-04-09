<?php

use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('/health', HealthController::class);

// Public endpoints
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);

Route::get('/tags', [TagController::class, 'index']);

Route::post('/tracking/analytics', [\App\Http\Controllers\Api\TrackingController::class, 'logAnalytics']);
Route::post('/tracking/subscribe', [\App\Http\Controllers\Api\TrackingController::class, 'subscribePush']);
Route::post('/tracking/email-subscribe', [\App\Http\Controllers\Api\TrackingController::class, 'subscribeEmail']);

// Auth
Route::post('/auth', [AuthController::class, 'login'])->middleware('throttle:30,1');
Route::put('/auth', [AuthController::class, 'refresh']);
Route::delete('/auth', [AuthController::class, 'logout']);

// Protected endpoints
Route::post('/posts', [PostController::class, 'store']);
Route::put('/posts/{id}', [PostController::class, 'update']);
Route::delete('/posts/{id}', [PostController::class, 'destroy']);

Route::post('/tags', [TagController::class, 'store']);
Route::get('/media', [MediaController::class, 'index']);
Route::post('/media', [MediaController::class, 'store']);
