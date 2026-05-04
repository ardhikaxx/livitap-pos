<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\POS\SaleController;

Route::get('/pos', function () {
    return view('pos.index');
})->middleware(['auth']);

Route::post('/pos', [SaleController::class, 'store'])->name('pos.store')->middleware(['auth']);
