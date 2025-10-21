<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search/{type}/{slug}', [ProductController::class, 'search'])->name('products.search');
