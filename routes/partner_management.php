<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PartnerManagementController;

// Partner Management Routes
Route::group(['middleware' => ['auth:admin', 'role:admin']], function () {
    Route::get('/', [PartnerManagementController::class, 'index'])->name('index');
    Route::get('/create', [PartnerManagementController::class, 'create'])->name('create');
    Route::post('/', [PartnerManagementController::class, 'store'])->name('store');
    Route::get('/{partner}', [PartnerManagementController::class, 'show'])->name('show');
    Route::get('/{partner}/edit', [PartnerManagementController::class, 'edit'])->name('edit');
    Route::put('/{partner}', [PartnerManagementController::class, 'update'])->name('update');
    Route::delete('/{partner}', [PartnerManagementController::class, 'destroy'])->name('destroy');
    
    // Deal Assignment
    Route::get('/{partner}/assign-deals', [PartnerManagementController::class, 'assignDealsForm'])->name('assign-deals.form');
    Route::post('/{partner}/assign-deals', [PartnerManagementController::class, 'assignDeals'])->name('assign-deals');
    Route::delete('/{partner}/deals/{deal}', [PartnerManagementController::class, 'removeDeal'])->name('remove-deal');
});
