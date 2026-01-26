<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\LoginController;
use App\Http\Controllers\Frontend\SocialAuthController;
use App\Http\Controllers\ProfileController;

Route::get('/auth/signin', [LoginController::class, 'index'])->name('auth.signin');
Route::post('/auth/signin', [LoginController::class, 'login'])->name('auth.signin.submit');
Route::post('/auth/logout', [LoginController::class, 'logout'])->name('auth.logout');

Route::get('/auth/register', [LoginController::class, 'register'])->name('auth.register');

// Social Authentication Routes
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);


// Token-based Dashboard Login Routes
Route::get('/auth/regional/token-login/{token}', [LoginController::class, 'regionalTokenLogin'])
    ->name('auth.regional.token-login');

Route::get('/auth/country/token-login/{token}', [LoginController::class, 'countryTokenLogin'])
    ->name('auth.country.token-login');

Route::get('/auth/agent/token-login/{token}', [LoginController::class, 'agentTokenLogin'])
    ->name('auth.agent.token-login');

Route::get('/auth/vendor/token-login/{token}', [LoginController::class, 'vendorTokenLogin'])
    ->name('auth.vendor.token-login');

Route::get('/auth/buyer/token-login/{token}', [LoginController::class, 'buyerTokenLogin'])
    ->name('auth.buyer.token-login');

// Profile Routes (Authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});
