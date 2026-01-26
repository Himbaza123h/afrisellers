<?php

use App\Http\Controllers\Frontend\VendorController;
use App\Http\Controllers\Vendor\Product\ProductController;
use App\Http\Controllers\Vendor\RFQController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\InventoryController;
use App\Http\Controllers\Vendor\ShowroomController;
use App\Http\Controllers\Vendor\TradeshowController;
use App\Http\Controllers\Vendor\LoadController;
use App\Http\Controllers\Vendor\MessageController;
use App\Http\Controllers\Vendor\ReviewController;
use App\Http\Controllers\Vendor\AnalyticsController;
use App\Http\Controllers\Vendor\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Vendor\AddonController;
use App\Http\Controllers\Vendor\SettingsController;
use App\Http\Controllers\Vendor\SubscriptionController;
use App\Http\Controllers\Vendor\PaymentController;
use App\Http\Controllers\Vendor\DocumentController;
use App\Http\Controllers\Vendor\SupportController;
use App\Http\Controllers\Vendor\TransactionController;
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\Vendor\EarningsController;
use App\Http\Controllers\Vendor\PerformanceController;
use App\Http\Controllers\Vendor\PlanController;
use App\Http\Controllers\Vendor\PromoCodeController;
use App\Http\Controllers\Vendor\VendorProfileController;
use App\Http\Controllers\Vendor\VendorShowroomController;
use Illuminate\Support\Facades\Route;

Route::prefix('vendor')
    ->name('vendor.')
    ->group(function () {
        // Step 1: Account Information
        Route::get('/register', [VendorController::class, 'showStep1'])->name('register.step1');
        Route::post('/register/step1', [VendorController::class, 'processStep1'])->name('register.step1.submit');

        // Step 2: Business Information
        Route::get('/register/step2', [VendorController::class, 'showStep2'])->name('register.step2');
        Route::post('/register/step2', [VendorController::class, 'processStep2'])->name('register.step2.submit');

        // Step 3: Documents
        Route::get('/register/step3', [VendorController::class, 'showStep3'])->name('register.step3');
        Route::post('/register/step3', [VendorController::class, 'processStep3'])->name('register.step3.submit');

        // Email Verification
        Route::get('/verify-email/{token}', [VendorController::class, 'verifyEmail'])->name('verify.email');
        Route::get('/verification-pending', [VendorController::class, 'verificationPending'])->name('verification.pending');

        // OTP Verification & Resend Email
        Route::post('/verify-otp', [VendorController::class, 'verifyOtp'])->name('verify.otp');
        Route::post('/resend-verification', [VendorController::class, 'resendVerification'])->name('resend.verification');




        // Dashboard
        Route::get('/dashboard', [VendorDashboardController::class, 'index'])
            ->name('dashboard.home');

        // web.php (in vendor routes group)
Route::get('/dashboard/print', [VendorDashboardController::class, 'print'])
    ->name('dashboard.print');

        // Product Management Routes (Vendor CRUD)
        Route::prefix('products')
            ->name('product.')
            ->group(function () {
                Route::get('/', [ProductController::class, 'index'])->name('index');

                // Route::middleware('product-plan-middleware')->group(function () {
                    // Route::get('/create', [ProductController::class, 'create'])->name('create');
                    // Route::post('/', [ProductController::class, 'store'])->name('store');

                // });



                Route::get('/', [ProductController::class, 'index'])->name('index');
                Route::get('/create', [ProductController::class, 'create'])->name('create');

                        // ADD THESE TWO NEW ROUTES FOR PRINT AND EXPORT
                Route::get('/print', [ProductController::class, 'print'])->name('print');
                Route::post('/export', [ProductController::class, 'export'])->name('export');
                Route::post('/', [ProductController::class, 'store'])->name('store');
                Route::get('/{product}', [ProductController::class, 'show'])->name('show');
                Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
                Route::put('/{product}', [ProductController::class, 'update'])->name('update');
                Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
                Route::post('/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');

                // NEW: Price management routes
                Route::get('/{product}/price', [ProductController::class, 'editPrice'])->name('price.edit');
                Route::put('/{product}/price', [ProductController::class, 'updatePrice'])->name('price.update');

                Route::get('/{product}/promo-codes', [ProductController::class, 'editPromoCodes'])->name('promo.edit');
                Route::put('/{product}/promo-codes', [ProductController::class, 'updatePromoCodes'])->name('promo.update');



            });


            // Promo Code Management Routes
            Route::prefix('promo-codes')->name('promo-code.')->group(function () {
                Route::get('/', [PromoCodeController::class, 'index'])->name('index');
                Route::get('/print', [PromoCodeController::class, 'print'])->name('print');
                Route::get('/create', [PromoCodeController::class, 'create'])->name('create');
                Route::post('/', [PromoCodeController::class, 'store'])->name('store');
                Route::get('/{promoCode}/edit', [PromoCodeController::class, 'edit'])->name('edit');
                Route::put('/{promoCode}', [PromoCodeController::class, 'update'])->name('update');
                Route::delete('/{promoCode}', [PromoCodeController::class, 'destroy'])->name('destroy');
                Route::post('/{promoCode}/toggle-status', [PromoCodeController::class, 'toggleStatus'])->name('toggle-status');
            });


        Route::get('transactions-print', [TransactionController::class, 'print'])->name('transactions.print');

        Route::get('earnings-print', [EarningsController::class, 'print'])->name('earnings.print');

        Route::get('reports-print', [ReportController::class, 'print'])->name('reports.print');

        Route::get('performance-print', [PerformanceController::class, 'print'])->name('performance.print');

        Route::get('showrooms-print', [VendorShowroomController::class, 'print'])->name('showrooms.print');


        // RFQ Routes
        Route::prefix('rfqs')
            ->name('rfq.')
            ->group(function () {
                Route::get('/', [RFQController::class, 'index'])->name('index');
                Route::get('/{rfq}', [RFQController::class, 'show'])->name('show');
                Route::post('/{rfq}/messages', [RFQController::class, 'storeMessage'])->name('message.store');
                Route::post('/{rfq}/quote', [RFQController::class, 'submitQuote'])->name('quote.submit');
                Route::post('/{rfq}/accept', [RFQController::class, 'accept'])->name('accept');
                Route::post('/{rfq}/decline', [RFQController::class, 'decline'])->name('decline');
            });

        // Order Management Routes
        Route::prefix('orders')
            ->name('orders.')
            ->group(function () {
                Route::get('/', [OrderController::class, 'index'])->name('index');
                Route::get('/create', [OrderController::class, 'create'])->name('create');
                Route::post('/', [OrderController::class, 'store'])->name('store');
                Route::get('/{order}', [OrderController::class, 'show'])->name('show');
                Route::post('/{order}/accept', [OrderController::class, 'accept'])->name('accept');
                Route::post('/{order}/process', [OrderController::class, 'process'])->name('process');
                Route::post('/{order}/ship', [OrderController::class, 'ship'])->name('ship');
                Route::post('/{order}/complete', [OrderController::class, 'complete'])->name('complete');
                Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
                Route::post('/{order}/refund', [OrderController::class, 'refund'])->name('refund');
                Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
                Route::get('/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('invoice.download');
                Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
                // AJAX routes for dynamic data
                Route::get('/buyer/{buyerId}/addresses', [OrderController::class, 'getBuyerAddresses'])->name('buyer.addresses');
                Route::get('/product/{productId}/details', [OrderController::class, 'getProductDetails'])->name('product.details');
                Route::get('/product/{productId}/price/{quantity}', [OrderController::class, 'getProductPrice'])->name('product.price');
            });

            Route::prefix('transactions')
            ->name('transactions.')
            ->group(function () {
                Route::get('/', [TransactionController::class, 'index'])->name('index');
                Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
            });

            Route::prefix('earnings')
            ->name('earnings.')
            ->group(function () {
                Route::get('/', [EarningsController::class, 'index'])->name('index');
            });

            // Subscription Management Routes
            Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
                Route::get('/', [SubscriptionController::class, 'index'])->name('index');
                Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscribe');
                Route::post('/upgrade', [SubscriptionController::class, 'upgrade'])->name('upgrade');
                Route::post('/renew', [SubscriptionController::class, 'renew'])->name('renew');
                Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
                Route::post('/toggle-auto-renew', [SubscriptionController::class, 'toggleAutoRenew'])->name('toggle-auto-renew');
                Route::get('/{subscription}/invoice', [SubscriptionController::class, 'invoice'])->name('invoice');
            });




            // Analytics Routes
            Route::prefix('analytics')
                ->name('analytics.')
                ->group(function () {
                    Route::get('/', [AnalyticsController::class, 'index'])->name('index');
                });


    Route::prefix('addons')->name('addons.')->group(function () {
        Route::get('/', [AddonController::class, 'index'])->name('index');
        Route::get('/print', [AddonController::class, 'print'])->name('print'); // ✅ ADD THIS LINE
        Route::get('/available', [AddonController::class, 'available'])->name('available');
        Route::get('/create', [AddonController::class, 'create'])->name('create');
        Route::post('/purchase/{addon}', [AddonController::class, 'purchase'])->name('purchase');
        Route::get('/{addon}/renew', [AddonController::class, 'renewForm'])->name('renew-form');
        Route::post('/{addon}/renew', [AddonController::class, 'renew'])->name('renew');
        Route::post('/{addon}/deactivate', [AddonController::class, 'deactivate'])->name('deactivate');
        Route::post('/{addon}/cancel', [AddonController::class, 'cancel'])->name('cancel');
        Route::get('/{addon}', [AddonController::class, 'show'])->name('show');
        Route::get('/{addon}/invoice', [AddonController::class, 'invoice'])->name('invoice');
    });

            // Sales Reports Routes
            Route::prefix('reports')
                ->name('reports.')
                ->group(function () {
                    Route::get('/', [ReportController::class, 'index'])->name('index');
                    Route::post('/export', [ReportController::class, 'export'])->name('export');
                });

            // Performance Routes
            Route::prefix('performance')
                ->name('performance.')
                ->group(function () {
                    Route::get('/', [PerformanceController::class, 'index'])->name('index');
                });






        // Inventory Management Routes
        Route::prefix('inventory')
            ->name('inventory.')
            ->group(function () {
                Route::get('/', [InventoryController::class, 'index'])->name('index');
                Route::get('/{product}', [InventoryController::class, 'show'])->name('show');
                Route::post('/{product}/update-stock', [InventoryController::class, 'updateStock'])->name('update-stock');
                Route::post('/{product}/stock-alert', [InventoryController::class, 'setStockAlert'])->name('stock-alert');
                Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
                Route::get('/out-of-stock', [InventoryController::class, 'outOfStock'])->name('out-of-stock');
                Route::post('/bulk-update', [InventoryController::class, 'bulkUpdate'])->name('bulk-update');
            });




    // Vendor Showrooms Routes
    Route::prefix('showrooms')->name('showrooms.')->group(function () {
        Route::get('/', [VendorShowroomController::class, 'index'])->name('index');
        Route::get('/create', [VendorShowroomController::class, 'create'])->name('create');
        Route::post('/', [VendorShowroomController::class, 'store'])->name('store');
        Route::get('/{id}', [VendorShowroomController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [VendorShowroomController::class, 'edit'])->name('edit');
        Route::put('/{id}', [VendorShowroomController::class, 'update'])->name('update');
        Route::delete('/{id}', [VendorShowroomController::class, 'destroy'])->name('destroy');

        // Gallery management
        Route::get('/{id}/gallery', [VendorShowroomController::class, 'gallery'])->name('gallery');
        Route::post('/{id}/gallery/upload', [VendorShowroomController::class, 'uploadGalleryImages'])->name('gallery.upload');
        Route::delete('/{id}/gallery/delete', [VendorShowroomController::class, 'deleteGalleryImage'])->name('gallery.delete');

        // Product management
        Route::get('/{id}/products', [VendorShowroomController::class, 'products'])->name('products');
        Route::post('/{id}/products/add', [VendorShowroomController::class, 'addProduct'])->name('products.add');
        Route::delete('/{id}/products/remove', [VendorShowroomController::class, 'removeProduct'])->name('products.remove');
    });

        // Tradeshow Management Routes
        Route::prefix('tradeshows')
            ->name('tradeshow.')
            ->group(function () {
                Route::get('/', [TradeshowController::class, 'index'])->name('index');
                Route::get('/browse', [TradeshowController::class, 'browse'])->name('browse');
                Route::get('/{tradeshow}', [TradeshowController::class, 'show'])->name('show');
                Route::post('/{tradeshow}/register', [TradeshowController::class, 'register'])->name('register');
                Route::post('/{tradeshow}/cancel-registration', [TradeshowController::class, 'cancelRegistration'])->name('cancel-registration');
                Route::get('/my-registrations', [TradeshowController::class, 'myRegistrations'])->name('my-registrations');
            });

        // Load/Shipping Management Routes
        Route::prefix('loads')
            ->name('load.')
            ->group(function () {
                Route::get('/', [LoadController::class, 'index'])->name('index');
                Route::get('/create', [LoadController::class, 'create'])->name('create');
                Route::post('/', [LoadController::class, 'store'])->name('store');
                Route::get('/{load}', [LoadController::class, 'show'])->name('show');
                Route::get('/{load}/edit', [LoadController::class, 'edit'])->name('edit');
                Route::match(['put', 'patch'], '/{load}', [LoadController::class, 'update'])->name('update');
                Route::delete('/{load}', [LoadController::class, 'destroy'])->name('destroy');
                Route::post('/{load}/assign-transporter', [LoadController::class, 'assignTransporter'])->name('assign-transporter');
                Route::post('/{load}/track', [LoadController::class, 'track'])->name('track');
            });

        // Messages/Communication Routes
        Route::prefix('messages')
            ->name('message.')
            ->group(function () {
                Route::get('/', [MessageController::class, 'index'])->name('index');
                Route::get('/compose', [MessageController::class, 'compose'])->name('compose');
                Route::post('/', [MessageController::class, 'store'])->name('store');
                Route::get('/{conversation}', [MessageController::class, 'show'])->name('show');
                Route::post('/{conversation}/reply', [MessageController::class, 'reply'])->name('reply');
                Route::delete('/{message}', [MessageController::class, 'destroy'])->name('destroy');
                Route::post('/{message}/mark-read', [MessageController::class, 'markAsRead'])->name('mark-read');
                Route::post('/mark-all-read', [MessageController::class, 'markAllAsRead'])->name('mark-all-read');
            });

        // Reviews & Ratings Routes
        Route::prefix('reviews')
            ->name('review.')
            ->group(function () {
                Route::get('/', [ReviewController::class, 'index'])->name('index');
                Route::get('/{review}', [ReviewController::class, 'show'])->name('show');
                Route::post('/{review}/respond', [ReviewController::class, 'respond'])->name('respond');
                Route::post('/{review}/report', [ReviewController::class, 'report'])->name('report');
            });

        // My store settings
        Route::prefix('store')
            ->name('store.')
            ->group(function () {
                Route::get('/settings', [VendorProfileController::class, 'storeSettings'])->name('settings');
                Route::post('/settings', [VendorProfileController::class, 'updateStoreSettings'])->name('update-settings');
            });

        // Analytics Routes
        // Route::prefix('analytics')
        //     ->name('analytics.')
        //     ->group(function () {
        //         Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        //         Route::get('/sales', [AnalyticsController::class, 'sales'])->name('sales');
        //         Route::get('/products', [AnalyticsController::class, 'products'])->name('products');
        //         Route::get('/customers', [AnalyticsController::class, 'customers'])->name('customers');
        //         Route::get('/traffic', [AnalyticsController::class, 'traffic'])->name('traffic');
        //         Route::get('/performance', [AnalyticsController::class, 'performance'])->name('performance');
        //     });

        // // Reports Routes
        // Route::prefix('reports')
            // ->name('report.')
            // ->group(function () {
            //     Route::get('/', [ReportController::class, 'index'])->name('index');
            //     Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            //     Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            //     Route::get('/orders', [ReportController::class, 'orders'])->name('orders');
            //     Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
            //     Route::get('/products', [ReportController::class, 'products'])->name('products');
            //     Route::post('/export', [ReportController::class, 'export'])->name('export');
            //     Route::get('/custom', [ReportController::class, 'custom'])->name('custom');
            //     Route::post('/custom/generate', [ReportController::class, 'generateCustom'])->name('custom.generate');
            // });

        // Profile Routes
        Route::prefix('profile')
            ->name('profile.')
            ->group(function () {
                Route::get('/', [ProfileController::class, 'show'])->name('show');
                Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
                Route::match(['put', 'patch'], '/', [ProfileController::class, 'update'])->name('update');
                Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('update-avatar');
                Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('delete-avatar');
                Route::get('/business', [ProfileController::class, 'businessProfile'])->name('business');
                Route::match(['put', 'patch'], '/business', [ProfileController::class, 'updateBusiness'])->name('update-business');
            });

        // Settings Routes
        Route::prefix('settings')
            ->name('settings.')
            ->group(function () {
                Route::get('/', [SettingsController::class, 'index'])->name('index');
                Route::get('/general', [SettingsController::class, 'general'])->name('general');
                Route::post('/general', [SettingsController::class, 'updateGeneral'])->name('update-general');
                Route::get('/notifications', [SettingsController::class, 'notifications'])->name('notifications');
                Route::post('/notifications', [SettingsController::class, 'updateNotifications'])->name('update-notifications');
                Route::get('/security', [SettingsController::class, 'security'])->name('security');
                Route::post('/security/password', [SettingsController::class, 'updatePassword'])->name('update-password');
                Route::post('/security/two-factor', [SettingsController::class, 'toggleTwoFactor'])->name('toggle-two-factor');
                Route::get('/payment', [SettingsController::class, 'payment'])->name('payment');
                Route::post('/payment', [SettingsController::class, 'updatePayment'])->name('update-payment');
                Route::get('/shipping', [SettingsController::class, 'shipping'])->name('shipping');
                Route::post('/shipping', [SettingsController::class, 'updateShipping'])->name('update-shipping');
            });

        // Subscription/Plan Routes
        Route::prefix('subscription')
            ->name('subscription.')
            ->group(function () {
                Route::get('/', [SubscriptionController::class, 'index'])->name('index');
                Route::get('/plans', [SubscriptionController::class, 'plans'])->name('plans');
                Route::post('/upgrade', [SubscriptionController::class, 'upgrade'])->name('upgrade');
                Route::post('/downgrade', [SubscriptionController::class, 'downgrade'])->name('downgrade');
                Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
                Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
                Route::get('/history', [SubscriptionController::class, 'history'])->name('history');
                Route::get('/invoices', [SubscriptionController::class, 'invoices'])->name('invoices');
                Route::get('/invoices/{invoice}/download', [SubscriptionController::class, 'downloadInvoice'])->name('invoice.download');
            });

        // Payment Routes
        Route::prefix('payments')
            ->name('payment.')
            ->group(function () {
                Route::get('/', [PaymentController::class, 'index'])->name('index');
                Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
                Route::get('/methods', [PaymentController::class, 'methods'])->name('methods');
                Route::post('/methods', [PaymentController::class, 'addMethod'])->name('add-method');
                Route::delete('/methods/{method}', [PaymentController::class, 'deleteMethod'])->name('delete-method');
                Route::post('/methods/{method}/default', [PaymentController::class, 'setDefaultMethod'])->name('set-default');
                Route::get('/withdraw', [PaymentController::class, 'withdraw'])->name('withdraw');
                Route::post('/withdraw', [PaymentController::class, 'processWithdraw'])->name('process-withdraw');
            });

        // Documents Routes
        Route::prefix('documents')
            ->name('document.')
            ->group(function () {
                Route::get('/', [DocumentController::class, 'index'])->name('index');
                Route::get('/upload', [DocumentController::class, 'upload'])->name('upload');
                Route::post('/', [DocumentController::class, 'store'])->name('store');
                Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
                Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download');
                Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
            });

        // Support/Help Routes
        Route::prefix('support')
            ->name('support.')
            ->group(function () {
                Route::get('/', [SupportController::class, 'index'])->name('index');
                Route::get('/tickets', [SupportController::class, 'tickets'])->name('tickets');
                Route::get('/tickets/create', [SupportController::class, 'createTicket'])->name('ticket.create');
                Route::post('/tickets', [SupportController::class, 'storeTicket'])->name('ticket.store');
                Route::get('/tickets/{ticket}', [SupportController::class, 'showTicket'])->name('ticket.show');
                Route::post('/tickets/{ticket}/reply', [SupportController::class, 'replyTicket'])->name('ticket.reply');
                Route::post('/tickets/{ticket}/close', [SupportController::class, 'closeTicket'])->name('ticket.close');
                Route::get('/faq', [SupportController::class, 'faq'])->name('faq');
                Route::get('/contact', [SupportController::class, 'contact'])->name('contact');
                Route::post('/contact', [SupportController::class, 'sendContact'])->name('contact.send');
            });
    });
