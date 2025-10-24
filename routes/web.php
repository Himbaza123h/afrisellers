<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use Illuminate\Support\Facades\Artisan;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search/{type}/{slug}', [ProductController::class, 'search'])->name('products.search');


Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/livestream', [HomeController::class, 'livestream'])->name('livestream');

Route::get('/clear-all', function() {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return "All caches cleared!";
});


require __DIR__.'/vendor.php';





