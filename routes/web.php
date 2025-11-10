<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToastTestController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleLoginController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::prefix('toast-test')->group(function () {
    Route::get('/', [ToastTestController::class, 'index'])->name('toast-test.index');
    Route::post('/success', [ToastTestController::class, 'success'])->name('toast-test.success');
    Route::post('/error', [ToastTestController::class, 'error'])->name('toast-test.error');
    Route::post('/warning', [ToastTestController::class, 'warning'])->name('toast-test.warning');
    Route::post('/info', [ToastTestController::class, 'info'])->name('toast-test.info');
    Route::post('/loading', [ToastTestController::class, 'loading'])->name('toast-test.loading');
    Route::post('/with-title', [ToastTestController::class, 'withTitle'])->name('toast-test.with-title');
    Route::post('/with-description', [ToastTestController::class, 'withDescription'])->name('toast-test.with-description');
    Route::post('/persistent', [ToastTestController::class, 'persistent'])->name('toast-test.persistent');
    Route::post('/non-dismissible', [ToastTestController::class, 'nonDismissible'])->name('toast-test.non-dismissible');
    Route::post('/with-actions', [ToastTestController::class, 'withActions'])->name('toast-test.with-actions');
    Route::post('/with-progress', [ToastTestController::class, 'withProgress'])->name('toast-test.with-progress');
    Route::post('/custom-duration', [ToastTestController::class, 'customDuration'])->name('toast-test.custom-duration');
    Route::post('/multiple-toasts', [ToastTestController::class, 'multipleToasts'])->name('toast-test.multiple-toasts');
    Route::post('/validation', [ToastTestController::class, 'validationExample'])->name('toast-test.validation');
    Route::post('/mark-read', [ToastTestController::class, 'markRead'])->name('toast-test.mark-read');
    Route::post('/clear', [ToastTestController::class, 'clearToasts'])->name('toast-test.clear');
    Route::post('/complex', [ToastTestController::class, 'complexExample'])->name('toast-test.complex');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');

    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');;

    Route::get('auth/google', [GoogleLoginController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('auth/google/callback', [GoogleLoginController::class, 'callback'])->name('auth.google.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::as('dashboard.')->prefix('dashboard')->group(function () {
        Route::get('/', \App\Http\Controllers\Dashboard\HomeController::class)->name('home');

        // Wallets routes
        Route::resource('wallets', \App\Http\Controllers\Dashboard\WalletsController::class)->except(['show']);

        // Category routes
        Route::resource('categories', \App\Http\Controllers\Dashboard\CategoriesController::class)->except(['show']);
        Route::patch('categories/{category}/toggle-status', [\App\Http\Controllers\Dashboard\CategoriesController::class, 'updateStatus'])->name('categories.toggle-status');

        // Account routes
        Route::resource('accounts', \App\Http\Controllers\Dashboard\AccountsController::class);
        Route::patch('accounts/{account}/toggle-status', [\App\Http\Controllers\Dashboard\AccountsController::class, 'updateStatus'])->name('accounts.toggle-status');

        // Transaction routes
        Route::get('transactions', [\App\Http\Controllers\Dashboard\TransactionsController::class, 'index'])->name('transactions.index');
        Route::get('transactions/{year}/{month}', [\App\Http\Controllers\Dashboard\TransactionsController::class, 'month'])->name('transactions.month');
        Route::get('transactions/{transaction}', [\App\Http\Controllers\Dashboard\TransactionsController::class, 'show'])->name('transactions.show');
        Route::patch('transactions/{transaction}/mark-as-paid', [\App\Http\Controllers\Dashboard\TransactionsController::class, 'markAsPaid'])->name('transactions.mark-as-paid');
        Route::patch('transactions/{transaction}/mark-as-unpaid', [\App\Http\Controllers\Dashboard\TransactionsController::class, 'markAsUnpaid'])->name('transactions.mark-as-unpaid');

        // Income routes
        Route::resource('incomes', \App\Http\Controllers\Dashboard\IncomesController::class);
        Route::patch('incomes/{income}/toggle-status', [\App\Http\Controllers\Dashboard\IncomesController::class, 'updateStatus'])->name('incomes.toggle-status');

        // Income Transaction routes
        Route::get('income-transactions', [\App\Http\Controllers\Dashboard\IncomeTransactionsController::class, 'index'])->name('income-transactions.index');
        Route::get('income-transactions/{year}/{month}', [\App\Http\Controllers\Dashboard\IncomeTransactionsController::class, 'month'])->name('income-transactions.month');
        Route::get('income-transactions/{incomeTransaction}', [\App\Http\Controllers\Dashboard\IncomeTransactionsController::class, 'show'])->name('income-transactions.show');
        Route::patch('income-transactions/{incomeTransaction}/mark-as-received', [\App\Http\Controllers\Dashboard\IncomeTransactionsController::class, 'markAsReceived'])->name('income-transactions.mark-as-received');
        Route::patch('income-transactions/{incomeTransaction}/mark-as-not-received', [\App\Http\Controllers\Dashboard\IncomeTransactionsController::class, 'markAsNotReceived'])->name('income-transactions.mark-as-not-received');
    });
});
