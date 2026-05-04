<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\DealController;
use App\Http\Controllers\Admin\DealClassController;
use App\Http\Controllers\Admin\DealDocumentController;
use App\Http\Controllers\Admin\DistributionController;
use App\Http\Controllers\Admin\InvestmentController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\ESignTemplateController;

/*
|--------------------------------------------------------------------------
| Partner Admin Routes
|--------------------------------------------------------------------------
|
| These routes define the admin functionality accessible to partners.
| They use the admin controllers but with access control to ensure
| partners can only see their own deals.
|
*/

// Partner Dashboard
Route::get('dashboard', [PartnerController::class, 'dashboard'])->name('dashboard');

// Partner Profile Management
Route::get('profile', [PartnerController::class, 'showProfile'])->name('profile');
Route::post('profile', [PartnerController::class, 'updateProfile'])->name('profile.update');

// Partner Deal Management - using existing DealController with access control
Route::group(['prefix' => 'deals', 'middleware' => ['partner.deal.access']], function () {
    // Main deal routes
    Route::get('/', [DealController::class, 'index'])->name('deals.index');
    Route::get('/create', [DealController::class, 'create'])->name('deals.create');
    Route::post('/', [DealController::class, 'store'])->name('deals.store');
    Route::get('/{deal}', [DealController::class, 'show'])->name('deals.show');
    Route::get('/{deal}/edit', [DealController::class, 'edit'])->name('deals.edit');
    Route::put('/{deal}', [DealController::class, 'update'])->name('deals.update');
    // Route::delete('/{deal}', [DealController::class, 'destroy'])->name('deals.destroy');
    
    // Deal class routes
    Route::get('/{deal}/classes', [DealClassController::class, 'index'])->name('deals.classes.index');
    Route::get('/{deal}/classes/create', [DealClassController::class, 'create'])->name('deals.classes.create');
    Route::post('/{deal}/classes', [DealClassController::class, 'store'])->name('deals.classes.store');
    Route::get('/{deal}/classes/{class}', [DealClassController::class, 'show'])->name('deals.classes.show');
    Route::get('/{deal}/classes/{class}/edit', [DealClassController::class, 'edit'])->name('deals.classes.edit');
    Route::put('/{deal}/classes/{class}', [DealClassController::class, 'update'])->name('deals.classes.update');
    Route::delete('/{deal}/classes/{class}', [DealClassController::class, 'destroy'])->name('deals.classes.destroy');
    
    // Deal document routes
    Route::get('/{deal}/documents', [DealDocumentController::class, 'index'])->name('deals.documents.index');
    Route::get('/{deal}/documents/create', [DealDocumentController::class, 'create'])->name('deals.documents.create');
    Route::post('/{deal}/documents', [DealDocumentController::class, 'store'])->name('deals.documents.store');
    Route::get('/{deal}/documents/{document}', [DealDocumentController::class, 'show'])->name('deals.documents.show');
    Route::get('/{deal}/documents/{document}/edit', [DealDocumentController::class, 'edit'])->name('deals.documents.edit');
    Route::put('/{deal}/documents/{document}', [DealDocumentController::class, 'update'])->name('deals.documents.update');
    Route::delete('/{deal}/documents/{document}', [DealDocumentController::class, 'destroy'])->name('deals.documents.destroy');
    
    // Distribution routes
    Route::get('/{deal}/distributions', [DistributionController::class, 'index'])->name('deals.distributions.index');
    Route::get('/{deal}/distributions/create', [DistributionController::class, 'create'])->name('deals.distributions.create');
    Route::post('/{deal}/distributions', [DistributionController::class, 'store'])->name('deals.distributions.store');
    Route::get('/{deal}/distributions/{distribution}', [DistributionController::class, 'show'])->name('deals.distributions.show');
    Route::get('/{deal}/distributions/{distribution}/edit', [DistributionController::class, 'edit'])->name('deals.distributions.edit');
    Route::put('/{deal}/distributions/{distribution}', [DistributionController::class, 'update'])->name('deals.distributions.update');
    Route::delete('/{deal}/distributions/{distribution}', [DistributionController::class, 'destroy'])->name('deals.distributions.destroy');
    
    // Investment routes
    Route::get('/{deal}/investments', [InvestmentController::class, 'index'])->name('deals.investments.index');
    Route::get('/{deal}/investments/create', [InvestmentController::class, 'create'])->name('deals.investments.create');
    Route::post('/{deal}/investments', [InvestmentController::class, 'store'])->name('deals.investments.store');
    Route::get('/{deal}/investments/{investment}', [InvestmentController::class, 'show'])->name('deals.investments.show');
    Route::get('/{deal}/investments/{investment}/edit', [InvestmentController::class, 'edit'])->name('deals.investments.edit');
    Route::put('/{deal}/investments/{investment}', [InvestmentController::class, 'update'])->name('deals.investments.update');
    Route::delete('/{deal}/investments/{investment}', [InvestmentController::class, 'destroy'])->name('deals.investments.destroy');
});

// Document Management
Route::group(['prefix' => 'documents'], function () {
    Route::get('/', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::put('/{document}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
});

// E-Signature Templates
Route::group(['prefix' => 'esign-templates'], function () {
    Route::get('/', [ESignTemplateController::class, 'index'])->name('esign-templates.index');
    Route::get('/create', [ESignTemplateController::class, 'create'])->name('esign-templates.create');
    Route::post('/', [ESignTemplateController::class, 'store'])->name('esign-templates.store');
    Route::get('/{template}', [ESignTemplateController::class, 'show'])->name('esign-templates.show');
    Route::get('/{template}/edit', [ESignTemplateController::class, 'edit'])->name('esign-templates.edit');
    Route::put('/{template}', [ESignTemplateController::class, 'update'])->name('esign-templates.update');
    Route::delete('/{template}', [ESignTemplateController::class, 'destroy'])->name('esign-templates.destroy');
});
