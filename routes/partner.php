<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Partner Authentication Routes
|--------------------------------------------------------------------------
|
| These routes handle partner authentication only. All partner functionality
| after login is handled by the admin_partner.php routes file.
|
*/

// Partner Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('login', [LoginController::class, 'login'])->name('login');

// Logout route
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('deal/{deal}/summary', [DealController::class, 'summary'])->name('deal.summary');
