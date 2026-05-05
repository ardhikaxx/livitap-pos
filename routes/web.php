<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POS\DashboardController;
use App\Http\Controllers\POS\SaleController;
use App\Http\Controllers\POS\ShiftController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('pos.index');
})->name('home');

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Protected Routes
Route::middleware(['auth', 'check.business', 'set.outlet'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\ReportController::class, 'dashboard'])->name('dashboard');

    // Dashboard / POS
    Route::get('/pos', [DashboardController::class, 'index'])->name('pos.index');
    Route::get('/pos/transactions', [App\Http\Controllers\POS\TransactionController::class, 'index'])->name('pos.transactions.index');
    Route::post('/pos', [SaleController::class, 'store'])->name('pos.store');
    Route::get('/pos/{sale}/receipt', [SaleController::class, 'receipt'])->name('pos.receipt');
    Route::post('/pos/sale/{sale}/void', [SaleController::class, 'void'])->name('pos.void');
    Route::post('/pos/hold', [SaleController::class, 'hold'])->name('pos.hold');
    Route::get('/pos/holds', [SaleController::class, 'holds'])->name('pos.holds');
    Route::post('/pos/cart/calculate', [SaleController::class, 'calculateCart'])->name('pos.calculate');

    // Products Management
    Route::resource('products', ProductController::class);
    Route::resource('categories', App\Http\Controllers\CategoryController::class);
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');

    // Stock Management
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/{product}/adjust', [StockController::class, 'adjust'])->name('stocks.adjust');
    Route::post('/stocks/{product}/update', [StockController::class, 'updateStock'])->name('stocks.update');
    Route::post('/stocks/adjustment', [StockController::class, 'adjustStock'])->name('stocks.adjustment');
    Route::post('/stocks/transfer', [StockController::class, 'transferStock'])->name('stocks.transfer');
    Route::post('/stocks/opname', [StockController::class, 'opname'])->name('stocks.opname');
    Route::post('/stocks/opname/{opname}/close', [StockController::class, 'closeOpname'])->name('stocks.closeOpname');

    // Customer Management
    Route::resource('customers', CustomerController::class);
    Route::get('/customers/{customer}/transactions', [CustomerController::class, 'transactions'])->name('customers.transactions');
    Route::get('/customers/{customer}/points', [CustomerController::class, 'points'])->name('customers.points');

    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
    Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    Route::get('/reports/cashier', [ReportController::class, 'cashier'])->name('reports.cashier');
    Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Discounts & Vouchers
    Route::resource('discounts', DiscountController::class);
    Route::post('/discounts/validate-voucher', [DiscountController::class, 'validateVoucher'])->name('discounts.validateVoucher');

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/business', [SettingsController::class, 'business'])->name('business');
        Route::put('/business', [SettingsController::class, 'updateBusiness'])->name('business.update');
        Route::get('/outlet', [SettingsController::class, 'outlet'])->name('outlet');
        Route::put('/outlet', [SettingsController::class, 'updateOutlet'])->name('outlet.update');
        Route::get('/receipt', [SettingsController::class, 'receipt'])->name('receipt');
        Route::put('/receipt', [SettingsController::class, 'updateReceipt'])->name('receipt.update');
    });

    // Cash Management
    Route::post('/cash/in', [CashController::class, 'cashIn'])->name('cash.in');
    Route::post('/cash/out', [CashController::class, 'cashOut'])->name('cash.out');
});
