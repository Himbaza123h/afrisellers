<?php

use App\Http\Controllers\Buyer\AccountController;
use App\Http\Controllers\Buyer\DashboardController;
use App\Http\Controllers\Buyer\RFQController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\BuyerController;
use App\Http\Controllers\Frontend\LoadBoardController;
use App\Http\Controllers\Frontend\ShowroomController;
use App\Http\Controllers\Frontend\TradeshowController;

Route::prefix('buyer')->name('buyer.')->group(function () {
    // Buyer registration routes (public)
    Route::post('/auth/register', [BuyerController::class, 'store'])->name('register.store');
    Route::get('/auth/verify-email', [BuyerController::class, 'showVerification'])->name('verification.show');
    Route::post('/auth/verify-email', [BuyerController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/auth/resend-verification', [BuyerController::class, 'resendVerification'])->name('verification.resend');

    // Authenticated routes
    Route::middleware('auth')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.home');

        // Orders Management
        Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');
        Route::get('/orders/{id}', [DashboardController::class, 'showOrder'])->name('orders.show');
        Route::post('/orders/{id}/cancel', [DashboardController::class, 'cancelOrder'])->name('orders.cancel');

        // Profile & Settings
        Route::get('/profile/edit', [BuyerController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile/update', [BuyerController::class, 'updateProfile'])->name('profile.update');

        // RFQs
        Route::get('/rfqs', [BuyerController::class, 'rfqs'])->name('rfqs.index');
        Route::get('/rfqs/create', [BuyerController::class, 'createRfq'])->name('rfqs.create');
        Route::post('/rfqs', [BuyerController::class, 'storeRfq'])->name('rfqs.store');
        Route::get('/rfqs/{id}', [BuyerController::class, 'showRfq'])->name('rfqs.show');

        // Support
        Route::get('/support', [BuyerController::class, 'support'])->name('support');


        // Become Vendor Routes
        Route::get('/become-vendor', [BuyerController::class, 'showBecomeVendor'])->name('become-vendor');
        Route::post('/become-vendor', [BuyerController::class, 'storeBecomeVendor'])->name('become-vendor.store');
        Route::get('/become-vendor/step2/{id}', [BuyerController::class, 'showBecomeVendorStep2'])->name('become-vendor.step2');
        Route::post('/become-vendor/step2/{id}', [BuyerController::class, 'storeBecomeVendorStep2'])->name('become-vendor.step2.store');

        // Submitted Business View
        Route::get('/submitted-business', [BuyerController::class, 'showSubmittedBusiness'])->name('submitted-business');

        // RFQ Routes
        Route::prefix('rfqs')->name('rfqs.')->group(function () {
            Route::get('/', [RFQController::class, 'index'])->name('index');
            Route::get('/{rfq}/vendors', [RFQController::class, 'showVendors'])->name('vendors');
            Route::get('/{rfq}/vendors/{vendor}/messages', [RFQController::class, 'showMessages'])->name('messages');
            Route::post('/{rfq}/messages', [RFQController::class, 'storeMessage'])->name('message.store');
            Route::post('/{rfq}/close', [RFQController::class, 'close'])->name('close');
        });

        // Account Settings Routes
        Route::prefix('account')->name('account.')->group(function () {
            Route::get('/settings', [AccountController::class, 'index'])->name('settings');
            Route::match(['put', 'patch'], '/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
            Route::post('/password', [AccountController::class, 'updatePassword'])->name('password.update');
        });
    });
});


// LoadBoard Routes (Public + Authenticated)
Route::prefix('loadboard')->name('loadboard.')->group(function () {

    // Main loadboard view with tabs
    Route::get('/main/{type?}', [LoadBoardController::class, 'index'])
        ->where('type', 'cars|loads')
        ->name('index');

    // Cars (Vehicles for Hire)
    Route::prefix('cars')->name('cars.')->group(function () {
        Route::get('/', [LoadBoardController::class, 'carsIndex'])->name('index');
        Route::get('/{listing_number}', [LoadBoardController::class, 'carShow'])->name('show');

        // Authenticated actions
        Route::middleware('auth')->group(function () {
            Route::post('/{car}/inquiry', [LoadBoardController::class, 'carInquiry'])->name('inquiry');
            Route::post('/{car}/book', [LoadBoardController::class, 'carBook'])->name('book');
        });
    });

    // Loads (Cargo needing transport)
    Route::prefix('loads')->name('loads.')->group(function () {
        Route::get('/', [LoadBoardController::class, 'loadsIndex'])->name('index');
        Route::get('/{load_number}', [LoadBoardController::class, 'loadShow'])->name('show');

        // Authenticated actions
        Route::middleware('auth')->group(function () {
            Route::post('/{load}/bid', [LoadBoardController::class, 'loadBid'])->name('bid');
            Route::get('/{load}/bids', [LoadBoardController::class, 'loadBids'])->name('bids');
        });
    });
});


Route::prefix('tradeshows')->name('tradeshows.')->group(function () {
    Route::get('/', [TradeshowController::class, 'index'])->name('index');
    Route::get('/{tradeshow:slug}', [TradeshowController::class, 'show'])->name('show');


    // Authenticated actions
    Route::middleware('auth')->group(function () {
        Route::post('/{tradeshow}/register', [TradeshowController::class, 'register'])->name('register');
        Route::post('/{tradeshow}/inquiry', [TradeshowController::class, 'inquiry'])->name('inquiry');
    });
});

Route::prefix('showrooms')->name('showrooms.')->group(function () {
    Route::get('/', [ShowroomController::class, 'index'])->name('index');
    Route::get('/{showroom:slug}', [ShowroomController::class, 'show'])->name('show');
    Route::get('/{showroom:slug}/products', [ShowroomController::class, 'products'])->name('products');



    // Authenticated actions
    Route::middleware('auth')->group(function () {
        Route::post('/{showroom}/inquiry', [ShowroomController::class, 'inquiry'])->name('inquiry');
        Route::post('/{showroom}/visit', [ShowroomController::class, 'scheduleVisit'])->name('visit');
    });
});

// Buyer routes - add these to your existing buyer routes
Route::prefix('buyer')->name('buyer.')->middleware('auth')->group(function () {

    // Load Management (Buyer posts loads)
    // Route::prefix('loads')->name('loads.')->group(function () {
    //     Route::get('/', [LoadController::class, 'index'])->name('index');
    //     Route::get('/create', [LoadController::class, 'create'])->name('create');
    //     Route::post('/', [LoadController::class, 'store'])->name('store');
    //     Route::get('/{load}', [LoadController::class, 'show'])->name('show');
    //     Route::get('/{load}/edit', [LoadController::class, 'edit'])->name('edit');
    //     Route::put('/{load}', [LoadController::class, 'update'])->name('update');
    //     Route::delete('/{load}', [LoadController::class, 'destroy'])->name('destroy');
    //     Route::post('/{load}/assign/{bid}', [LoadController::class, 'assignBid'])->name('assign-bid');
    // });

    // // Car Bookings (Buyer books vehicles)
    // Route::prefix('car-bookings')->name('car-bookings.')->group(function () {
    //     Route::get('/', [CarBookingController::class, 'index'])->name('index');
    //     Route::get('/{booking}', [CarBookingController::class, 'show'])->name('show');
    //     Route::post('/{booking}/cancel', [CarBookingController::class, 'cancel'])->name('cancel');
    // });
});

// Vendor/Transporter routes
// Route::prefix('vendor')->name('vendor.')->middleware('auth')->group(function () {

//     // Car Listings (Vendor lists vehicles for hire)
//     Route::prefix('cars')->name('cars.')->group(function () {
//         Route::get('/', [CarController::class, 'index'])->name('index');
//         Route::get('/create', [CarController::class, 'create'])->name('create');
//         Route::post('/', [CarController::class, 'store'])->name('store');
//         Route::get('/{car}', [CarController::class, 'show'])->name('show');
//         Route::get('/{car}/edit', [CarController::class, 'edit'])->name('edit');
//         Route::put('/{car}', [CarController::class, 'update'])->name('update');
//         Route::delete('/{car}', [CarController::class, 'destroy'])->name('destroy');
//         Route::post('/{car}/toggle-status', [CarController::class, 'toggleStatus'])->name('toggle-status');
//     });

//     // Load Bids (Vendor bids on loads)
//     Route::prefix('load-bids')->name('load-bids.')->group(function () {
//         Route::get('/', [LoadBidController::class, 'index'])->name('index');
//         Route::get('/{bid}', [LoadBidController::class, 'show'])->name('show');
//         Route::post('/{bid}/withdraw', [LoadBidController::class, 'withdraw'])->name('withdraw');
//     });
// });
