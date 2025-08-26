<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // REGISTER
    Route::post('register', [RegisteredUserController::class, 'store']);

    // LOGIN pakai API
    Route::post('login', [AuthenticatedSessionController::class, 'loginApi']);

    // LUPA PASSWORD
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth:sanctum')->group(function () {
    // VERIFIKASI EMAIL
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // KONFIRMASI PASSWORD
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // UPDATE PASSWORD
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // LOGOUT pakai API
    Route::post('logout', [AuthenticatedSessionController::class, 'logoutApi'])
        ->name('logout');
});
