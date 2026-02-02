<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agent\DashboardController;
use App\Http\Controllers\Agent\ReferralController;
use App\Http\Controllers\Agent\CommissionController;
use App\Http\Controllers\Agent\PackageController;

Route::prefix('agent')->name('agent.')->middleware(['auth'])->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.home');
    Route::get('/dashboard/print', [DashboardController::class, 'print'])->name('dashboard.print');

    // Referral routes
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [ReferralController::class, 'index'])->name('index');
        Route::get('/create', [ReferralController::class, 'create'])->name('create');
        Route::post('/', [ReferralController::class, 'store'])->name('store');
        Route::get('/{id}', [ReferralController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ReferralController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ReferralController::class, 'update'])->name('update');
        Route::delete('/{id}', [ReferralController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/status', [ReferralController::class, 'updateStatus'])->name('update-status');
        Route::get('/print/report', [ReferralController::class, 'print'])->name('print');
    });

    // Commission routes
    Route::prefix('commissions')->name('commissions.')->group(function () {
        Route::get('/', [CommissionController::class, 'index'])->name('index');
        Route::get('/{id}', [CommissionController::class, 'show'])->name('show');
        Route::get('/print/report', [CommissionController::class, 'print'])->name('print');
    });


        Route::prefix('packages')->name('packages.')->group(function () {
        Route::get('/', [PackageController::class, 'index'])->name('index');
        Route::get('/{id}', [PackageController::class, 'show'])->name('show');
        Route::get('/{id}/checkout', [PackageController::class, 'checkout'])->name('checkout');
        Route::post('/{id}/subscribe', [PackageController::class, 'subscribe'])->name('subscribe');
        Route::get('/subscription/current', [PackageController::class, 'current'])->name('current');
        Route::post('/subscription/cancel', [PackageController::class, 'cancel'])->name('cancel');
        Route::post('/subscription/renew', [PackageController::class, 'renew'])->name('renew');
        Route::get('/subscription/history', [PackageController::class, 'history'])->name('history');
        Route::get('/subscription/print', [PackageController::class, 'print'])->name('print');
    });

});
