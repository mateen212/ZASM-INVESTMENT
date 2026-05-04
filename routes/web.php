<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Partner\Auth\LoginController;
use App\Http\Controllers\Partner\Auth\SocialiteController;
use App\Http\Controllers\Partner\PartnerController;
use App\Http\Controllers\Partner\ForgotPasswordController;
use App\Http\Controllers\Partner\ResetPasswordController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\ESignTemplateController;

Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('cron', 'CronController@cron')->name('cron');
// Partner Portal Routes
Route::name('partner.')->prefix('partner')->group(function () {
    // Auth Routes
    Route::controller(LoginController::class)->group(function () {
        Route::get('/', 'showLoginForm')->name('login');
        Route::post('/', 'login');
        Route::get('logout', 'logout')->name('logout');
    });
    
    // Social Login
    Route::controller(SocialiteController::class)->group(function () {
        Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
        Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
    });
    
    // Partner Registration
    Route::controller(RegisterController::class)->group(function () {
        Route::get('onboarding', 'showPartnerRegistrationForm')->name('register');
        Route::post('onboarding', 'registerPartner');
    });
    
    // Authenticated Partner Routes
    Route::middleware(['web', 'auth:admin', 'role:partner'])->group(function () {
        // Dashboard
        Route::get('dashboard', [PartnerController::class, 'dashboard'])->name('dashboard');
        
        // Profile
        Route::get('profile', [PartnerController::class, 'showProfile'])->name('profile');
        Route::post('profile/update', [PartnerController::class, 'updateProfile'])->name('profile.update');
        
        // Password
        Route::get('password', [PartnerController::class, 'showPasswordForm'])->name('password');
        Route::post('password/update', [PartnerController::class, 'updatePassword'])->name('password.update');
        
        // Deals
        Route::get('deals', [PartnerController::class, 'deals'])->name('deals.index');
        Route::get('deals/{deal}', [PartnerController::class, 'showDeal'])->name('deals.show');


    });
});

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});
Route::get('public/offering/{encryptedId}', 'Admin\DealController@showPublicPreview')->name('public.offering');
Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');
    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');
    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');
    Route::get('blog', 'blogs')->name('blog');
    Route::get('blog/{slug}', 'blogDetails')->name('blog.details');
    Route::get('properties', 'property')->name('property');
    Route::get('deals', 'deals')->name('deals');
    Route::get('offering/{offering}', 'offering')->name('offering');
    Route::get('property/{slug}', 'propertyDetails')->name('property.details');
    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');
    Route::post('/subscribe', 'addSubscriber')->name('subscribe');
    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode','maintenance')->withoutMiddleware('maintenance')->name('maintenance');
    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});
// Route::controller('WebhookController')->group(function () {
//     Route::post('webhooks/documenso', 'handleWebhook')->name('webhooks.documenso');
//     Route::get('webhooks/documenso', 'handleWebhook')->name('webhooks.documenso');
// });