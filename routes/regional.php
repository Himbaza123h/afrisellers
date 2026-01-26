<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Regional\DashboardController;
use App\Http\Controllers\Regional\VendorController;
use App\Http\Controllers\Regional\ProductController;
use App\Http\Controllers\Regional\ShowroomController;
use App\Http\Controllers\Regional\OrderController;
use App\Http\Controllers\Regional\LoadController;
use App\Http\Controllers\Regional\TransporterController;
use App\Http\Controllers\Regional\CountryAdminController;
use App\Http\Controllers\Regional\ReportController;

Route::prefix('regional')->name('regional.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.home');

    // Vendors Management
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('index');
            Route::get('/print', [VendorController::class, 'print'])->name('print'); // ADD THIS LINE
        Route::get('/{id}', [VendorController::class, 'show'])->name('show');
        Route::post('/{id}/verify', [VendorController::class, 'verify'])->name('verify');
        Route::post('/{id}/suspend', [VendorController::class, 'suspend'])->name('suspend');
        Route::post('/{id}/activate', [VendorController::class, 'activate'])->name('activate');
    });

    // Products Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/print', [ProductController::class, 'print'])->name('print'); // ADD THIS LINE

        Route::get('/{id}', [ProductController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [ProductController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [ProductController::class, 'reject'])->name('reject');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // Showrooms Management
    Route::prefix('showrooms')->name('showrooms.')->group(function () {
        Route::get('/', [ShowroomController::class, 'index'])->name('index');
            Route::get('/print', [ShowroomController::class, 'print'])->name('print'); // ADD THIS LINE

        Route::get('/{id}', [ShowroomController::class, 'show'])->name('show');
        Route::post('/{id}/verify', [ShowroomController::class, 'verify'])->name('verify');
        Route::post('/{id}/feature', [ShowroomController::class, 'feature'])->name('feature');
        Route::delete('/{id}', [ShowroomController::class, 'destroy'])->name('destroy');
    });

    // Orders Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
                Route::get('/print', [OrderController::class, 'print'])->name('print'); // NEW PRINT ROUTE

        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
    });

    // Loads Management
    Route::prefix('loads')->name('loads.')->group(function () {
        Route::get('/', [LoadController::class, 'index'])->name('index');
            Route::get('/print', [LoadController::class, 'print'])->name('print'); // ADD THIS LINE

        Route::get('/{id}', [LoadController::class, 'show'])->name('show');
    });

    // Transporters Management
    Route::prefix('transporters')->name('transporters.')->group(function () {
        Route::get('/', [TransporterController::class, 'index'])->name('index');
            Route::get('/print', [TransporterController::class, 'print'])->name('print'); // ADD THIS

        Route::get('/{id}', [TransporterController::class, 'show'])->name('show');
        Route::post('/{id}/verify', [TransporterController::class, 'verify'])->name('verify');
    });

    // Country Admins Management
    Route::prefix('country-admins')->name('country-admins.')->group(function () {
        Route::get('/', [CountryAdminController::class, 'index'])->name('index');
        Route::get('/print', [CountryAdminController::class, 'print'])->name('print');
        Route::get('/create', [CountryAdminController::class, 'create'])->name('create');
        Route::post('/', [CountryAdminController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CountryAdminController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CountryAdminController::class, 'update'])->name('update');
        Route::delete('/{id}', [CountryAdminController::class, 'destroy'])->name('destroy');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/print', [ReportController::class, 'print'])->name('print');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });
});
