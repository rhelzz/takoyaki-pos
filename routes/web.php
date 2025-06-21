<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Protected routes
Route::middleware('auth')->group(function () {
    
    // Dashboard - All authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Transaction Receipt - All authenticated users can view receipts
    Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])
        ->name('transactions.receipt');
    
    // Cashier - Admin, Manager, Cashier
    Route::middleware(['role:admin,manager,cashier'])->group(function () {
        Route::get('/cashier', [CashierController::class, 'index'])->name('cashier');
        Route::post('/cashier/process', [CashierController::class, 'processTransaction'])->name('cashier.process');
        Route::get('/cashier/product/{id}', [CashierController::class, 'getProduct'])->name('cashier.product');
        Route::get('/cashier/receipt/{transactionCode}', [CashierController::class, 'getTransactionReceipt'])->name('cashier.receipt');
    });
    
    // Categories & Products - Admin, Manager only
    Route::middleware(['role:admin,manager'])->group(function () {
        // Categories Routes
        Route::resource('categories', CategoryController::class);
        Route::patch('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])
            ->name('categories.toggle-status');
        
        // Products Routes
        Route::resource('products', ProductController::class);
        Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
            ->name('products.toggle-status');
        Route::delete('/products/bulk-delete', [ProductController::class, 'bulkDelete'])
            ->name('products.bulk-delete');
    });
    
    // Transactions - Admin, Manager only
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    });
    
    // Users - Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Delete transaction - Admin only, same day only
        Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    });
    
    // Reports - Admin, Manager only
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/daily', [ReportController::class, 'dailyReport'])->name('daily');
            Route::get('/busiest-hours', [ReportController::class, 'busiestHours'])->name('busiest-hours');
            Route::get('/best-selling', [ReportController::class, 'bestSellingProducts'])->name('best-selling');
            Route::get('/financial', [ReportController::class, 'financialReport'])->name('financial');
            Route::get('/export/daily', [ReportController::class, 'exportDaily'])->name('export.daily');
        });
    });
});