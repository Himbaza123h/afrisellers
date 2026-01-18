
<?php

use App\Http\Controllers\Admin\AddonController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\Country\CountryController;
use App\Http\Controllers\Admin\ProductCategory\ProductCategoryController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\BusinessProfile\BusinessProfileController;
use App\Http\Controllers\Admin\Plan\PlanController;
use App\Http\Controllers\Admin\RFQController;
use App\Http\Controllers\Admin\BuyerController;
use App\Http\Controllers\Admin\RegionalAdminController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\TransporterController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ShowroomController;
use App\Http\Controllers\Admin\TradeshowController;
use App\Http\Controllers\Admin\LoadController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\EscrowController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\Membership\MembershipPlanController;
use App\Http\Controllers\Admin\Membership\MembershipSettingController;
use App\Http\Controllers\Admin\Membership\PlanFeatureController;
use App\Http\Controllers\Admin\Membership\SubscriptionController;

Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.home');

    // Regional Admins Management
    Route::resource('regional-admins', RegionalAdminController::class);
    Route::post('regional-admins/{regionalAdmin}/activate', [RegionalAdminController::class, 'activate'])->name('regional-admins.activate');
    Route::post('regional-admins/{regionalAdmin}/deactivate', [RegionalAdminController::class, 'deactivate'])->name('regional-admins.deactivate');


            // NEW ROUTES FOR REGIONAL ADMIN USER ASSIGNMENT
    Route::get('regional-admins/{regionalAdmin}/assign-regional-user', [RegionalAdminController::class, 'showAssignRegionalUser'])->name('regional-admins.assign-regional-user');
    Route::post('regional-admins/{regionalAdmin}/assign-regional-user', [RegionalAdminController::class, 'assignRegionalUser'])->name('regional-admins.assign-regional-user.store');


    // Country Management
    Route::resource('countries', CountryController::class);
    Route::post('countries/{country}/activate', [CountryController::class, 'activate'])->name('countries.activate');
    Route::post('countries/{country}/deactivate', [CountryController::class, 'deactivate'])->name('countries.deactivate');

    // Vendor/Business Profile Management
    Route::get('business-profiles', [BusinessProfileController::class, 'index'])->name('business-profile.index');
    Route::get('business-profiles/{businessProfile}', [BusinessProfileController::class, 'show'])->name('business-profile.show');
    Route::post('business-profiles/{businessProfile}/verify', [BusinessProfileController::class, 'verify'])->name('business-profile.verify');
    Route::post('business-profiles/{businessProfile}/reject', [BusinessProfileController::class, 'reject'])->name('business-profile.reject');
    Route::post('business-profiles/{businessProfile}/suspend', [BusinessProfileController::class, 'suspend'])->name('business-profile.suspend');
    Route::post('business-profiles/{businessProfile}/activate', [BusinessProfileController::class, 'activate'])->name('business-profile.activate');

    // Buyer Management
    Route::resource('buyers', BuyerController::class);
    Route::post('buyers/{buyer}/suspend', [BuyerController::class, 'suspend'])->name('buyer.suspend');
    Route::post('buyers/{buyer}/activate', [BuyerController::class, 'activate'])->name('buyer.activate');

    // Agent Management
    Route::resource('agents', AgentController::class);
    Route::post('agents/{agent}/verify', [AgentController::class, 'verify'])->name('agents.verify');
    Route::post('agents/{agent}/suspend', [AgentController::class, 'suspend'])->name('agents.suspend');

    // Transporter Management
    Route::resource('transporters', TransporterController::class);
    Route::post('transporters/{transporter}/verify', [TransporterController::class, 'verify'])->name('transporters.verify');
    Route::post('transporters/{transporter}/suspend', [TransporterController::class, 'suspend'])->name('transporters.suspend');

    // Product Category Management
    Route::resource('product-categories', ProductCategoryController::class);
    Route::post('product-categories/{category}/activate', [ProductCategoryController::class, 'activate'])->name('product-category.activate');

    // Product Management
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/approve', [ProductController::class, 'approve'])->name('product.approve');
    Route::post('products/{product}/reject', [ProductController::class, 'reject'])->name('product.reject');
    Route::post('products/{product}/feature', [ProductController::class, 'feature'])->name('product.feature');

    // Order Management
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // RFQ Management
    Route::resource('rfqs', RFQController::class);
    Route::post('rfqs/{rfq}/approve', [RFQController::class, 'approve'])->name('rfq.approve');
    Route::post('rfqs/{rfq}/reject', [RFQController::class, 'reject'])->name('rfq.reject');

    // Showroom Management
    Route::get('showrooms', [ShowroomController::class, 'index'])->name('showrooms.index');
    Route::get('showrooms/{showroom}', [ShowroomController::class, 'show'])->name('showrooms.show');
    Route::post('showrooms/{showroom}/verify', [ShowroomController::class, 'verify'])->name('showrooms.verify');
    Route::post('showrooms/{showroom}/feature', [ShowroomController::class, 'feature'])->name('showrooms.feature');

    // Showroom Management - Add these routes
    Route::post('showrooms/{showroom}/unverify', [ShowroomController::class, 'unverify'])->name('showrooms.unverify');
    Route::post('showrooms/{showroom}/activate', [ShowroomController::class, 'activate'])->name('showrooms.activate');
    Route::post('showrooms/{showroom}/suspend', [ShowroomController::class, 'suspend'])->name('showrooms.suspend');
    Route::delete('showrooms/{showroom}', [ShowroomController::class, 'destroy'])->name('showrooms.destroy');
    // Load Management - Add missing route
    Route::delete('loads/{load}', [LoadController::class, 'destroy'])->name('loads.destroy');

    // Tradeshow Management - Add missing routes
    Route::post('tradeshows/{tradeshow}/verify', [TradeshowController::class, 'verify'])->name('tradeshows.verify');
    Route::post('tradeshows/{tradeshow}/unverify', [TradeshowController::class, 'unverify'])->name('tradeshows.unverify');
    Route::post('tradeshows/{tradeshow}/suspend', [TradeshowController::class, 'suspend'])->name('tradeshows.suspend');
    Route::delete('tradeshows/{tradeshow}', [TradeshowController::class, 'destroy'])->name('tradeshows.destroy');

    // Tradeshow Management
    Route::get('tradeshows', [TradeshowController::class, 'index'])->name('tradeshows.index');
    Route::get('tradeshows/{tradeshow}', [TradeshowController::class, 'show'])->name('tradeshows.show');
    Route::post('tradeshows/{tradeshow}/approve', [TradeshowController::class, 'approve'])->name('tradeshows.approve');
    Route::post('tradeshows/{tradeshow}/feature', [TradeshowController::class, 'feature'])->name('tradeshows.feature');


    // Transaction Management - Add missing routes
    Route::post('transactions/{transaction}/refund', [TransactionController::class, 'refund'])->name('transactions.refund');
    Route::post('transactions/{transaction}/update-status', [TransactionController::class, 'updateStatus'])->name('transactions.update-status');
    Route::post('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
    // Load Management
    Route::get('loads', [LoadController::class, 'index'])->name('loads.index');
    Route::get('loads/{load}', [LoadController::class, 'show'])->name('loads.show');
    Route::post('loads/{load}/cancel', [LoadController::class, 'cancel'])->name('loads.cancel');

    // Transaction Management
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('transactions/{transaction}/refund', [TransactionController::class, 'refund'])->name('transactions.refund');

    // Escrow Management
// Escrow Management - Update these routes
    Route::prefix('escrow')->name('escrow.')->group(function () {
        Route::get('/', [EscrowController::class, 'index'])->name('index');
        Route::get('/{escrow}', [EscrowController::class, 'show'])->name('show');
        Route::post('/{escrow}/release', [EscrowController::class, 'release'])->name('release');
        Route::post('/{escrow}/refund', [EscrowController::class, 'refund'])->name('refund');
        Route::post('/{escrow}/activate', [EscrowController::class, 'activate'])->name('activate');
        Route::post('/{escrow}/open-dispute', [EscrowController::class, 'openDispute'])->name('open-dispute');
        Route::post('/{escrow}/resolve-dispute', [EscrowController::class, 'resolveDispute'])->name('resolve-dispute');
        Route::post('/{escrow}/admin-approve', [EscrowController::class, 'adminApprove'])->name('admin-approve');
        Route::post('/{escrow}/cancel', [EscrowController::class, 'cancel'])->name('cancel');
        Route::post('/export', [EscrowController::class, 'export'])->name('export');
    });

    // Commission Management - Update these routes
    Route::prefix('commissions')->name('commissions.')->group(function () {
        Route::get('/', [CommissionController::class, 'index'])->name('index');
        Route::get('/{commission}', [CommissionController::class, 'show'])->name('show');
        Route::post('/{commission}/approve', [CommissionController::class, 'approve'])->name('approve');
        Route::post('/{commission}/mark-as-paid', [CommissionController::class, 'markAsPaid'])->name('mark-as-paid');
        Route::post('/{commission}/cancel', [CommissionController::class, 'cancel'])->name('cancel');
        Route::post('/bulk-approve', [CommissionController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-pay', [CommissionController::class, 'bulkPay'])->name('bulk-pay');
        Route::post('/export', [CommissionController::class, 'export'])->name('export');
        Route::get('/settings/edit', [CommissionController::class, 'settings'])->name('settings');
        Route::post('/settings/update', [CommissionController::class, 'updateSettings'])->name('update-settings');
    });

    // Membership Plans


    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('reports/users', [ReportController::class, 'users'])->name('reports.users');
    Route::get('reports/orders', [ReportController::class, 'orders'])->name('reports.orders');
    Route::get('reports/vendors', [ReportController::class, 'vendors'])->name('reports.vendors');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/regional', [AnalyticsController::class, 'regional'])->name('analytics.regional');
    Route::get('analytics/products', [AnalyticsController::class, 'products'])->name('analytics.products');
    Route::get('analytics/performance', [AnalyticsController::class, 'performance'])->name('analytics.performance');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('settings/general', [SettingsController::class, 'general'])->name('settings.general');
    Route::get('settings/email', [SettingsController::class, 'email'])->name('settings.email');
    Route::get('settings/payment', [SettingsController::class, 'payment'])->name('settings.payment');

    // Security
    Route::get('security', [SecurityController::class, 'index'])->name('security.index');
    Route::post('security/two-factor', [SecurityController::class, 'enableTwoFactor'])->name('security.two-factor');
    Route::get('security/sessions', [SecurityController::class, 'sessions'])->name('security.sessions');
    Route::post('security/sessions/{session}/revoke', [SecurityController::class, 'revokeSession'])->name('security.revoke-session');

    Route::get('security/change-password', [SecurityController::class, 'changePassword'])->name('security.change-password');
    Route::post('security/update-password', [SecurityController::class, 'updatePassword'])->name('security.update-password');

    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/{log}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    Route::post('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.home');

        // NEW ROUTES FOR REGIONAL ADMIN USER ASSIGNMENT
    Route::get('regional-admins/{regionalAdmin}/assign-regional-user', [RegionalAdminController::class, 'showAssignRegionalUser'])->name('regional-admins.assign-regional-user');
    Route::post('regional-admins/{regionalAdmin}/assign-regional-user', [RegionalAdminController::class, 'assignRegionalUser'])->name('regional-admins.assign-regional-user.store');


    // Country Management Routes
    Route::resource('countries', CountryController::class);

    // Product Category Management Routes
    Route::resource('product-categories', ProductCategoryController::class);

    // NEW ROUTES FOR COUNTRY ADMIN USER ASSIGNMENT
    Route::get('countries/{country}/assign-country-admin', [CountryController::class, 'showAssignCountryAdmin'])->name('countries.assign-country-admin');
    Route::post('countries/{country}/assign-country-admin', [CountryController::class, 'assignCountryAdmin'])->name('countries.assign-country-admin.store');

    // Product Management Routes
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/approve', [ProductController::class, 'approve'])->name('products.approve');
    Route::post('products/{product}/reject', [ProductController::class, 'reject'])->name('products.reject');

    // Business Profile Management Routes
    Route::resource('business-profiles', BusinessProfileController::class);

    // Plan Management Routes


    // RFQ Management Routes
    Route::resource('rfqs', RFQController::class);

    // Buyer Management Routes
    Route::resource('buyers', BuyerController::class);

    Route::prefix('orders')
            ->name('orders.')
            ->group(function () {
                Route::get('/', [OrderController::class, 'index'])->name('index');
                Route::get('/create', [OrderController::class, 'create'])->name('create');
                Route::post('/', [OrderController::class, 'store'])->name('store');
                Route::get('/{order}', [OrderController::class, 'show'])->name('show');
                Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');


                Route::match(['put', 'patch'], '/{order}', [OrderController::class, 'update'])->name('update');


                Route::post('/{order}/accept', [OrderController::class, 'accept'])->name('accept');
                Route::post('/{order}/process', [OrderController::class, 'process'])->name('process');
                Route::post('/{order}/ship', [OrderController::class, 'ship'])->name('ship');
                Route::post('/{order}/complete', [OrderController::class, 'complete'])->name('complete');
                Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
                Route::post('/{order}/refund', [OrderController::class, 'refund'])->name('refund');
                Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
                Route::get('/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('invoice.download');
            });
    // Vendor Management Routes
    Route::get('vendors', [AdminDashboardController::class, 'vendors'])->name('vendors.index');
    Route::get('vendors/{vendor}', [AdminDashboardController::class, 'vendorShow'])->name('vendors.show');
    Route::post('vendors/{vendor}/verify', [AdminDashboardController::class, 'verifyVendor'])->name('vendors.verify');

    // Order Management Routes
    Route::get('dashboardorders', [AdminDashboardController::class, 'orders'])->name('orders.Adminindex');
    Route::get('dashboardorders/{order}', [AdminDashboardController::class, 'orderShow'])->name('orders.Adminshow');

    // User Management Routes
    Route::get('users', [AdminDashboardController::class, 'users'])->name('users.index');
    Route::get('users/{user}', [AdminDashboardController::class, 'userShow'])->name('users.show');
    Route::post('users/{user}/suspend', [AdminDashboardController::class, 'suspendUser'])->name('users.suspend');
    Route::post('users/{user}/activate', [AdminDashboardController::class, 'activateUser'])->name('users.activate');

    // Showroom Management Routes
    // Route::get('showrooms', [AdminDashboardController::class, 'showrooms'])->name('showrooms.index');
    // Route::post('showrooms/{showroom}/verify', [AdminDashboardController::class, 'verifyShowroom'])->name('showrooms.verify');

    // // Tradeshow Management Routes
    // Route::get('tradeshows', [AdminDashboardController::class, 'tradeshows'])->name('tradeshows.index');
    // Route::post('tradeshows/{tradeshow}/approve', [AdminDashboardController::class, 'approveTradeshow'])->name('tradeshows.approve');

    // // Load Management Routes
    // Route::get('loads', [AdminDashboardController::class, 'loads'])->name('loads.index');
    // Route::get('loads/{load}', [AdminDashboardController::class, 'loadShow'])->name('loads.show');

    // Analytics & Reports
    Route::get('reports/revenue', [AdminDashboardController::class, 'revenueReport'])->name('reports.revenue');
    Route::get('reports/users', [AdminDashboardController::class, 'usersReport'])->name('reports.users');


    // Country Management Routes
    Route::prefix('countries')->name('country.')->group(function () {
        Route::get('/', [CountryController::class, 'index'])->name('index');
        Route::get('/create', [CountryController::class, 'create'])->name('create');
        Route::post('/', [CountryController::class, 'store'])->name('store');
        Route::get('/{country}', [CountryController::class, 'show'])->name('show');
        Route::get('/{country}/edit', [CountryController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{country}', [CountryController::class, 'update'])->name('update');
        Route::delete('/{country}', [CountryController::class, 'destroy'])->name('destroy');
        Route::post('/{country}/toggle-status', [CountryController::class, 'toggleStatus'])->name('toggle-status');
    });


    // Addon Management
    Route::prefix('addons')->name('addons.')->group(function () {
        Route::get('/', [AddonController::class, 'index'])->name('index');
        Route::get('/create', [AddonController::class, 'create'])->name('create');
        Route::post('/', [AddonController::class, 'store'])->name('store');
        Route::get('/{addon}', [AddonController::class, 'show'])->name('show');
        Route::get('/{addon}/edit', [AddonController::class, 'edit'])->name('edit');
        Route::put('/{addon}', [AddonController::class, 'update'])->name('update');
        Route::delete('/{addon}', [AddonController::class, 'destroy'])->name('destroy');
    });

    // Product Category Management Routes
    Route::prefix('product-categories')->name('product-category.')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
        Route::get('/create', [ProductCategoryController::class, 'create'])->name('create');
        Route::post('/', [ProductCategoryController::class, 'store'])->name('store');
        Route::get('/{productCategory}', [ProductCategoryController::class, 'show'])->name('show');
        Route::get('/{productCategory}/edit', [ProductCategoryController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{productCategory}', [ProductCategoryController::class, 'update'])->name('update');
        Route::delete('/{productCategory}', [ProductCategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{productCategory}/toggle-status', [ProductCategoryController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Product Management Routes (Admin - View & Verify Only)
    Route::prefix('products')->name('product.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::post('/{product}/toggle-verification', [ProductController::class, 'toggleVerification'])->name('toggle-verification');
    });

    // Business Profile Management Routes (Pending Vendors)
    Route::prefix('business-profiles')->name('business-profile.')->group(function () {
        Route::get('/', [BusinessProfileController::class, 'index'])->name('index');
        Route::get('/{businessProfile}', [BusinessProfileController::class, 'show'])->name('show');
        Route::post('/{businessProfile}/verify', [BusinessProfileController::class, 'verify'])->name('verify');
        Route::post('/{businessProfile}/reject', [BusinessProfileController::class, 'reject'])->name('reject');
    });

    // Membership System Routes
    Route::prefix('memberships')->name('memberships.')->group(function () {
        // Membership Plans
        Route::get('/plans', [MembershipPlanController::class, 'index'])->name('plans.index');
        Route::get('/plans/create', [MembershipPlanController::class, 'create'])->name('plans.create');
        Route::post('/plans', [MembershipPlanController::class, 'store'])->name('plans.store');
        Route::get('/plans/{membershipPlan}/edit', [MembershipPlanController::class, 'edit'])->name('plans.edit');
        Route::put('/plans/{membershipPlan}', [MembershipPlanController::class, 'update'])->name('plans.update');
        Route::delete('/plans/{membershipPlan}', [MembershipPlanController::class, 'destroy'])->name('plans.destroy');
        Route::post('/plans/{membershipPlan}/toggle-status', [MembershipPlanController::class, 'toggleStatus'])->name('plans.toggle-status');

        // Plan Features
        Route::get('/plans/{membershipPlan}/features', [PlanFeatureController::class, 'index'])->name('features.index');
        Route::post('/plans/{membershipPlan}/features', [PlanFeatureController::class, 'store'])->name('features.store');
        Route::put('/features/{feature}', [PlanFeatureController::class, 'update'])->name('features.update');
        Route::delete('/features/{feature}', [PlanFeatureController::class, 'destroy'])->name('features.destroy');

        // Subscriptions
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
        Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('/subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew'])->name('subscriptions.renew');
        Route::post('/subscriptions/{subscription}/change-plan', [SubscriptionController::class, 'changePlan'])->name('subscriptions.change-plan');

        // System Settings
        Route::get('/settings', [MembershipSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [MembershipSettingController::class, 'update'])->name('settings.update');
    });

    // RFQ Management Routes (Admin - No limits, no plan check)
    Route::prefix('rfqs')->name('rfq.')->group(function () {
        Route::get('/', [RFQController::class, 'index'])->name('index');
        Route::get('/{rfq}/vendors', [RFQController::class, 'showVendors'])->name('vendors');
        Route::get('/{rfq}/vendors/{vendor}/messages', [RFQController::class, 'showMessages'])->name('messages');
        Route::post('/{rfq}/messages', [RFQController::class, 'storeMessage'])->name('message.store');
    });

    // Buyer Management Routes
    Route::prefix('buyers')->name('buyer.')->group(function () {
        Route::get('/', [BuyerController::class, 'index'])->name('index');
        Route::get('/{buyer}', [BuyerController::class, 'show'])->name('show');
        Route::post('/{buyer}/update-status', [BuyerController::class, 'updateStatus'])->name('update-status');
    });
});
