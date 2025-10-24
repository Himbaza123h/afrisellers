<?php

use App\Http\Controllers\Frontend\VendorController;
use Illuminate\Support\Facades\Route;




Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/register', [VendorController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [VendorController::class, 'register'])->name('register.submit');
});
