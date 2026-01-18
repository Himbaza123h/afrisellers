<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agent\DashboardController;
use App\Http\Controllers\Agent\ReferralController;
use App\Http\Controllers\Agent\CommissionController;

Route::prefix('agent')->name('agent.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.home');

    // Agents can view and manage their referrals
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [ReferralController::class, 'index'])->name('index');
        Route::get('/create', [ReferralController::class, 'create'])->name('create');
        Route::post('/', [ReferralController::class, 'store'])->name('store');
    });

    // Agent commissions
    Route::prefix('commissions')->name('commissions.')->group(function () {
        Route::get('/', [CommissionController::class, 'index'])->name('index');
    });
});
