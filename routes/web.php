<?php

use App\Http\Controllers\Frontend\CountryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\RegionController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\RFQController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\GroupManagementController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SystemLogController;
use Illuminate\Support\Facades\Artisan;
    use App\Http\Controllers\Frontend\CompanyShowController;



// Language switcher route (no locale prefix)
Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr', 'sw'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');




Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');


// Partner Request (public)
Route::get('/become-a-partner', [\App\Http\Controllers\Frontend\PartnerRequestController::class, 'show'])->name('partner.request.form');
Route::post('/become-a-partner', [\App\Http\Controllers\Frontend\PartnerRequestController::class, 'store'])->name('partner.request.store');
Route::get('/become-a-partner/thank-you', [\App\Http\Controllers\Frontend\PartnerRequestController::class, 'success'])->name('partner.request.success');

// Single Partner Public Page
Route::get('/partners/{id}/{name}', [\App\Http\Controllers\Frontend\PartnerController::class, 'show'])
    ->name('partners.show');
// Define routes function with optional name prefix
$routes = function ($namePrefix = '') {
    Route::get('/', [HomeController::class, 'index'])->name($namePrefix . 'home');
    Route::get('/search/{type}/{slug}', [ProductController::class, 'search'])->name($namePrefix . 'products.search');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name($namePrefix . 'products.show');
    Route::post('/products/review', [ProductController::class, 'storeReview'])->name($namePrefix . 'products.review');
    Route::get('/livestream', [HomeController::class, 'livestream'])->name($namePrefix . 'livestream');

    Route::post('/products/{product}/track-click', [ProductController::class, 'trackClick'])->name('products.track-click');
    Route::post('/products/{product}/track-impression', [ProductController::class, 'trackImpression'])->name('products.track-impression');

    Route::get('/category/{slug}', [ProductController::class, 'categoryProducts'])
     ->name($namePrefix . 'categories.products');

    Route::get('/search', [HomeController::class, 'globalSearch'])->name($namePrefix . 'global.search');
    Route::get('/search/suggestions', [HomeController::class, 'searchSuggestions'])->name($namePrefix . 'search.suggestions');

    Route::get('/business-profile/{businessProfileId}/{name?}', [HomeController::class, 'showBusinessProfile'])->name($namePrefix . 'business-profile.show');





    Route::prefix('company/{id}')->name('company.')->group(function () {

        // 1 · Profile Overview  (default landing — /company/42)
        Route::get('/',           [CompanyShowController::class, 'overview'])    ->name('overview');

        // 2 · Company Info
        Route::get('/info',       [CompanyShowController::class, 'companyInfo']) ->name('info');

        // 3 · Branding & Content
        Route::get('/branding',   [CompanyShowController::class, 'branding'])    ->name('branding');

        // 4 · Contact Details
        Route::get('/contact',    [CompanyShowController::class, 'contact'])     ->name('contact');

        // 5 · Social Media
        Route::get('/social',     [CompanyShowController::class, 'social'])      ->name('social');

        // 6 · Business Type
        Route::get('/type',       [CompanyShowController::class, 'businessType'])->name('type');

        // 7 · Operations
        Route::get('/operations', [CompanyShowController::class, 'operations'])  ->name('operations');
    });

        // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name($namePrefix . 'wishlist.index');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name($namePrefix . 'wishlist.toggle');
    Route::delete('/wishlist/{id}/remove', [WishlistController::class, 'remove'])->name($namePrefix . 'wishlist.remove');
    Route::get('/wishlist/count', [WishlistController::class, 'count'])->name($namePrefix . 'wishlist.count');
    // Notification routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
    });


    // System Alerts routes
    Route::middleware('auth')->group(function () {


    Route::get('/system-logs/active-count', [SystemLogController::class, 'activeCount']);
    Route::get('/system-logs/recent',       [SystemLogController::class, 'recent']);

    Route::get('/system-alerts', [App\Http\Controllers\SystemAlertController::class, 'index'])->name('alerts.index');
    Route::post('/system-alerts/{id}/resolve', [App\Http\Controllers\SystemAlertController::class, 'resolve'])->name('alerts.resolve');
    Route::post('/system-alerts/{id}/dismiss', [App\Http\Controllers\SystemAlertController::class, 'dismiss'])->name('alerts.dismiss');
    Route::post('/system-alerts/clear-all', [App\Http\Controllers\SystemAlertController::class, 'clearAll'])->name('alerts.clearAll');
    Route::get('/system-alerts/active-count', [App\Http\Controllers\SystemAlertController::class, 'activeCount'])->name('alerts.activeCount');
});


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



        // Add to routes/web.php
        Route::get('/messages/join', function() {
            return view('messages.join');
        })->name('message.join-page')->middleware('auth');



            // Region Routes
        Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');
        Route::get('/regions/{region}/countries', [RegionController::class, 'countries'])->name('regions.countries');

        // Country Business Profiles Route
        Route::get('/countries/{country}/business-profiles', [CountryController::class, 'businessProfiles'])->name('country.business-profiles');



        // Country Products grouped by supplier
        Route::get('/countries/{country}/products', [CountryController::class, 'products'])->name($namePrefix . 'country.products');

        // Business Profile Products
        Route::get('/business-profile/{businessProfileId}/products', [HomeController::class, 'businessProfileProducts'])->name($namePrefix . 'business-profile.products');


        Route::get('/business-profile/{businessProfileId}/products/{articleSlug}', [HomeController::class, 'singleArticle'])->name($namePrefix . 'business-profile.products.singleArticle');

        Route::post('/business-profile/{businessProfileId}/products/{articleSlug}/comment', [HomeController::class, 'storeArticleComment'])->name($namePrefix . 'business-profile.products.comment.store');



    Route::post('/business-profile/{businessProfileId}/products/{articleSlug}/like', [HomeController::class, 'toggleArticleLike'])->name($namePrefix . 'business-profile.products.like');


    Route::post('/articles/comments/{comment}/approve', [HomeController::class, 'approveComment'])->name($namePrefix . 'articles.comments.approve');
    Route::post('/articles/comments/{comment}/reject', [HomeController::class, 'rejectComment'])->name($namePrefix . 'articles.comments.reject');

    Route::post('/vendor/articles/{article}/toggle-auto-approve', [\App\Http\Controllers\Vendor\ArticleController::class, 'toggleAutoApprove'])->name('vendor.articles.toggle-auto-approve');

    // Featured Suppliers
    Route::get('/featured-suppliers', [HomeController::class, 'featuredSuppliers'])->name($namePrefix . 'featured-suppliers');

    // All Countries
    Route::get('/countries', [HomeController::class, 'allCountries'])->name($namePrefix . 'countries');

    // RFQ Routes
    Route::get('/rfqs/create', [RFQController::class, 'create'])->name($namePrefix . 'rfqs.create');
    Route::post('/rfqs', [RFQController::class, 'store'])->name($namePrefix . 'rfqs.store');
    Route::post('/rfqs/translate', [RFQController::class, 'translate'])->name('rfqs.translate');
    Route::post('/rfqs/upload-image', [RFQController::class, 'uploadImage'])->name($namePrefix . 'rfqs.upload-image');
    Route::post('/rfqs/upload-file', [RFQController::class, 'uploadFile'])->name($namePrefix . 'rfqs.upload-file');





Route::middleware(['auth'])->prefix('messages')->name('messages.')->group(function () {
    Route::get('/', [MessageController::class, 'index'])->name('index');
    Route::get('/group/{id}', [MessageController::class, 'show'])->name('show');
    Route::get('/private/{userId}', [MessageController::class, 'private'])->name('private');
    Route::post('/send', [MessageController::class, 'store'])->name('store');
    Route::post('/group/create', [MessageController::class, 'createGroup'])->name('group.create');
    Route::get('/search-users', [MessageController::class, 'searchUsers'])->name('search-users');
    Route::get('/members-by-type', [MessageController::class, 'getMembersByType'])->name('members-by-type');

    Route::get('/load-private/{userId}', [MessageController::class, 'loadPrivateChat'])->name('load-private');
Route::get('/load-group/{id}', [MessageController::class, 'loadGroupChat'])->name('load-group');

    // Group Management
    Route::get('/group/{id}/settings', [GroupManagementController::class, 'show'])->name('group.settings');
    Route::post('/group/{id}/update', [GroupManagementController::class, 'update'])->name('group.update');
    Route::post('/group/{id}/add-member', [GroupManagementController::class, 'addMember'])->name('group.add-member');
    Route::delete('/group/{id}/remove-member/{userId}', [GroupManagementController::class, 'removeMember'])->name('group.remove-member');
    Route::post('/group/{id}/make-admin/{userId}', [GroupManagementController::class, 'makeAdmin'])->name('group.make-admin');
    Route::post('/group/{id}/remove-admin/{userId}', [GroupManagementController::class, 'removeAdmin'])->name('group.remove-admin');
    Route::post('/group/{id}/leave', [GroupManagementController::class, 'leave'])->name('group.leave');
    Route::post('/group/{id}/toggle-lock', [GroupManagementController::class, 'toggleLock'])->name('group.toggle-lock');
    Route::post('/group/{id}/generate-invite', [GroupManagementController::class, 'generateInvite'])->name('group.generate-invite');
    Route::post('/join-group', [GroupManagementController::class, 'joinViaInvite'])->name('group.join');
    Route::get('/group/{id}/search-users', [GroupManagementController::class, 'searchUsers'])->name('group.search-users');
});







    require __DIR__.'/vendor.php';
    require __DIR__.'/auth.php';
    require __DIR__.'/buyer.php';
    require __DIR__.'/admin.php';
    require __DIR__.'/agent.php';
    require __DIR__.'/country.php';
    require __DIR__.'/regional.php';
    require __DIR__.'/partner.php';
};

// Routes WITHOUT locale prefix (default English)
Route::group(['middleware' => ['web', 'track.visitor']], function() use ($routes) {
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
