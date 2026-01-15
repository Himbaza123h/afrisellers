<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\LoginController;
use App\Http\Controllers\ProfileController;

Route::get('/auth/signin', [LoginController::class, 'index'])->name('auth.signin');
Route::post('/auth/signin', [LoginController::class, 'login'])->name('auth.signin.submit');
Route::post('/auth/logout', [LoginController::class, 'logout'])->name('auth.logout');

Route::get('/auth/register', [LoginController::class, 'register'])->name('auth.register');

// Profile Routes (Authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});
