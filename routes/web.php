<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POS\SaleController;
use App\Http\Controllers\POS\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return redirect()->route('pos.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/pos', [DashboardController::class, 'index'])->name('pos.index');
    Route::post('/pos', [SaleController::class, 'store'])->name('pos.store');
    
    Route::resource('products', ProductController::class)->except(['show']);
    
    Route::post('/logout', function () {
        auth()->logout();
        return redirect('/');
    })->name('logout');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);