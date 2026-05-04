<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\User\StripeACHController;

Route::
        namespace('User\Auth')->name('user.')->middleware('guest')->group(function () {
            Route::controller('LoginController')->group(function () {
                Route::get('/login', 'showLoginForm')->name('login');
                Route::post('/login', 'login');
                Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
            });

            Route::controller('RegisterController')->group(function () {
                Route::get('register', 'showRegistrationForm')->name('register');
                Route::post('register', 'register');
                Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
            });

            Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
                Route::get('reset', 'showLinkRequestForm')->name('request');
                Route::post('email', 'sendResetCodeEmail')->name('email');
                Route::get('code-verify', 'codeVerify')->name('code.verify');
                Route::post('verify-code', 'verifyCode')->name('verify.code');
            });

            Route::controller('ResetPasswordController')->group(function () {
                Route::post('password/reset', 'reset')->name('password.update');
                Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
            });

            Route::controller('SocialiteController')->group(function () {
                Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
                Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
            });
        });

Route::get('public/offering/{encryptedId}', function ($encryptedId) {
    $id = Crypt::decryptString($encryptedId);
    $offering = App\Models\Offering::findOrFail($id);
    $pageTitle = 'Offering Detail';
    return view('templates.basic.user.deals.offerings.offering', compact('offering', 'pageTitle'));
})->name('public.offering');

Route::middleware('auth')->name('user.')->group(function () {
    Route::get('user-data', 'User\UserController@userData')->name('data');
    Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
    });

    Route::middleware(['check.status', 'registration.complete'])->group(function () {
        Route::namespace('User')->group(function () {
            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');
                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');
                Route::get('referrals', 'referrals')->name('referrals');
                Route::any('profit/history', 'profitHistory')->name('profit.history');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });


            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::middleware('kyc')->group(function () {
                    Route::get('/', 'withdrawMoney');
                    Route::post('/', 'withdrawStore')->name('.money');
                    Route::get('preview', 'withdrawPreview')->name('.preview');
                    Route::post('preview', 'withdrawSubmit')->name('.submit');
                });
                Route::get('history', 'withdrawLog')->name('.history');
            });

            Route::controller('InvestController')->prefix('invest')->name('invest.')->group(function () {
                Route::post('store/{id}', 'store')->name('store');
                Route::get('installment/details/{id}', 'installmentDetails')->name('installment.details');
                Route::post('installment/pay/{id}/{installmentId}', 'installmentPay')->name('installment.pay');
                Route::get('history', 'investHistory')->name('history');
                Route::get('payment', 'gatewayPayment')->name('gateway.payment');
                Route::post('payment/insert', 'gatewayPaymentInsert')->name('payment.insert');
                Route::get('installment/payment', 'installmentGatewayPayment')->name('installment.gateway.payment');
                Route::get('contract/download/{id}', 'downloadContract')->name('contract.download');
            });
            Route::controller('StripeACHController')->group(function () {
                Route::post('/investments/{id}/stripe/ach/initiate',  'initiateACH')->name('stripe.ach.initiate');
                Route::post('/stripe/ach/verify',  'verifyMicroDeposits')->name('stripe.ach.verify');
                Route::get('/stripe/refresh', 'refresh')->name('stripe.onboarding.refresh');
                Route::get('/stripe/return',  'return')->name('stripe.return');
                Route::post('/investments/{id}/initiate-ach-payment',  'initiateACHPayment')->middleware('auth')->name('initiateACHPayment');
                Route::get('/check-bank-account', 'checkBankAccount')->middleware('auth')->name('checkBankAccount');

            });

            Route::controller('UserDealController')->group(function () {
                Route::get('deals/my-deals', 'mydeals')->name('deals.mydeals');


                Route::post('deals/{deal}/classes', 'storeClasses')->name('deals.class.store');
                // Add new Offering to a Deal
                Route::post('deals/{deal}/offerings', 'storeOffering')->name('deals.offerings.store');
                Route::post('deals/{deal}/distributions', 'storedistribution')->name('deals.distributions.store');
            });
            Route::controller('DealDashboardController')->group(function () {
                Route::get('Dashboard2', 'dashboard2')->name('dashboards.dashboard2');
            });
            Route::controller('OfferingDetailController')->group(function () {
                Route::get('offering/{offering}', 'Offering')->name('offerings.offering');
                Route::get('offering/{offering}/invest', 'investNow')->name('offerings.investNow');
                Route::get('stripe-success/on-boarding', 'stripeSuccess')->name('stripe.success');
                Route::post('offering/{offering}/invest/store-profile', 'storeProfile')->name('offerings.invest.storeProfile');
                Route::post('offering/{offering}/invest/store-investment', 'storeInvestment')->name('offerings.invest.storeInvestment');
                Route::post('/investment/{id}', 'updateInvestment')->name('investment.updateInvestment');
                Route::post('offering/{offering}/invest/store-questionnaire', 'storeQuestionnaire')->name('offerings.invest.storeQuestionnaire');
                Route::post('offering/{offering}/invest/store-questionnaire-address', 'storeQuestionnaireAddress')->name('offerings.invest.storeQuestionnaireAddress');
                Route::post('offering/{offering}/invest/store-questionnaire-form', 'storeQuestionnaireForm')->name('offerings.invest.storeQuestionnaireForm');
                Route::post('/investment/{id}/upload-invoice', 'uploadInvoice')->name('investment.uploadInvoice');
                Route::post('download-invoice', 'downloadInvoice')->name('downloadInvoice');


            });
            // Route::post('/download-invoice', [InvoiceController::class, 'downloadInvoice'])->name('downloadInvoice');

            // Asset Routes
            Route::controller('AssetsController')->group(function () {
                Route::get('assets', 'index')->name('assets');
                Route::get('assets/create', 'create')->name('assets.create');
                Route::post('assets', 'store')->name('assets.store');
                Route::get('assets/{id}/edit', 'edit')->name('assets.edit');
                Route::post('assets/update/{id}', 'update')->name('assets.update');
                Route::delete('assets/delete/{id}', 'destroy')->name('assets.destroy');
                Route::get('assets/history', 'history')->name('assets.history');
            });

        });



        // Payment
        Route::prefix('deposit')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
            Route::any('/', 'deposit')->name('index');
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
        });
    });
});
