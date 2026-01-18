<?php

use App\Http\Controllers\Frontend\CountryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\RegionController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\RFQController;
use Illuminate\Support\Facades\Artisan;

// Language switcher route (no locale prefix)
Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr', 'sw'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');

// Define routes function with optional name prefix
$routes = function ($namePrefix = '') {
    Route::get('/', [HomeController::class, 'index'])->name($namePrefix . 'home');
    Route::get('/search/{type}/{slug}', [ProductController::class, 'search'])->name($namePrefix . 'products.search');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name($namePrefix . 'products.show');
    Route::post('/products/review', [ProductController::class, 'storeReview'])->name($namePrefix . 'products.review');
    Route::get('/livestream', [HomeController::class, 'livestream'])->name($namePrefix . 'livestream');

    Route::post('/products/{product}/track-click', [ProductController::class, 'trackClick'])->name('products.track-click');
    Route::post('/products/{product}/track-impression', [ProductController::class, 'trackImpression'])->name('products.track-impression');

    Route::get('/search', [HomeController::class, 'globalSearch'])->name($namePrefix . 'global.search');
    Route::get('/search/suggestions', [HomeController::class, 'searchSuggestions'])->name($namePrefix . 'search.suggestions');

Route::get('/business-profile/{businessProfileId}/{name?}', [HomeController::class, 'showBusinessProfile'])->name($namePrefix . 'business-profile.show');



Route::get('/request-quote/{businessProfileId}/{productId?}', [HomeController::class, 'showRequestQuote'])
    ->name($namePrefix . 'request-quote.show');

Route::post('/request-quote/submit', [HomeController::class, 'submitRequestQuote'])
    ->name($namePrefix . 'request-quote.submit');


    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

    Route::post('/cart/shipping', [CartController::class, 'updateShipping'])->name('cart.shipping');






        // Region Routes
    Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');
    Route::get('/regions/{region}/countries', [RegionController::class, 'countries'])->name('regions.countries');

    // Country Business Profiles Route
    Route::get('/countries/{country}/business-profiles', [CountryController::class, 'businessProfiles'])->name('country.business-profiles');



    // Country Products grouped by supplier
    Route::get('/countries/{country}/products', [CountryController::class, 'products'])->name($namePrefix . 'country.products');

    // Business Profile Products
    Route::get('/business-profile/{businessProfileId}/products', [HomeController::class, 'businessProfileProducts'])->name($namePrefix . 'business-profile.products');


    // Featured Suppliers
    Route::get('/featured-suppliers', [HomeController::class, 'featuredSuppliers'])->name($namePrefix . 'featured-suppliers');

    // All Countries
    Route::get('/countries', [HomeController::class, 'allCountries'])->name($namePrefix . 'countries');

    // RFQ Routes
    Route::get('/rfqs/create', [RFQController::class, 'create'])->name($namePrefix . 'rfqs.create');
    Route::post('/rfqs', [RFQController::class, 'store'])->name($namePrefix . 'rfqs.store');
    Route::post('/rfqs/upload-image', [RFQController::class, 'uploadImage'])->name($namePrefix . 'rfqs.upload-image');
    Route::post('/rfqs/upload-file', [RFQController::class, 'uploadFile'])->name($namePrefix . 'rfqs.upload-file');

    require __DIR__.'/vendor.php';
    require __DIR__.'/auth.php';
    require __DIR__.'/buyer.php';
    require __DIR__.'/admin.php';
    require __DIR__.'/agent.php';
    require __DIR__.'/country.php';
    require __DIR__.'/regional.php';
};

// Routes WITHOUT locale prefix (default English)
Route::group(['middleware' => 'web'], function() use ($routes) {
    $routes('');
});

// Routes WITH locale prefix (fr, sw)
Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => 'fr|sw'],
    'middleware' => 'web',
    'as' => 'locale.' // This adds 'locale.' prefix to all route names
], function() use ($routes) {
    $routes('locale.');
});

Route::get('/clear-all', function() {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return "All caches cleared!";
});
