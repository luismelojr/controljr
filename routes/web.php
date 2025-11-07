<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToastTestController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
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
});
