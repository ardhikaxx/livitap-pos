<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POS\SaleController;
use App\Http\Controllers\POS\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes - LIVITAP POS
|--------------------------------------------------------------------------
| Semua endpoint prefixed /api/v1 dan menggunakan Sanctum auth
*/

// Public Auth Routes
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
});

// Protected Routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboardData']);

    // POS - Cart & Sales
    Route::post('/pos/cart/calculate', [SaleController::class, 'calculateCart']);
    Route::post('/pos/sale', [SaleController::class, 'store']);
    Route::get('/pos/sale/{sale}/receipt', [SaleController::class, 'receipt']);
    Route::post('/pos/sale/{sale}/void', [SaleController::class, 'void']);
    Route::post('/pos/hold', [SaleController::class, 'hold']);
    Route::get('/pos/holds', [SaleController::class, 'holds']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::post('/products/import', [ProductController::class, 'import']);

    // Stock Management
    Route::get('/stocks', [StockController::class, 'index']);
    Route::post('/stocks/adjustment', [StockController::class, 'adjust']);
    Route::post('/stocks/transfer', [StockController::class, 'transfer']);
    Route::post('/stocks/opname', [StockController::class, 'opname']);
    Route::post('/stocks/opname/{opname}/close', [StockController::class, 'closeOpname']);

    // Shift Management
    Route::post('/shifts/open', [ShiftController::class, 'open']);
    Route::get('/shifts/active', [ShiftController::class, 'active']);
    Route::post('/shifts/{shift}/close', [ShiftController::class, 'close']);
    Route::get('/shifts/{shift}', [ShiftController::class, 'show']);
    Route::get('/shifts/{shift}/report', [ShiftController::class, 'report']);

    // Customers
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{customer}', [CustomerController::class, 'update']);
    Route::get('/customers/{customer}/transactions', [CustomerController::class, 'transactions']);
    Route::get('/customers/{customer}/points', [CustomerController::class, 'points']);

    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales']);
    Route::get('/reports/products', [ReportController::class, 'products']);
    Route::get('/reports/stock', [ReportController::class, 'stock']);
    Route::get('/reports/cashier', [ReportController::class, 'cashier']);
    Route::get('/reports/shift/{shift}', [ReportController::class, 'shift']);
    Route::post('/reports/export', [ReportController::class, 'export']);

    // Discounts & Promotions
    Route::get('/discounts', [DiscountController::class, 'index']);
    Route::post('/discounts', [DiscountController::class, 'store']);
    Route::put('/discounts/{discount}', [DiscountController::class, 'update']);
    Route::delete('/discounts/{discount}', [DiscountController::class, 'destroy']);
    Route::post('/vouchers/validate', [DiscountController::class, 'validateVoucher']);

    // Settings (Business & Outlet)
    Route::get('/settings/business', [SettingsController::class, 'business']);
    Route::put('/settings/business', [SettingsController::class, 'updateBusiness']);
    Route::get('/settings/outlet', [SettingsController::class, 'outlet']);
    Route::put('/settings/outlet', [SettingsController::class, 'updateOutlet']);
    Route::get('/settings/receipt', [SettingsController::class, 'receipt']);
    Route::put('/settings/receipt', [SettingsController::class, 'updateReceipt']);

    // F&B - Tables & Kitchen
    Route::get('/tables', [TableController::class, 'index']);
    Route::patch('/tables/{table}/status', [TableController::class, 'updateStatus']);
    Route::post('/tables/merge', [TableController::class, 'merge']);
    Route::post('/tables/{table}/move-to/{target}', [TableController::class, 'move']);
    Route::get('/kitchen/orders', [KitchenController::class, 'orders']);
    Route::patch('/kitchen/orders/{kitchenOrder}/status', [KitchenController::class, 'updateStatus']);

    // Cash Management
    Route::post('/cash/in', [CashController::class, 'cashIn']);
    Route::post('/cash/out', [CashController::class, 'cashOut']);
});
