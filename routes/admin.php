<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PartnerManagementController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\PlaidController;

Route::
        namespace('Auth')->group(function () {
            Route::middleware('admin.guest')->group(function () {
                Route::controller('LoginController')->group(function () {
                    Route::get('/', 'showLoginForm')->name('login');
                    Route::post('/', 'login')->name('login');
                    Route::get('logout', 'logout')->middleware('admin')->withoutMiddleware('admin.guest')->name('logout');
                });

                // Admin Password Reset
                Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
                    Route::get('reset', 'showLinkRequestForm')->name('reset');
                    Route::post('reset', 'sendResetCodeEmail');
                    Route::get('code-verify', 'codeVerify')->name('code.verify');
                    Route::post('verify-code', 'verifyCode')->name('verify.code');
                });

                Route::controller('ResetPasswordController')->group(function () {
                    Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
                    Route::post('password/reset/change', 'reset')->name('password.change');
                });
            });
        });

Route::middleware('admin')->group(function () {
    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('chart/deposit-withdraw', 'depositAndWithdrawReport')->name('chart.deposit.withdraw');
        Route::get('chart/transaction', 'transactionReport')->name('chart.transaction');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');

        // Partner Management Routes
        Route::group(['prefix' => 'partner-management'], function () {  // Temporarily removed role:admin middleware
            Route::get('/', [PartnerManagementController::class, 'index'])->name('partner-management.index');
            Route::get('/create', [PartnerManagementController::class, 'create'])->name('partner-management.create');
            Route::post('/', [PartnerManagementController::class, 'store'])->name('partner-management.store');
            Route::delete('/{partner}', [PartnerManagementController::class, 'destroy'])->name('partner-management.destroy')->middleware('permission:partnerships.delete');
            Route::get('/{partner}', [PartnerManagementController::class, 'show'])->name('partner-management.show');
            Route::get('/{partner}/edit', [PartnerManagementController::class, 'edit'])->name('partner-management.edit');
            Route::put('/{partner}', [PartnerManagementController::class, 'update'])->name('partner-management.update');

            // Status Toggle
            Route::get('/{partner}/status', [PartnerManagementController::class, 'toggleStatus'])->name('partner-management.status');

            // Deal Assignment
            Route::get('/{partner}/assign-deals', [PartnerManagementController::class, 'assignDealsForm'])->name('partner-management.assign-deals.form');
            Route::post('/{partner}/assign-deals', [PartnerManagementController::class, 'assignDeals'])->name('partner-management.assign-deals');
            Route::delete('/{partner}/deals/{deal}', [PartnerManagementController::class, 'removeDeal'])->name('partner-management.remove-deal');
        });

        // Partner Portal Routes
        Route::group(['prefix' => 'partner', 'middleware' => ['role:partner']], function () {
            Route::get('/dashboard', [PartnerController::class, 'dashboard'])->name('partner.dashboard');
            Route::get('/profile', [PartnerController::class, 'showProfile'])->name('partner.profile.index');
            Route::post('/profile', [PartnerController::class, 'updateProfile'])->name('partner.profile.update');

            // Partner Deal Management
            Route::group(['prefix' => 'deals', 'middleware' => ['partner.deal.access']], function () {
                Route::get('/', [PartnerController::class, 'deals'])->name('partner.deals.index');
                Route::get('/create', [PartnerController::class, 'createDeal'])->name('partner.deals.create');
                Route::post('/', [PartnerController::class, 'storeDeal'])->name('partner.deals.store');
                Route::get('/{deal}', [PartnerController::class, 'showDeal'])->name('partner.deals.show');
                Route::get('/{deal}/edit', [PartnerController::class, 'editDeal'])->name('partner.deals.edit');
                Route::put('/{deal}', [PartnerController::class, 'updateDeal'])->name('partner.deals.update');
                Route::delete('/{deal}', [PartnerController::class, 'destroyDeal'])->name('partner.deals.destroy');
            });
        });

        // Deal Routes
        Route::controller('DealController')->middleware('permission:deals.view')->group(function () {
            Route::get('deals/{deal}/class', 'class')->name('deals.class');
            Route::get('deals', 'index')->name('deals.index');
            Route::get('deals/create', 'create')->middleware('permission:deals.create')->name('deals.create');
            Route::post('deals', 'store')->middleware('permission:deals.create')->name('deals.store');
            Route::get('deals/{deal}/edit', 'edit')->middleware('permission:deals.edit')->name('deals.edit');
            Route::get('deals/{deal}/edit/entity-detail', 'EntityDetail')->middleware('permission:deals.edit')->name('deals.edit.EntityDetail');
            Route::post('deals/{deal}/entity-detail-store', 'entityDetailStore')->middleware('permission:deals.edit')->name('deals.entityDetailStore');
            Route::post('deals/{deal}/edit/store-verify-entity', 'storeAchSetting')->middleware('permission:deals.edit')->name('deals.edit.storeAchSetting');
            Route::post('deals/{deal}/approve-entity', 'approveEntity')->middleware('permission:deals.approveEntity')->name('deals.approveEntity');

            Route::post('deals/{deal}/edit/store-address', 'storeAddress')->middleware('permission:deals.edit')->name('deals.edit.storeAddress');
            Route::post('deals/{deal}/edit/beneficial-detail', 'storeBeneficialOwnerDetail')->middleware('permission:deals.edit')->name('deals.edit.storeBeneficialOwnerDetail');
            Route::post('deals/{deal}/edit/beneficial-detail/{beneficial}', 'updateBeneficialOwnerDetail')->middleware('permission:deals.edit')->name('deals.edit.updateBeneficialOwnerDetail');
            Route::delete('deals/{deal}/edit/beneficial-detail/delete-beneficial', 'destroyBeneficial')->middleware('permission:deals.delete')->name('deals.edit.destroyBeneficial');
            Route::post('deals/update/{deal}', 'update')->middleware('permission:deals.edit')->name('deals.update');
            Route::post('deals/setting/{deal}', 'setting')->middleware('permission:deals.edit')->name('deals.setting');
            Route::post('deals/personal/{deal}', 'personal')->middleware('permission:deals.edit')->name('deals.personal');
            Route::post('deals/{deal}/storesetting', 'storeSetting')->middleware('permission:deals.edit')->name('deals.storeSetting');
            Route::post('deals/{deal}/storesetting/storesenderaddress', 'storesenderaddress')->middleware('permission:deals.edit')->name('deals.storesenderaddress');
            Route::post('deals/{deal}/storesetting/storesenderaddress/storebankaccount', 'storebankaccount')->middleware('permission:deals.edit')->name('deals.storebankaccount');
            Route::delete('deals/{deal}', 'destroy')->middleware('permission:deals.delete')->name('deals.destroy');
            Route::get('deals/history', 'history')->name('deals.history');
            Route::get('deals/{deal}/summary', 'showSummary')->name('deals.summary');

            Route::post('deals/{deal}/classes', 'storeClasses')->middleware('permission:deals.edit')->name('deals.class.store');
            // Add new Offering to a Deal
            Route::post('deals/{deal}/offerings', 'storeOffering')->name('deals.offerings.store');
            Route::get('deals/{deal}/offerings/{offering}/offering-detail', 'showOfferingDetail')->name('deals.offerings.offering_detail');
            Route::get('deals/{deal}/offerings/{offering}/offering-preview', 'showOfferingDetailPreview')->name('deals.offerings.offering_preview');
            Route::get('deals/{deal}/offerings/{offering}/offering-manage', 'showOfferingManage')->name('deals.offerings.offering_manage');
            Route::post('deals/offerings/{offering}/offering-manage/manage-offering', 'storeManageOffering')->name('deals.offerings.storeManageOffering');
            Route::post('deals/{deal}/distributions', 'storedistribution')->name('deals.distributions.store');
            // Update Offering
            Route::post('deals/{deal}/offerings/{offering}/update', 'updateOffering')->name('deals.offerings.update');
            Route::post('deals/{deal}/offerings/{offering}/funding', 'createFundingInfo')->name('deals.offerings.funding');
            Route::post('deals/{deal}/offerings/{offering}/keySection', 'createKeySection')->name('deals.offerings.keySection');
            Route::post('deals/{deal}/offerings/{offering}/createMetric', 'saveMetric')->name('deals.offerings.createMetric');
            Route::delete('deals/offerings/{offering}/delMetric/{id}', 'delKeyMetric')->name('deals.offerings.delMetric');
            Route::get('deals/{deal}/assets/{asset}/asset-detail', 'showAssetDetail')->name('deals.assets.asset_detail');
            // Member Routes
            Route::post('deals/{deal}/members', 'storeMember')->name('deals.storeMember');
            Route::post('deals/{deal}/members/{member}/update', 'updateMember')->name('deals.updateMember');
            Route::delete('deals/{deal}/member/{member}', 'destroyMember')->name('deals.destroyMember');
            Route::post('deals/offerings/{offering}/insight', 'updateOfferingInsight')->name('deals.offerings.insight.update');
            Route::delete('deals/{deal}/offerings/{offering}/delete', 'destroyOffering')->name('deals.offerings.destroyOffering');
        });
        Route::controller('DealClassController')->group(function () {
            Route::get('deals/{deal}/class/{class}', 'showClass')->name('deals.class.showClass');
        });
        Route::controller('StripeWebhookController')->group(function () {
           Route::post('/stripe/webhook', 'handleWebhook')->name('handleWebhook');
        });
        //Waterfall routes
        Route::controller('WaterFallController')->group(function () {
            Route::post('deals/{deal}/waterfalls', 'storeWaterfall')->name('waterfalls.store');
            Route::post('deals/{deal}/waterfall/new', 'storeNewWaterfall')->name('waterfalls.new.store');
            Route::post('deals/{deal}/waterfall/default', 'setDefaultWaterfall')->name('deals.waterfalls.default');
            Route::delete('deals/{deal}/waterfalls/{waterfall}', 'destroy')->name('deals.waterfalls.destroy');
        });

        Route::controller('DealDocumentController')->group(function () {
            Route::post('documents', 'storeDocument')->name('document.store');
            Route::post('documents/storeLink', 'storeLink')->name('document.storeLink');
            Route::post('documents{id}/rename', 'rename')->name('document.rename');
            Route::delete('documents/delete/{id}', 'destroy')->name('document.destroy');
            Route::get('documents/view/{id}', 'view')->name('document.view');
            Route::post('documents/sections', 'storeSection')->name('document.storeSection');
            Route::post('documents{id}/renameSection', 'renameSection')->name('document.renameSection');
            Route::delete('documents/deleteSection/{id}', 'destroySection')->name('document.destroySection');
        });
        // e_sign_template route
        // e_sign_template routes
        Route::controller('ESignTemplateController')->group(function () {
            Route::post('esigntemplate/template', 'uploadTemplate')->name('ESignTemplates.uploadTemplate');
            Route::delete('esigntemplate/template/{template}', 'deleteTemplate')->name('ESignTemplates.deleteTemplate');
            Route::post('esigntemplate/{template}', 'updateTemplate')->name('ESignTemplates.updateTemplate');
            Route::post('save-fields-to-document', 'saveFieldsToDocument')->name('ESignTemplates.saveFields');
            Route::get('esigntemplate/template/{template}/view', 'viewTemplate')->name('ESignTemplates.viewTemplate');
        });

        // Distribution routes
        Route::controller('DistributionsController')->group(function () {
            Route::get('distributions', 'index')->name('distributions.index');
            Route::post('distributions', 'store')->name('distributions.store');
            Route::delete('distributions/{id}', 'destroy')->name('distributions.destroy');
            Route::post('distributions/toggle-visibility/{id}', 'toggleVisibility')->name('distributions.toggleVisibility');
        });

        //Investment routes
        Route::controller('InvestmentController')->group(function () {
            Route::post('deals/{deal}/investments', 'store')->name('investments.store');
            Route::get('investments/create', 'create')->name('investments.create');
            Route::delete('investments/{id}', 'deleteInvestment')->name('investments.deleteInvestment');
        });
        //Investor routes
        Route::controller('InvestorController')->group(function () {
            Route::post('deals/{deal}/investors/profiles', 'storeInvestorProfile')->name('investors.profiles.store');
            Route::post('deals/{deal}/investors', 'storeInvestor')->name('investors.store');
            Route::get('investors/create', 'create')->name('investors.create');
            Route::post('investors/store-tag', 'storeInvestorTag')->name('investors.storeInvestorTag');
        });
        //Document routes
        Route::controller('DocumentController')->group(function () {
            Route::get('deals/{deal}/documents', 'index')->name('admin.documents.index');
            Route::get('deals/{deal}/documents/create', 'create')->name('admin.documents.create');
            Route::post('deals/{deal}/documents', 'store')->name('admin.documents.store');
            Route::get('deals/{deal}/documents/{id}/edit', 'edit')->name('admin.documents.edit');
            Route::put('deals/{deal}/documents/{id}', 'update')->name('admin.documents.update');
            Route::delete('deals/{deal}/documents/{id}', 'destroy')->name('admin.documents.destroy');
        });
        Route::controller('AdminStripeACHController')->group(function () {
            Route::post('/deals/{deal}/stripe/ach/initiate', 'initiateACH')->name('stripe.ach.initiate');
            Route::post('/stripe/ach/verify', 'verifyMicroDeposits')->name('stripe.ach.verify');
            Route::get('/stripe/refresh', 'refresh')->name('stripe.onboarding.refresh');
            Route::get('/stripe/ach/return', 'return')->name('ach.return');
            Route::post('/deals/{deal}/againOnboarding', 'againOnboarding')->name('againOnboarding');
        });

        // Class Hurdle Conditions routes
        // Asset Routes
        Route::controller('AssetsController')->group(function () {
            Route::get('assets', 'index')->name('assets');
            Route::get('assets/create', 'create')->name('assets.create');
            Route::post('assets', 'store')->name('assets.store');
            Route::get('assets/{id}/edit', 'edit')->name('assets.edit');
            // Route::post('assets/update/{id}', 'update')->name('assets.update');
            Route::delete('assets/delete/{id}', 'destroy')->name('assets.destroy');
            Route::get('assets/history', 'history')->name('assets.history');
            Route::post('assets/update/offering/{offering}', 'updateOfferingAssets')->name('assets.update.offering');
            Route::post('assets/update/{asset}', 'updateAsset')->name('offering.assets.update');
            Route::post('assets/upload/images/{asset}', 'uploadAssetMedia')->name('offering.assets.upload');
            Route::post('assets/remove/images/{asset}', 'deleteAssetMedia')->name('offering.assets.remove-media');
        });
        Route::get('/test', function () {
            return $data = \App\Models\Deal::find(1)->classes()->with('class_hurdles')->get();
        });


        //Notification
        Route::get('notifications', 'notifications')->name('notifications');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'readAllNotification')->name('notifications.read.all');
        Route::post('notifications/delete-all', 'deleteAllNotification')->name('notifications.delete.all');
        Route::post('notifications/delete-single/{id}', 'deleteSingleNotification')->name('notifications.delete.single');

        //Report Bugs
        Route::get('request-report', 'requestReport')->name('request.report');
        Route::post('request-report', 'reportSubmit');

        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
    });
    Route::get('/api/plaid/link-token', [PlaidController::class, 'createLinkToken']);
    Route::post('/api/plaid/exchange-token', [PlaidController::class, 'exchangeToken']);
    Route::post('/api/plaid/accounts', [PlaidController::class, 'getAccounts']);
    Route::post('/api/plaid/create-payment', [PlaidController::class, 'createPayment']);

    // Settings
    Route::controller('GeneralSettingController')->middleware('permission:settings.view')->group(function () {
        // System Settings
        Route::get('setting/system', 'systemSetting')->name('setting.system');
        Route::get('setting/general', 'general')->name('setting.general');
        Route::post('setting/general', 'generalUpdate');
        Route::get('setting/logo-icon', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'logoIconUpdate');
        Route::get('setting/system-configuration', 'systemConfiguration')->name('setting.system.configuration');
        Route::post('setting/system-configuration', 'systemConfigurationSubmit');
        Route::get('setting/custom-css', 'customCss')->name('setting.custom.css');
        Route::post('setting/custom-css', 'customCssSubmit');
        Route::get('setting/cookie', 'cookie')->name('setting.cookie');
        Route::post('setting/cookie', 'cookieSubmit');
        Route::get('setting/sitemap', 'sitemap')->name('setting.sitemap');
        Route::post('setting/sitemap', 'sitemapSubmit');
        Route::get('setting/robot', 'robot')->name('setting.robot');
        Route::post('setting/robot', 'robotSubmit');

        // Social Login Settings
        Route::get('setting/socialite/credentials', 'socialiteCredentials')->name('setting.socialite.credentials');
        Route::get('setting/socialite/credentials/{key}/update/status', 'updateSocialiteCredentialStatus')->name('setting.socialite.credentials.status');
        Route::post('setting/socialite/credentials/{key}/update', 'updateSocialiteCredential')->name('setting.socialite.credentials.update');
    });

    // Maintenance Mode - Separate route group to match expected route name pattern in settings.json
    Route::controller('GeneralSettingController')->middleware('permission:settings.view')->group(function () {
        Route::get('maintenance-mode', 'maintenanceMode')->name('maintenance.mode');
        Route::post('maintenance-mode', 'maintenanceModeSubmit');
    });

    // Staff Management
    Route::controller('StaffController')->name('staff.')->prefix('staff')->middleware('permission:staff.view')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->middleware('permission:staff.create')->name('create');
        Route::post('/', 'store')->middleware('permission:staff.create')->name('store');
        Route::get('/{staff}/edit', 'edit')->middleware('permission:staff.edit')->name('edit');
        Route::put('/{staff}', 'update')->middleware('permission:staff.edit')->name('update');
        Route::delete('/{staff}', 'destroy')->middleware('permission:staff.delete')->name('destroy');

        // Roles & Permissions
        Route::get('/roles', 'roles')->middleware('permission:roles.view')->name('roles');
        Route::post('/roles', 'storeRole')->middleware('permission:roles.create')->name('roles.store');
        Route::get('/roles/{role}/edit', 'editRole')->middleware('permission:roles.edit')->name('roles.edit');
        Route::put('/roles/{role}', 'updateRole')->middleware('permission:roles.edit')->name('roles.update');
        Route::delete('/roles/{role}', 'destroyRole')->middleware('permission:roles.delete')->name('roles.destroy');

        // Role Permissions
        Route::get('/roles/{role}/permissions', 'rolePermissions')->middleware('permission:roles.edit')->name('roles.permissions');
        Route::post('/roles/{role}/permissions', 'updateRolePermissions')->middleware('permission:roles.edit')->name('roles.permissions.update');
    });

    // Users Manager
    Route::controller('ManageUsersController')->name('users.')->prefix('users')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('email-verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email-unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('mobile-unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('kyc-unverified', 'kycUnverifiedUsers')->name('kyc.unverified');
        Route::get('kyc-pending', 'kycPendingUsers')->name('kyc.pending');
        Route::get('mobile-verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('with-balance', 'usersWithBalance')->name('with.balance');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
        Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
        Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('add-sub-balance/{id}', 'addSubBalance')->name('add.sub.balance');
        Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('list', 'list')->name('list');
        Route::get('count-by-segment/{methodName}', 'countBySegment')->name('segment.count');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');
    });

    // Subscriber
    Route::controller('SubscriberController')->prefix('subscriber')->name('subscriber.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('send-email', 'sendEmailForm')->name('send.email');
        Route::post('remove/{id}', 'remove')->name('remove');
        Route::post('send-email', 'sendEmail')->name('send.email');
    });

    // Deposit Gateway
    Route::name('gateway.')->prefix('gateway')->group(function () {
        // Automatic Gateway
        Route::controller('AutomaticGatewayController')->prefix('automatic')->name('automatic.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{code}', 'update')->name('update');
            Route::post('remove/{id}', 'remove')->name('remove');
            Route::post('status/{id}', 'status')->name('status');
        });


        // Manual Methods
        Route::controller('ManualGatewayController')->prefix('manual')->name('manual.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('new', 'create')->name('create');
            Route::post('new', 'store')->name('store');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });


    // DEPOSIT SYSTEM
    Route::controller('DepositController')->prefix('deposit')->name('deposit.')->group(function () {
        Route::get('all/{user_id?}', 'deposit')->name('list');
        Route::get('pending/{user_id?}', 'pending')->name('pending');
        Route::get('rejected/{user_id?}', 'rejected')->name('rejected');
        Route::get('approved/{user_id?}', 'approved')->name('approved');
        Route::get('successful/{user_id?}', 'successful')->name('successful');
        Route::get('initiated/{user_id?}', 'initiated')->name('initiated');
        Route::get('details/{id}', 'details')->name('details');
        Route::post('reject', 'reject')->name('reject');
        Route::post('approve/{id}', 'approve')->name('approve');
    });


    // WITHDRAW SYSTEM
    Route::name('withdraw.')->prefix('withdraw')->group(function () {

        Route::controller('WithdrawalController')->name('data.')->group(function () {
            Route::get('pending/{user_id?}', 'pending')->name('pending');
            Route::get('approved/{user_id?}', 'approved')->name('approved');
            Route::get('rejected/{user_id?}', 'rejected')->name('rejected');
            Route::get('all/{user_id?}', 'all')->name('all');
            Route::get('details/{id}', 'details')->name('details');
            Route::post('approve', 'approve')->name('approve');
            Route::post('reject', 'reject')->name('reject');
        });


        // Withdraw Method
        Route::controller('WithdrawMethodController')->prefix('method')->name('method.')->group(function () {
            Route::get('/', 'methods')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::post('edit/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });

    // Report
    Route::controller('ReportController')->prefix('report')->name('report.')->group(function () {
        Route::get('transaction/{user_id?}', 'transaction')->name('transaction');
        Route::get('login/history', 'loginHistory')->name('login.history');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory');
        Route::get('notification/history', 'notificationHistory')->name('notification.history');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
    });


    // Admin Support
    Route::controller('SupportTicketController')->prefix('ticket')->name('ticket.')->group(function () {
        Route::get('/', 'tickets')->name('index');
        Route::get('pending', 'pendingTicket')->name('pending');
        Route::get('closed', 'closedTicket')->name('closed');
        Route::get('answered', 'answeredTicket')->name('answered');
        Route::get('view/{id}', 'ticketReply')->name('view');
        Route::post('reply/{id}', 'replyTicket')->name('reply');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
        Route::post('delete/{id}', 'ticketDelete')->name('delete');
    });


    // Language Manager
    Route::controller('LanguageController')->prefix('language')->name('language.')->group(function () {
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
        Route::get('get-keys', 'getKeys')->name('get.key');
    });

    Route::controller('GeneralSettingController')->middleware('permission:settings.view')->group(function () {

        Route::get('setting', 'index')->name('setting.index');
        Route::post('setting', 'update')->middleware('permission:settings.edit')->name('setting.update');

        // Logo-Icon Setting
        Route::get('setting/logo-icon', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'logoIconUpdate')->middleware('permission:settings.edit')->name('setting.logo.icon.update');

        // Notification Setting
        Route::get('setting/notification', 'notification')->name('setting.notification');
        Route::post('setting/notification', 'notificationUpdate')->middleware('permission:settings.edit')->name('setting.notification.update');

        // Cookie Setting
        Route::get('setting/cookie', 'cookie')->name('setting.cookie');
        Route::post('setting/cookie', 'cookieSubmit')->middleware('permission:settings.edit')->name('setting.cookie.submit');

        // SEO Manager
        Route::get('setting/seo', 'seoIndex')->name('setting.seo.index');
        Route::post('setting/seo/update/{id}', 'seoUpdate')->middleware('permission:settings.edit')->name('setting.seo.update');
    });


    Route::controller('CronConfigurationController')->name('cron.')->prefix('cron')->group(function () {
        Route::get('index', 'cronJobs')->name('index');
        Route::post('store', 'cronJobStore')->name('store');
        Route::post('update', 'cronJobUpdate')->name('update');
        Route::post('delete/{id}', 'cronJobDelete')->name('delete');
        Route::get('schedule', 'schedule')->name('schedule');
        Route::post('schedule/store', 'scheduleStore')->name('schedule.store');
        Route::post('schedule/status/{id}', 'scheduleStatus')->name('schedule.status');
        Route::get('schedule/pause/{id}', 'schedulePause')->name('schedule.pause');
        Route::get('schedule/logs/{id}', 'scheduleLogs')->name('schedule.logs');
        Route::post('schedule/log/resolved/{id}', 'scheduleLogResolved')->name('schedule.log.resolved');
        Route::post('schedule/log/flush/{id}', 'logFlush')->name('log.flush');
    });


    // KYC setting
    Route::controller('KycController')->group(function () {
        Route::get('kyc-setting', 'setting')->name('kyc.setting');
        Route::post('kyc-setting', 'settingUpdate');
    });

    // Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notification')->group(function () {
        // Template Setting
        Route::get('global/email', 'globalEmail')->name('global.email');
        Route::post('global/email/update', 'globalEmailUpdate')->name('global.email.update');

        Route::get('global/sms', 'globalSms')->name('global.sms');
        Route::post('global/sms/update', 'globalSmsUpdate')->name('global.sms.update');

        Route::get('global/push', 'globalPush')->name('global.push');
        Route::post('global/push/update', 'globalPushUpdate')->name('global.push.update');

        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{type}/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{type}/{id}', 'templateUpdate')->name('template.update');

        // Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        // SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');

        Route::get('notification/push/setting', 'pushSetting')->name('push');
        Route::post('notification/push/setting', 'pushSettingUpdate');
        Route::post('notification/push/setting/upload', 'pushSettingUpload')->name('push.upload');
        Route::get('notification/push/setting/download', 'pushSettingDownload')->name('push.download');
    });

    // Plugin
    Route::controller('ExtensionController')->prefix('extensions')->name('extensions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });


    // System Information
    Route::controller('SystemController')->name('system.')->prefix('system')->group(function () {
        Route::get('info', 'systemInfo')->name('info');
        Route::get('server-info', 'systemServerInfo')->name('server.info');
        Route::get('optimize', 'optimize')->name('optimize');
        Route::get('optimize-clear', 'optimizeClear')->name('optimize.clear');
        Route::get('system-update', 'systemUpdate')->name('update');
        Route::post('system-update', 'systemUpdateProcess')->name('update.process');
        Route::get('system-update/log', 'systemUpdateLog')->name('update.log');
    });

    // Backup Management
    Route::controller('BackupController')->name('backup.')->prefix('backup')->middleware('permission:system.manage_backups')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create/database', 'createDatabaseBackup')->name('create.database');
        Route::get('create/application', 'createApplicationBackup')->name('create.application');
        Route::post('create/restore-point', 'createRestorePoint')->name('create.restore_point');
        Route::get('restore/{type}/{filename}', 'restore')->name('restore');
        Route::get('download/{type}/{filename}', 'download')->name('download');
        Route::post('delete/{type}/{filename}', 'delete')->name('delete');
    });


    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');


    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {

        Route::controller('FrontendController')->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key?}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::get('frontend-slug-check/{key}/{id?}', 'frontendElementSlugCheck')->name('sections.element.slug.check');
            Route::get('frontend-element-seo/{key}/{id}', 'frontendSeo')->name('sections.element.seo');
            Route::post('frontend-element-seo/{key}/{id}', 'frontendSeoUpdate');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::controller('PageBuilderController')->group(function () {
            Route::get('manage-pages', 'managePages')->name('manage.pages');
            Route::get('manage-pages/check-slug/{id?}', 'checkSlug')->name('manage.pages.check.slug');
            Route::post('manage-pages', 'managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete/{id}', 'managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'manageSectionUpdate')->name('manage.section.update');

            Route::get('manage-seo/{id}', 'manageSeo')->name('manage.pages.seo');
            Route::post('manage-seo/{id}', 'manageSeoStore');
        });
    });

    // API Integrations
    Route::name('other-apis.')->prefix('other-apis')->group(function () {
        Route::get('/', 'ApiIntegrationsController@index')->name('index');
        Route::get('/{code}/edit', 'ApiIntegrationsController@edit')->name('edit');
        Route::post('/{code}/update', 'ApiIntegrationsController@update')->name('update');
    });

    // property
    Route::prefix('manage')->name('manage.')->group(function () {
        // time setting
        Route::controller('TimeSettingController')->prefix('time')->name('time.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('store/{id?}', 'store')->name('store');
            Route::post('status/{id}', 'changeStatus')->name('status');
        });

        // location
        Route::controller('LocationController')->prefix('location')->name('location.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('store/{id?}', 'store')->name('store');
            Route::post('status/{id}', 'changeStatus')->name('status');
        });

        // property
        Route::controller('PropertyController')->prefix('properties')->name('property.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('active', 'activeProperty')->name('active');
            Route::get('invested', 'investedProperty')->name('invested');
            Route::get('create', 'create')->name('create');
            Route::post('store/{id?}', 'store')->name('store');
            Route::post('gallery/delete', 'GalleryDelete')->name('gallery.delete');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::post('status/{id}', 'changeStatus')->name('status');
            Route::get('check-slug/{id?}', 'checkSlug')->name('check.slug');
            Route::post('featured/status/{id}', 'changeFeaturedStatus')->name('featured.status');
        });

        // contract template
        Route::controller('ContractTemplateController')->prefix('contract')->name('contract.')->group(function () {
            Route::get('template', 'template')->name('template');
            Route::post('template/store', 'templateStore')->name('template.store');
        });
    });

    Route::prefix('investment')->name('invest.')->controller('InvestController')->middleware('permission:investments.view')->group(function () {
        Route::get('running', 'running')->name('running');
        Route::get('completed', 'completed')->name('completed');
        Route::get('all', 'all')->name('all');
        Route::get('details/{id}', 'investmentDetails')->name('details');
        Route::get('pending', 'pending')->name('pending');
        Route::get('rejected', 'rejected')->name('rejected');
        Route::get('approved', 'approved')->name('approved');
        Route::get('create', 'create')->middleware('permission:investments.create')->name('create');
        Route::post('store', 'store')->middleware('permission:investments.create')->name('store');
        Route::post('update/{id}', 'update')->middleware('permission:investments.edit')->name('update');
        Route::post('approve/{id}', 'approve')->middleware('permission:investments.edit')->name('approve');
        Route::post('reject/{id}', 'reject')->middleware('permission:investments.edit')->name('reject');
        Route::post('delete/{id}', 'delete')->middleware('permission:investments.delete')->name('delete');
        Route::get('installment/history', 'installment')->name('installment');
        Route::get('profit/history', 'profit')->name('profit');
        Route::get('profit/pending', 'pendingProfit')->name('profit.pending');
        Route::post('profit/discharge/preview/{id}', 'dischargePreview')->name('profit.discharge.preview');
        Route::post('profit/discharge/{id}', 'dischargeProfit')->name('profit.discharge');

        Route::prefix('report')->name('report.')->controller('InvestmentReportController')->group(function () {
            Route::get('dashboard', 'dashboard')->name('dashboard');
            Route::get('statistics', 'statistics')->name('statistics');
            Route::get('profit-invest', 'investProfitStatistics')->name('profit.invest');
        });
    });

    // referral
    Route::controller('ReferralController')->name('referrals.')->prefix('referrals')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'update')->name('update');
        Route::get('status/{id}', 'status')->name('status');
    });
});