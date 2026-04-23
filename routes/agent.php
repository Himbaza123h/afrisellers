<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agent\DashboardController;
use App\Http\Controllers\Agent\ReferralController;
use App\Http\Controllers\Agent\CommissionController;
use App\Http\Controllers\Agent\PackageController;
use App\Http\Controllers\Agent\SubscriptionController;
use App\Http\Controllers\Agent\VendorController;
use App\Http\Controllers\Agent\TransactionController;
use App\Http\Controllers\Agent\EarningsController;
use App\Http\Controllers\Agent\AnalyticsController;
use App\Http\Controllers\Agent\ReportController;
use App\Http\Controllers\Agent\PerformanceController;
use App\Http\Controllers\Agent\ProfileController;
use App\Http\Controllers\Agent\SettingsController;
use App\Http\Controllers\Agent\PaymentController;
use App\Http\Controllers\Agent\SupportController;
use App\Http\Controllers\Agent\MessageController;
use App\Http\Controllers\Agent\DocumentController;
use App\Http\Controllers\Agent\NotificationController;
use App\Http\Controllers\Agent\PayoutController;

Route::prefix('agent')->name('agent.')->middleware(['auth'])->group(function () {

    // ─────────────────────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.home');
    Route::get('/dashboard/print', [DashboardController::class, 'print'])->name('dashboard.print');


    // ─────────────────────────────────────────────────────────
    // REFERRALS
    // ─────────────────────────────────────────────────────────
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/',                        [ReferralController::class, 'index'])->name('index');
        Route::get('/print',                   [ReferralController::class, 'print'])->name('print');
        Route::get('/create',                  [ReferralController::class, 'create'])->name('create');
        Route::post('/',                       [ReferralController::class, 'store'])->name('store');
        Route::get('/{id}',                    [ReferralController::class, 'show'])->name('show');
        Route::get('/{id}/edit',               [ReferralController::class, 'edit'])->name('edit');
        Route::put('/{id}',                    [ReferralController::class, 'update'])->name('update');
        Route::delete('/{id}',                 [ReferralController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/status',           [ReferralController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/resend-invite',     [ReferralController::class, 'resendInvite'])->name('resend-invite');
        Route::post('/generate-link',          [ReferralController::class, 'generateLink'])->name('generate-link');
    });


    // ─────────────────────────────────────────────────────────
    // COMMISSIONS
    // ─────────────────────────────────────────────────────────
    Route::prefix('commissions')->name('commissions.')->group(function () {
        Route::get('/',          [CommissionController::class, 'index'])->name('index');
        Route::get('/print',     [CommissionController::class, 'print'])->name('print');
        Route::get('/{id}',      [CommissionController::class, 'show'])->name('show');
        Route::post('/export',   [CommissionController::class, 'export'])->name('export');
    });


    // ─────────────────────────────────────────────────────────
    // VENDORS UNDER AGENT
    // Agents manage vendors they onboarded; their subscription
    // plan controls how many vendors they can have active.
    // ─────────────────────────────────────────────────────────
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/',                        [VendorController::class, 'index'])->name('index');
        Route::get('/print',                   [VendorController::class, 'print'])->name('print');
        Route::get('/create',                  [VendorController::class, 'create'])->name('create');
        Route::post('/',                       [VendorController::class, 'store'])->name('store');
        Route::get('/{id}',                    [VendorController::class, 'show'])->name('show');
        Route::get('/{id}/edit',               [VendorController::class, 'edit'])->name('edit');
        Route::put('/{id}',                    [VendorController::class, 'update'])->name('update');
        Route::delete('/{id}',                 [VendorController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/status',           [VendorController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/suspend',           [VendorController::class, 'suspend'])->name('suspend');
        Route::post('/{id}/activate',          [VendorController::class, 'activate'])->name('activate');
        Route::post('/{id}/transfer',          [VendorController::class, 'transfer'])->name('transfer');
        Route::get('/{id}/commissions',        [VendorController::class, 'commissions'])->name('commissions');
        Route::get('/{id}/orders',             [VendorController::class, 'orders'])->name('orders');
        Route::post('/export',                 [VendorController::class, 'export'])->name('export');
    });


    // ─────────────────────────────────────────────────────────
    // PACKAGES  (browse & purchase plans)
    // ─────────────────────────────────────────────────────────
    Route::prefix('packages')->name('packages.')->group(function () {
        Route::get('/',                          [PackageController::class, 'index'])->name('index');
        Route::get('/print',                     [PackageController::class, 'print'])->name('print');
        Route::get('/{id}',                      [PackageController::class, 'show'])->name('show');
        Route::get('/{id}/checkout',             [PackageController::class, 'checkout'])->name('checkout');
        Route::post('/{id}/subscribe',           [PackageController::class, 'subscribe'])->name('subscribe');
    });


    // ─────────────────────────────────────────────────────────
    // SUBSCRIPTIONS
    // Agents subscribe to plans that determine how many
    // vendors they can onboard and manage at a time.
    // ─────────────────────────────────────────────────────────
    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/',                                        [SubscriptionController::class, 'index'])->name('index');
        Route::get('/plans',                                   [SubscriptionController::class, 'plans'])->name('plans');
        Route::get('/current',                                 [SubscriptionController::class, 'current'])->name('current');
        Route::get('/history',                                 [SubscriptionController::class, 'history'])->name('history');
        Route::get('/print',                                   [SubscriptionController::class, 'print'])->name('print');
        Route::post('/subscribe/{plan}',                       [SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::post('/upgrade',                                [SubscriptionController::class, 'upgrade'])->name('upgrade');
        Route::post('/downgrade',                              [SubscriptionController::class, 'downgrade'])->name('downgrade');
        Route::post('/renew',                                  [SubscriptionController::class, 'renew'])->name('renew');
        Route::post('/cancel',                                 [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/resume',                                 [SubscriptionController::class, 'resume'])->name('resume');
        Route::post('/toggle-auto-renew',                      [SubscriptionController::class, 'toggleAutoRenew'])->name('toggle-auto-renew');
        Route::get('/invoices',                                [SubscriptionController::class, 'invoices'])->name('invoices');
        Route::get('/{subscription}/invoice',                  [SubscriptionController::class, 'invoice'])->name('invoice');
        Route::get('/{subscription}/invoice/download',         [SubscriptionController::class, 'downloadInvoice'])->name('invoice.download');
        Route::get('/{subscription}/plan-pdf',                 [SubscriptionController::class, 'planPdf'])->name('plan-pdf');
    });


    // ─────────────────────────────────────────────────────────
    // TRANSACTIONS
    // ─────────────────────────────────────────────────────────
    Route::get('transactions/print',    [TransactionController::class, 'print'])->name('transactions.print');
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/',        [TransactionController::class, 'index'])->name('index');
        Route::get('/{id}',    [TransactionController::class, 'show'])->name('show');
        Route::post('/export', [TransactionController::class, 'export'])->name('export');
    });


    // ─────────────────────────────────────────────────────────
    // EARNINGS
    // ─────────────────────────────────────────────────────────
    Route::get('earnings/print', [EarningsController::class, 'print'])->name('earnings.print');
    Route::prefix('earnings')->name('earnings.')->group(function () {
        Route::get('/',        [EarningsController::class, 'index'])->name('index');
        Route::post('/export', [EarningsController::class, 'export'])->name('export');

    });


    // ─────────────────────────────────────────────────────────
    // PAYOUTS  (agent withdrawal requests)
    // ─────────────────────────────────────────────────────────
    Route::prefix('payouts')->name('payouts.')->group(function () {
        Route::get('/',                    [PayoutController::class, 'index'])->name('index');
        Route::get('/print',               [PayoutController::class, 'print'])->name('print');
        Route::get('/request',             [PayoutController::class, 'request'])->name('request');
        Route::post('/request',            [PayoutController::class, 'store'])->name('store');
        Route::get('/{id}',                [PayoutController::class, 'show'])->name('show');
        Route::post('/{id}/cancel',        [PayoutController::class, 'cancel'])->name('cancel');
    });


    // ─────────────────────────────────────────────────────────
    // ANALYTICS
    // ─────────────────────────────────────────────────────────
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/',            [AnalyticsController::class, 'index'])->name('index');
        Route::get('/referrals',   [AnalyticsController::class, 'referrals'])->name('referrals');
        Route::get('/vendors',     [AnalyticsController::class, 'vendors'])->name('vendors');
        Route::get('/commissions', [AnalyticsController::class, 'commissions'])->name('commissions');
    });


    // ─────────────────────────────────────────────────────────
    // REPORTS
    // ─────────────────────────────────────────────────────────
    Route::get('reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',        [ReportController::class, 'index'])->name('index');
        Route::post('/export', [ReportController::class, 'export'])->name('export');
    });


    // ─────────────────────────────────────────────────────────
    // PERFORMANCE
    // ─────────────────────────────────────────────────────────
    Route::get('performance/print', [PerformanceController::class, 'print'])->name('performance.print');
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [PerformanceController::class, 'index'])->name('index');
        Route::get('/{product}', [PerformanceController::class, 'show'])->name('show');
    });


    // ─────────────────────────────────────────────────────────
    // PAYMENTS  (methods & withdrawals)
    // ─────────────────────────────────────────────────────────
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/',                              [PaymentController::class, 'index'])->name('index');
        Route::get('/{id}',                          [PaymentController::class, 'show'])->name('show');
        Route::get('/methods',                       [PaymentController::class, 'methods'])->name('methods');
        Route::post('/methods',                      [PaymentController::class, 'addMethod'])->name('add-method');
        Route::delete('/methods/{method}',           [PaymentController::class, 'deleteMethod'])->name('delete-method');
        Route::post('/methods/{method}/default',     [PaymentController::class, 'setDefaultMethod'])->name('set-default');
        Route::get('/withdraw',                      [PaymentController::class, 'withdraw'])->name('withdraw');
        Route::post('/withdraw',                     [PaymentController::class, 'processWithdraw'])->name('process-withdraw');
    });


    // ─────────────────────────────────────────────────────────
    // MESSAGES
    // ─────────────────────────────────────────────────────────
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/',                              [MessageController::class, 'index'])->name('index');
        Route::get('/compose',                       [MessageController::class, 'compose'])->name('compose');
        Route::post('/',                             [MessageController::class, 'store'])->name('store');
        Route::get('/{conversation}',                [MessageController::class, 'show'])->name('show');
        Route::post('/{conversation}/reply',         [MessageController::class, 'reply'])->name('reply');
        Route::delete('/{message}',                  [MessageController::class, 'destroy'])->name('destroy');
        Route::post('/{message}/mark-read',          [MessageController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read',                [MessageController::class, 'markAllAsRead'])->name('mark-all-read');
    });


    // ─────────────────────────────────────────────────────────
    // DOCUMENTS
    // ─────────────────────────────────────────────────────────
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/',                      [DocumentController::class, 'index'])->name('index');
        Route::get('/upload',                [DocumentController::class, 'upload'])->name('upload');
        Route::post('/',                     [DocumentController::class, 'store'])->name('store');
        Route::get('/{document}',            [DocumentController::class, 'show'])->name('show');
        Route::get('/{document}/download',   [DocumentController::class, 'download'])->name('download');
        Route::delete('/{document}',         [DocumentController::class, 'destroy'])->name('destroy');
    });


    // ─────────────────────────────────────────────────────────
    // PROFILE
    // ─────────────────────────────────────────────────────────
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',                      [ProfileController::class, 'show'])->name('show');
        Route::get('/edit',                  [ProfileController::class, 'edit'])->name('edit');
        Route::match(['put','patch'], '/',   [ProfileController::class, 'update'])->name('update');
        Route::post('/avatar',               [ProfileController::class, 'updateAvatar'])->name('update-avatar');
        Route::delete('/avatar',             [ProfileController::class, 'deleteAvatar'])->name('delete-avatar');
        Route::get('/business',              [ProfileController::class, 'businessProfile'])->name('business');
        Route::match(['put','patch'], '/business', [ProfileController::class, 'updateBusiness'])->name('update-business');
    });


    // ─────────────────────────────────────────────────────────
    // SETTINGS
    // ─────────────────────────────────────────────────────────
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/',                          [SettingsController::class, 'index'])->name('index');
        Route::get('/general',                   [SettingsController::class, 'general'])->name('general');
        Route::post('/general',                  [SettingsController::class, 'updateGeneral'])->name('update-general');
        Route::get('/notifications',             [SettingsController::class, 'notifications'])->name('notifications');
        Route::post('/notifications',            [SettingsController::class, 'updateNotifications'])->name('update-notifications');
        Route::get('/security',                  [SettingsController::class, 'security'])->name('security');
        Route::post('/security/password',        [SettingsController::class, 'updatePassword'])->name('update-password');
        Route::post('/security/two-factor',      [SettingsController::class, 'toggleTwoFactor'])->name('toggle-two-factor');
        Route::get('/payment',                   [SettingsController::class, 'payment'])->name('payment');
        Route::post('/payment',                  [SettingsController::class, 'updatePayment'])->name('update-payment');
        Route::get('/commission',                [SettingsController::class, 'commission'])->name('commission');
        Route::post('/commission',               [SettingsController::class, 'updateCommission'])->name('update-commission');
    });


    // ─────────────────────────────────────────────────────────
    // NOTIFICATIONS
    // ─────────────────────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',                  [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/mark-read',   [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read',    [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}',           [NotificationController::class, 'destroy'])->name('destroy');
    });


    // ─────────────────────────────────────────────────────────
    // SUPPORT
    // ─────────────────────────────────────────────────────────
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/',                          [SupportController::class, 'index'])->name('index');
        Route::get('/tickets',                   [SupportController::class, 'tickets'])->name('tickets');
        Route::get('/tickets/create',            [SupportController::class, 'createTicket'])->name('ticket.create');
        Route::post('/tickets',                  [SupportController::class, 'storeTicket'])->name('ticket.store');
        Route::get('/tickets/{ticket}',          [SupportController::class, 'showTicket'])->name('ticket.show');
        Route::post('/tickets/{ticket}/reply',   [SupportController::class, 'replyTicket'])->name('ticket.reply');
        Route::post('/tickets/{ticket}/close',   [SupportController::class, 'closeTicket'])->name('ticket.close');
        Route::get('/faq',                       [SupportController::class, 'faq'])->name('faq');
        Route::get('/contact',                   [SupportController::class, 'contact'])->name('contact');
        Route::post('/contact',                  [SupportController::class, 'sendContact'])->name('contact.send');
    });

});
