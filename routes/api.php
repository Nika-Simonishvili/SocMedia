<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\User\UserSettingsController;
use Illuminate\Support\Facades\Route;

// Auth
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Email Verification
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'markAsVerified'])
            ->middleware('signed')->name('verification.verify');
        Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
            ->middleware('throttle:6,1')->name('verification.send');
    });
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::put('settings', [UserSettingsController::class, 'updateChannels']);
    });
});
