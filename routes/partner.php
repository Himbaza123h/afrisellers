<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Partner\DashboardController;
use App\Http\Controllers\Partner\ProfileController;
use App\Http\Controllers\Partner\CompanyController;
use App\Http\Controllers\Partner\BrandingController;
use App\Http\Controllers\Partner\ContactController;
use App\Http\Controllers\Partner\SocialController;
use App\Http\Controllers\Partner\BusinessController;
use App\Http\Controllers\Partner\OperationsController;
use App\Http\Controllers\Partner\SettingsController;
use App\Http\Controllers\Partner\NotificationController;
use App\Http\Controllers\Partner\MessageController;
use App\Http\Controllers\Partner\DocumentController;
use App\Http\Controllers\Partner\SupportController;

Route::prefix('partner')->name('partner.')->middleware(['auth', 'partner.access'])->group(function () {

    // ─────────────────────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────────────────────
    Route::get('/dashboard',       [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/print', [DashboardController::class, 'print'])->name('dashboard.print');


    // ─────────────────────────────────────────────────────────
    // PROFILE  (overview / completion status)
    // ─────────────────────────────────────────────────────────
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',    [ProfileController::class, 'show'])->name('show');
        Route::get('/edit',[ProfileController::class, 'edit'])->name('edit');
    });


    // ─────────────────────────────────────────────────────────
    // 1. BASIC COMPANY INFORMATION
    // ─────────────────────────────────────────────────────────
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/',                          [CompanyController::class, 'show'])->name('show');
        Route::get('/edit',                      [CompanyController::class, 'edit'])->name('edit');
        Route::match(['put','patch'], '/',        [CompanyController::class, 'update'])->name('update');
    });


    // ─────────────────────────────────────────────────────────
    // 2. BRANDING & CONTENT
    //    Logo, Cover Image, Short/Full Description, Promo Video
    // ─────────────────────────────────────────────────────────
    Route::prefix('branding')->name('branding.')->group(function () {
        Route::get('/',                          [BrandingController::class, 'show'])->name('show');
        Route::get('/edit',                      [BrandingController::class, 'edit'])->name('edit');
        Route::match(['put','patch'], '/',        [BrandingController::class, 'update'])->name('update');
        Route::post('/logo',                     [BrandingController::class, 'uploadLogo'])->name('upload-logo');
        Route::delete('/logo',                   [BrandingController::class, 'deleteLogo'])->name('delete-logo');
        Route::post('/cover',                    [BrandingController::class, 'uploadCover'])->name('upload-cover');
        Route::delete('/cover',                  [BrandingController::class, 'deleteCover'])->name('delete-cover');
    });


    // ─────────────────────────────────────────────────────────
    // 3. CONTACT PERSON DETAILS
    // ─────────────────────────────────────────────────────────
    Route::prefix('contact')->name('contact.')->group(function () {
        Route::get('/',                          [ContactController::class, 'show'])->name('show');
        Route::get('/edit',                      [ContactController::class, 'edit'])->name('edit');
        Route::match(['put','patch'], '/',        [ContactController::class, 'update'])->name('update');
    });


    // ─────────────────────────────────────────────────────────
    // 4. SOCIAL MEDIA PROFILES
    // ─────────────────────────────────────────────────────────
    Route::prefix('social')->name('social.')->group(function () {
        Route::get('/',                          [SocialController::class, 'show'])->name('show');
        Route::get('/edit',                      [SocialController::class, 'edit'])->name('edit');
        Route::match(['put','patch'], '/',        [SocialController::class, 'update'])->name('update');
    });


    // ─────────────────────────────────────────────────────────
    // 5. BUSINESS TYPE & CATEGORY
    //    Industry, Business Type, Services Offered
    // ─────────────────────────────────────────────────────────
    Route::prefix('business')->name('business.')->group(function () {
        Route::get('/',                          [BusinessController::class, 'show'])->name('show');
        Route::get('/edit',                      [BusinessController::class, 'edit'])->name('edit');
        Route::match(['put','patch'], '/',        [BusinessController::class, 'update'])->name('update');
    });


    // ─────────────────────────────────────────────────────────
    // 6. OPERATIONS & PRESENCE
    //    Countries, Branches, Target Market
    // ─────────────────────────────────────────────────────────
    Route::prefix('operations')->name('operations.')->group(function () {
        Route::get('/',                          [OperationsController::class, 'show'])->name('show');
        Route::get('/edit',                      [OperationsController::class, 'edit'])->name('edit');
        Route::match(['put','patch'], '/',        [OperationsController::class, 'update'])->name('update');
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
    // NOTIFICATIONS
    // ─────────────────────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',                  [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/mark-read',   [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read',    [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}',           [NotificationController::class, 'destroy'])->name('destroy');
    });


    // ─────────────────────────────────────────────────────────
    // SETTINGS
    // ─────────────────────────────────────────────────────────
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/',                          [SettingsController::class, 'index'])->name('index');
        Route::get('/general',                   [SettingsController::class, 'general'])->name('general');
        Route::post('/general',                  [SettingsController::class, 'updateGeneral'])->name('update-general');
        Route::get('/security',                  [SettingsController::class, 'security'])->name('security');
        Route::post('/security/password',        [SettingsController::class, 'updatePassword'])->name('update-password');
        Route::get('/notifications',             [SettingsController::class, 'notifications'])->name('notifications');
        Route::post('/notifications',            [SettingsController::class, 'updateNotifications'])->name('update-notifications');
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
