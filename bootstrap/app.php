<?php

use App\Domain\IncomeTransactions\Services\IncomeTransactionService;
use App\Domain\Transactions\Services\TransactionService;
use App\Facades\Toast;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Configure redirect for authenticated users
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo('/dashboard');

        // Register middleware aliases
        $middleware->alias([
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
            'plan.feature' => \App\Http\Middleware\CheckPlanFeature::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Mark overdue transactions (expenses) - runs daily at midnight
        $schedule->call(function () {
            app(TransactionService::class)->markOverdueTransactions();
        })->daily()->name('mark-overdue-transactions');

        // Mark overdue income transactions - runs daily at midnight
        $schedule->call(function () {
            app(IncomeTransactionService::class)->markOverdueIncomeTransactions();
        })->daily()->name('mark-overdue-income-transactions');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Bugsnag::attach($exceptions);

        // Handle authorization exceptions with toast notifications
        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Você não tem permissão para realizar esta ação.',
                ], 403);
            }

            Toast::error('Você não tem permissão para realizar esta ação.')
                ->title('Acesso Negado')
                ->flash();

            return redirect()->back();
        });
    })->create();
