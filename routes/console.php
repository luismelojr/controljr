<?php

use App\Domain\Alerts\Services\AlertService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Alert system scheduled tasks
Schedule::call(function () {
    app(AlertService::class)->checkCreditCardUsageAlerts();
})->hourly()->name('check-credit-card-usage-alerts');

Schedule::call(function () {
    app(AlertService::class)->checkBillDueDateAlerts();
})->dailyAt('09:00')->name('check-bill-due-date-alerts');

Schedule::call(function () {
    app(AlertService::class)->checkAccountBalanceAlerts();
})->dailyAt('08:00')->name('check-account-balance-alerts');

Schedule::call(function () {
    app(AlertService::class)->checkBudgetAlerts();
})->dailyAt('10:00')->name('check-budget-alerts');
