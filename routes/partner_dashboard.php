<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Partner\PartnerController;
use App\Http\Controllers\Admin\DealController;
use App\Http\Controllers\Admin\DealClassController;
use App\Http\Controllers\Admin\DealDocumentController;
use App\Http\Controllers\Admin\InvestmentController;
use App\Http\Controllers\Admin\AssetsController;
use App\Http\Controllers\Admin\WaterFallController;
use App\Http\Controllers\Admin\DistributionsController;
use App\Http\Controllers\Admin\ESignTemplateController;
use App\Http\Controllers\Admin\InvestorController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Middleware\PartnerDealAccess;
use App\Http\Controllers\Admin\AdminStripeACHController;


/*
|--------------------------------------------------------------------------
| Partner Dashboard Routes
|--------------------------------------------------------------------------
|
| These routes define the partner dashboard functionality. They are completely
| separate from the admin routes and provide a dedicated interface for partners.
|
*/

// Partner Dashboard
Route::get('dashboard', [PartnerController::class, 'dashboard'])->name('dashboard');

// Partner Profile
Route::get('profile', [PartnerController::class, 'showProfile'])->name('profile');
Route::post('profile/update', [PartnerController::class, 'updateProfile'])->name('profile.update');
Route::post('password/update', [PartnerController::class, 'updatePassword'])->name('password.update');


// Partner Deal Management - using Admin controllers directly while staying in /partner/ URL context
Route::group(['prefix' => 'deals'], function () {
    // Main deal routes

    Route::get('/{deal}/class', [DealController::class, 'class'])->name('deals.class');
    Route::get('/{deal}/summary', [DealController::class, 'showSummary'])->name('deals.summary');
    Route::post('/{deal}/classes', [DealController::class, 'storeClasses'])->name('deals.class.store');
    Route::get('/', [DealController::class, 'index'])->name('deals.index');
    Route::get('/create', [DealController::class, 'create'])->name('deals.create');
    Route::post('/', [DealController::class, 'store'])->name('deals.store');
    Route::get('/{deal}', [DealController::class, 'show'])->name('deals.show');
    Route::get('/{deal}/edit', [DealController::class, 'edit'])->name('deals.edit');
    // Route::put('/{deal}', [DealController::class, 'update'])->name('deals.update');
    Route::delete('/{deal}', [DealController::class, 'destroy'])->name('deals.destroy');

    Route::get('/{deal}/edit/entity-detail', [DealController::class, 'EntityDetail'])->name('deals.edit.EntityDetail');
    Route::post('/{deal}/entity-detail-store', [DealController::class, 'entityDetailStore'])->name('deals.entityDetailStore');

    Route::post('/{deal}/edit/store-verify-entity', [DealController::class, 'storeAchSetting'])->name('deals.edit.storeAchSetting');
    Route::post('/{deal}/edit/store-address', [DealController::class, 'storeAddress'])->name('deals.edit.storeAddress');
    Route::post('/{deal}/edit/beneficial-detail', [DealController::class, 'storeBeneficialOwnerDetail'])->name('deals.edit.storeBeneficialOwnerDetail');
    Route::post('/{deal}/edit/beneficial-detail/{beneficial}', [DealController::class, 'updateBeneficialOwnerDetail'])->name('deals.edit.updateBeneficialOwnerDetail');
    Route::delete('/{deal}/edit/beneficial-detail/delete-beneficial', [DealController::class, 'destroyBeneficial'])->middleware('permission:deals.delete')->name('deals.edit.destroyBeneficial');
    Route::post('/update/{deal}', [DealController::class, 'update'])->name('deals.update');
    Route::post('/setting/{deal}', [DealController::class, 'setting'])->name('deals.setting');
    Route::post('/personal/{deal}', [DealController::class, 'personal'])->name('deals.personal');
    Route::post('/{deal}/storesetting', [DealController::class, 'storeSetting'])->name('deals.storeSetting');
    Route::post('/{deal}/storesetting/storesenderaddress', [DealController::class, 'storesenderaddress'])->name('deals.storesenderaddress');
    Route::post('/{deal}/storesetting/storesenderaddress/storebankaccount', [DealController::class, 'storebankaccount'])->name('deals.storebankaccount');
    Route::get('/history', [DealController::class, 'history'])->name('deals.history');

    Route::post('/deals/{deal}/approve-entity', [DealController::class, 'approveEntity'])->name('deals.approveEntity');
    // Deal class routes
    Route::get('/{deal}/classes', [DealClassController::class, 'index'])->name('deals.classes.index');
    Route::get('/{deal}/classes/create', [DealClassController::class, 'create'])->name('deals.classes.create');

    // Deal document routes
    // Route::get('/{deal}/documents', [DealDocumentController::class, 'index'])->name('deals.documents.index');
    // Route::post('/{deal}/documents', [DealDocumentController::class, 'store'])->name('deals.documents.store');

    Route::get('{deal}/documents', [DocumentController::class, 'index'])->name('admin.documents.index');
    Route::get('{deal}/documents/create', [DocumentController::class, 'create'])->name('admin.documents.create');
    Route::post('{deal}/documents', [DocumentController::class, 'store'])->name('admin.documents.store');
    Route::get('{deal}/documents/{id}/edit', [DocumentController::class, 'edit'])->name('admin.documents.edit');
    Route::put('{deal}/documents/{id}', [DocumentController::class, 'update'])->name('admin.documents.update');
    Route::delete('{deal}/documents/{id}', [DocumentController::class, 'destroy'])->name('admin.documents.destroy');
    // Deal distribution routes

    // Deal investment routes
    Route::get('/{deal}/investments', [InvestmentController::class, 'index'])->name('deals.investments.index');

    // Deal Waterfalls Routes
    Route::post('/{deal}/waterfalls', [WaterFallController::class, 'storeWaterfall'])->name('waterfalls.store');
    Route::post('/{deal}/waterfall/new', [WaterFallController::class, 'storeNewWaterfall'])->name('waterfalls.new.store');
    Route::post('/{deal}/waterfall/default', [WaterFallController::class, 'setDefaultWaterfall'])->name('deals.waterfalls.default');
    Route::delete('/{deal}/waterfalls/{waterfall}', [WaterFallController::class, 'destroy'])->name('deals.waterfalls.destroy');

    Route::post('/{deal}/offerings', [DealController::class, 'storeOffering'])->name('deals.offerings.store');
    Route::get('/{deal}/offerings/{offering}/offering-detail', [DealController::class, 'showOfferingDetail'])->name('deals.offerings.offering_detail');
    Route::get('/{deal}/offerings/{offering}/offering-preview', [DealController::class, 'showOfferingDetailPreview'])->name('deals.offerings.offering_preview');
    Route::get('/{deal}/offerings/{offering}/offering-manage', [DealController::class, 'showOfferingManage'])->name('deals.offerings.offering_manage');
    Route::post('/offerings/{offering}/offering-manage/manage-offering', [DealController::class, 'storeManageOffering'])->name('deals.offerings.storeManageOffering');
    Route::post('/{deal}/distributions', [DealController::class, 'storedistribution'])->name('deals.distributions.store');
    // Update Offering
    Route::post('/{deal}/offerings/{offering}/update', [DealController::class, 'updateOffering'])->name('deals.offerings.update');
    Route::post('/{deal}/offerings/{offering}/funding', [DealController::class, 'createFundingInfo'])->name('deals.offerings.funding');
    Route::post('/{deal}/offerings/{offering}/keySection', [DealController::class, 'createKeySection'])->name('deals.offerings.keySection');
    Route::post('/{deal}/offerings/{offering}/createMetric', [DealController::class, 'saveMetric'])->name('deals.offerings.createMetric');
    Route::delete('/offerings/{offering}/delMetric/{id}', [DealController::class, 'delKeyMetric'])->name('deals.offerings.delMetric');
    Route::get('/{deal}/assets/{asset}/asset-detail', [DealController::class, 'showAssetDetail'])->name('deals.assets.asset_detail');
    Route::post('/{deal}/members', [DealController::class, 'storeMember'])->name('deals.storeMember');
    Route::delete('/members/{member}', [DealController::class, 'destroyMember'])->name('deals.destroyMember');
    Route::post('/offerings/{offering}/insight', [DealController::class, 'updateOfferingInsight'])->name('deals.offerings.insight.update');
    Route::delete('/{deal}/offerings/{offering}/delete', [DealController::class, 'destroyOffering'])->name('deals.offerings.destroyOffering');

    // DealClass Routes
    Route::get('/{deal}/class/{class}', [DealClassController::class, 'showClass'])->name('deals.class.showClass');

    // Investments Routes
    Route::post('/{deal}/investments', [InvestmentController::class, 'store'])->name('investments.store');
    Route::get('/investments/create', [InvestmentController::class, 'create'])->name('investments.create');
    Route::delete('/investments/{id}', [InvestmentController::class, 'deleteInvestment'])->name('investments.deleteInvestment');

    // Distribution Routes
    Route::get('distributions', [DistributionsController::class, 'index'])->name('distributions.index');
    Route::post('distributions', [DistributionsController::class, 'store'])->name('distributions.store');
    Route::delete('distributions/{id}', [DistributionsController::class, 'destroy'])->name('distributions.destroy');
    Route::post('distributions/toggle-visibility/{id}', [DistributionsController::class, 'toggleVisibility'])->name('distributions.toggleVisibility');

    Route::get('/{deal}/distributions', [DistributionController::class, 'index'])->name('deals.distributions.index');
    Route::get('/{deal}/distributions/create', [DistributionController::class, 'create'])->name('deals.distributions.create');
    // Document Routes
    Route::post('documents', [DealDocumentController::class, 'storeDocument'])->name('document.store');
    Route::post('documents/storeLink', [DealDocumentController::class, 'storeLink'])->name('document.storeLink');
    Route::post('documents/{id}/rename', [DealDocumentController::class, 'rename'])->name('document.rename');
    Route::delete('documents/delete/{id}', [DealDocumentController::class, 'destroy'])->name('document.destroy');
    Route::get('documents/view/{id}', [DealDocumentController::class, 'view'])->name('document.view');
    Route::post('documents/sections', [DealDocumentController::class, 'storeSection'])->name('document.storeSection');
    Route::post('documents/{id}/renameSection', [DealDocumentController::class, 'renameSection'])->name('document.renameSection');
    Route::delete('documents/deleteSection/{id}', [DealDocumentController::class, 'destroySection'])->name('document.destroySection');

    // Members Routes
    Route::post('/{deal}/members', [DealController::class, 'storeMember'])->name('deals.storeMember');
    Route::delete('/{deal}/member/{member}', [DealController::class, 'destroyMember'])->name('deals.destroyMember');

    // Investor Routes
    Route::post('/{deal}/investors/profiles', [InvestorController::class, 'storeInvestorProfile'])->name('investors.profiles.store');
    Route::post('/{deal}/investors', [InvestorController::class, 'storeInvestor'])->name('investors.store');
    Route::get('investors/create', [InvestorController::class, 'create'])->name('investors.create');
    Route::post('investors/store-tag', [InvestorController::class, 'storeInvestorTag'])->name('investors.storeInvestorTag');

    Route::post('ESignTemplates/template', [ESignTemplateController::class, 'uploadTemplate'])->name('ESignTemplates.uploadTemplate');
    Route::delete('esigntemplate/template/{template}', [ESignTemplateController::class, 'deleteTemplate'])->name('ESignTemplates.deleteTemplate');
    Route::post('esigntemplate/{template}', [ESignTemplateController::class, 'updateTemplate'])->name('ESignTemplates.updateTemplate');
    Route::post('save-fields-to-document', [ESignTemplateController::class, 'saveFieldsToDocument'])->name('ESignTemplates.saveFields');
    Route::get('esigntemplate/template/{template}/view', [ESignTemplateController::class, 'viewTemplate'])->name('ESignTemplates.viewTemplate');

    // ACH Initiate for specific deal
    Route::post('/deals/{deal}/stripe/ach/initiate', [AdminStripeACHController::class, 'initiateACH'])
        ->name('stripe.ach.initiate');

    // Verify micro-deposits
    Route::post('/stripe/ach/verify', [AdminStripeACHController::class, 'verifyMicroDeposits'])
        ->name('stripe.ach.verify');

    // Refresh the Stripe onboarding link
    Route::get('/stripe/refresh', [AdminStripeACHController::class, 'refresh'])
        ->name('stripe.onboarding.refresh');

    // Stripe redirect return URL after onboarding
    Route::get('/stripe/ach/return', [AdminStripeACHController::class, 'return'])
        ->name('ach.return');

    // Re-initiate onboarding for specific deal
    Route::post('/deals/{deal}/againOnboarding', [AdminStripeACHController::class, 'againOnboarding'])
        ->name('againOnboarding');

});
// Asset Routes
Route::group(['prefix' => 'assets'], function () {
    // Route::get('/', [AssetsController::class, 'index'])->name('assets');
    // Route::get('/create', [AssetsController::class, 'create'])->name('assets.create');
    // Route::post('/', [AssetsController::class, 'store'])->name('assets.store');
    // Route::get('/{id}/edit', [AssetsController::class, 'edit'])->name('assets.edit');
    // Route::post('/update/{id}', [AssetsController::class, 'update'])->name('assets.update');
    // Route::delete('/delete/{id}', [AssetsController::class, 'destroy'])->name('assets.destroy');
    // Route::get('/history', [AssetsController::class, 'history'])->name('assets.history');
    // Route::post('/update/offering/{offering}', [AssetsController::class, 'updateOfferingAssets'])->name('assets.update.offering');
    // Route::post('/update/{asset}', [AssetsController::class, 'updateAsset'])->name('offering.assets.update');
    // Route::post('/upload/images/{asset}', [AssetsController::class, 'uploadAssetMedia'])->name('offering.assets.upload');
    // Route::post('/remove/images/{asset}', [AssetsController::class, 'deleteAssetMedia'])->name('offering.assets.remove-media');

    Route::get('', [AssetsController::class, 'index'])->name('assets');
    Route::get('create', [AssetsController::class, 'create'])->name('assets.create');
    Route::post('', [AssetsController::class, 'store'])->name('assets.store');
    Route::get('{id}/edit', [AssetsController::class, 'edit'])->name('assets.edit');
    // Route::post('update/{id}', [AssetsController::class, 'update'])->name('assets.update');
    Route::delete('delete/{id}', [AssetsController::class, 'destroy'])->name('assets.destroy');
    Route::get('history', [AssetsController::class, 'history'])->name('assets.history');
    Route::post('update/offering/{offering}', [AssetsController::class, 'updateOfferingAssets'])->name('assets.update.offering');
    Route::post('update/{asset}', [AssetsController::class, 'updateAsset'])->name('offering.assets.update');
    Route::post('upload/images/{asset}', [AssetsController::class, 'uploadAssetMedia'])->name('offering.assets.upload');
    Route::post('remove/images/{asset}', [AssetsController::class, 'deleteAssetMedia'])->name('offering.assets.remove-media');
});
