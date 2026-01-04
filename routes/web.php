<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToastTestController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\WebhookController;
use Inertia\Inertia;

use App\Http\Controllers\LandingPageController;

Route::get('/', LandingPageController::class)->name('home');

// Webhook routes (public, no auth)
Route::post('/webhook/asaas', [WebhookController::class, 'asaas'])->name('webhook.asaas');
Route::get('/webhook/health', [WebhookController::class, 'healthCheck'])->name('webhook.health');
Route::post('/webhook/test', [WebhookController::class, 'test'])->name('webhook.test');

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

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('reset-password', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
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
        Route::post('transactions', [\App\Http\Controllers\Dashboard\TransactionsController::class, 'store'])->name('transactions.store');
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

        // Alert routes
        Route::get('alerts', [\App\Http\Controllers\Dashboard\AlertsController::class, 'index'])->name('alerts.index');
        Route::post('alerts', [\App\Http\Controllers\Dashboard\AlertsController::class, 'store'])->name('alerts.store');
        Route::patch('alerts/{alert}', [\App\Http\Controllers\Dashboard\AlertsController::class, 'update'])->name('alerts.update');
        Route::delete('alerts/{alert}', [\App\Http\Controllers\Dashboard\AlertsController::class, 'destroy'])->name('alerts.destroy');
        Route::patch('alerts/{alert}/toggle-status', [\App\Http\Controllers\Dashboard\AlertsController::class, 'toggleStatus'])->name('alerts.toggle-status');

        // Reconciliation routes
        Route::get('reconciliation', [\App\Http\Controllers\Dashboard\ReconciliationController::class, 'index'])->name('reconciliation.index');
        Route::post('reconciliation/upload', [\App\Http\Controllers\Dashboard\ReconciliationController::class, 'upload'])->name('reconciliation.upload');
        Route::post('reconciliation/{transaction}/reconcile', [\App\Http\Controllers\Dashboard\ReconciliationController::class, 'reconcile'])->name('reconciliation.reconcile');

        // Notification routes
        Route::get('notifications', [\App\Http\Controllers\Dashboard\NotificationsController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{notification}/read', [\App\Http\Controllers\Dashboard\NotificationsController::class, 'markAsRead'])->name('notifications.read');
        Route::post('notifications/read-all', [\App\Http\Controllers\Dashboard\NotificationsController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::delete('notifications/{notification}', [\App\Http\Controllers\Dashboard\NotificationsController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('notifications', [\App\Http\Controllers\Dashboard\NotificationsController::class, 'deleteAllRead'])->name('notifications.delete-all-read');

        // User Profile routes
        Route::post('profile/cpf', [\App\Http\Controllers\Dashboard\UserProfileController::class, 'updateCpf'])->name('profile.cpf.update');
        Route::get('profile/cpf/check', [\App\Http\Controllers\Dashboard\UserProfileController::class, 'hasCpf'])->name('profile.cpf.check');

        // Budget routes
        Route::resource('budgets', \App\Http\Controllers\Dashboard\BudgetController::class)->except(['create', 'edit', 'show']);

        // Report routes
        Route::get('reports', [\App\Http\Controllers\Dashboard\ReportController::class, 'index'])->name('reports.index');

        // Export routes
        Route::prefix('exports')->as('exports.')->group(function () {
            Route::post('/transactions', [\App\Http\Controllers\Dashboard\ExportsController::class, 'transactions'])
                ->name('transactions');
            Route::post('/incomes', [\App\Http\Controllers\Dashboard\ExportsController::class, 'incomes'])
                ->name('incomes');
            Route::post('/accounts', [\App\Http\Controllers\Dashboard\ExportsController::class, 'accounts'])
                ->name('accounts');
            Route::post('/budgets', [\App\Http\Controllers\Dashboard\ExportsController::class, 'budgets'])
                ->name('budgets');
        });

        // Subscription routes
        Route::prefix('subscription')->as('subscription.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Dashboard\SubscriptionController::class, 'index'])
                ->name('index');
            Route::get('/plans', [\App\Http\Controllers\Dashboard\SubscriptionController::class, 'plans'])
                ->name('plans');
            Route::post('/subscribe/{planSlug}', [\App\Http\Controllers\Dashboard\SubscriptionController::class, 'subscribe'])
                ->name('subscribe');
            Route::delete('/cancel', [\App\Http\Controllers\Dashboard\SubscriptionController::class, 'cancel'])
                ->name('cancel');
            Route::post('/resume', [\App\Http\Controllers\Dashboard\SubscriptionController::class, 'resume'])
                ->name('resume');
            Route::post('/upgrade/{planSlug}', [\App\Http\Controllers\Dashboard\SubscriptionController::class, 'upgrade'])
                ->name('upgrade');
            Route::post('/downgrade/{planSlug}', [\App\Http\Controllers\Dashboard\SubscriptionController::class, 'downgrade'])
                ->name('downgrade');
            Route::get('/preview/{planSlug}', [\App\Http\Controllers\Dashboard\SubscriptionController::class, 'previewChange'])
                ->name('preview');
        });

        // Payment routes
        Route::prefix('payment')->as('payment.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Dashboard\PaymentController::class, 'index'])
                ->name('index');
            Route::get('/choose-method', [\App\Http\Controllers\Dashboard\PaymentController::class, 'choosePaymentMethod'])
                ->name('choose-method');
            Route::post('/create', [\App\Http\Controllers\Dashboard\PaymentController::class, 'createPayment'])
                ->name('create');
            Route::get('/{payment:uuid}', [\App\Http\Controllers\Dashboard\PaymentController::class, 'show'])
                ->name('show');
            Route::get('/{payment:uuid}/success', [\App\Http\Controllers\Dashboard\PaymentController::class, 'success'])
                ->name('success');
            Route::get('/{payment:uuid}/check-status', [\App\Http\Controllers\Dashboard\PaymentController::class, 'checkStatus'])
                ->name('check-status');
            Route::delete('/{payment:uuid}/cancel', [\App\Http\Controllers\Dashboard\PaymentController::class, 'cancel'])
                ->name('cancel');
        });
    });
});
