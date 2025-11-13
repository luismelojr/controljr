<?php

namespace App\Domain\Dashboard\Services;

use App\Enums\TransactionStatusEnum;
use App\Models\Transaction;
use App\Models\IncomeTransaction;
use App\Models\Wallet;
use App\Models\AlertNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get all dashboard data with caching
     */
    public function getDashboardData(string $userId): array
    {
        // Cache for 10 minutes (600 seconds)
        return Cache::remember("dashboard_user_{$userId}", 600, function () use ($userId) {
            return [
                'total_balance' => $this->getTotalBalance($userId),
                'balance_percentage_change' => $this->getBalancePercentageChange($userId),

                'monthly_expenses' => $this->getMonthlyExpenses($userId),
                'expenses_percentage_change' => $this->getExpensesPercentageChange($userId),

                'monthly_income' => $this->getMonthlyIncome($userId),
                'income_percentage_change' => $this->getIncomePercentageChange($userId),

                'cashflow_data' => $this->getCashflowData($userId),

                'upcoming_transactions' => $this->getUpcomingTransactions($userId),

                'recent_activities' => $this->getRecentActivities($userId),

                'wallets_summary' => $this->getWalletsSummary($userId),

                'accounts_ending_this_month' => $this->getAccountsEndingThisMonth($userId),

                'unread_notifications_count' => $this->getUnreadNotificationsCount($userId),
                'unread_notifications' => $this->getUnreadNotifications($userId),
            ];
        });
    }

    /**
     * Clear dashboard cache for user
     */
    public function clearCache(string $userId): void
    {
        Cache::forget("dashboard_user_{$userId}");
    }

    /**
     * Get total balance from all wallets
     */
    protected function getTotalBalance(string $userId): float
    {
        $wallets = Wallet::where('user_id', $userId)->get();

        return $wallets->sum(function ($wallet) {
            return $wallet->balance;
        });
    }

    /**
     * Get balance percentage change compared to last month
     */
    protected function getBalancePercentageChange(string $userId): float
    {
        $currentBalance = $this->getTotalBalance($userId);

        // Calculate balance at the end of last month
        // Last month balance = current balance - (this month income - this month expenses)
        $thisMonthExpenses = $this->getMonthlyExpenses($userId);
        $thisMonthIncome = $this->getMonthlyIncome($userId);

        $lastMonthBalance = $currentBalance - ($thisMonthIncome - $thisMonthExpenses);

        if ($lastMonthBalance == 0) {
            return 0;
        }

        return round((($currentBalance - $lastMonthBalance) / abs($lastMonthBalance)) * 100, 1);
    }

    /**
     * Get total expenses for current month
     */
    protected function getMonthlyExpenses(string $userId): float
    {
        $sumInCents = Transaction::where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PAID)
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');

        return $sumInCents / 100; // Convert cents to reais
    }

    /**
     * Get expenses percentage change compared to last month
     */
    protected function getExpensesPercentageChange(string $userId): float
    {
        $currentMonthExpenses = $this->getMonthlyExpenses($userId);

        $lastMonthExpensesInCents = Transaction::where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PAID)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->sum('amount');

        $lastMonthExpenses = $lastMonthExpensesInCents / 100; // Convert to reais

        if ($lastMonthExpenses == 0) {
            return 0;
        }

        return round((($currentMonthExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100, 1);
    }

    /**
     * Get total income for current month
     */
    protected function getMonthlyIncome(string $userId): float
    {
        $sumInCents = IncomeTransaction::where('user_id', $userId)
            ->where('is_received', true)
            ->whereYear('received_at', now()->year)
            ->whereMonth('received_at', now()->month)
            ->sum('amount');

        return $sumInCents / 100; // Convert cents to reais
    }

    /**
     * Get income percentage change compared to last month
     */
    protected function getIncomePercentageChange(string $userId): float
    {
        $currentMonthIncome = $this->getMonthlyIncome($userId);

        $lastMonthIncomeInCents = IncomeTransaction::where('user_id', $userId)
            ->where('is_received', true)
            ->whereYear('received_at', now()->subMonth()->year)
            ->whereMonth('received_at', now()->subMonth()->month)
            ->sum('amount');

        $lastMonthIncome = $lastMonthIncomeInCents / 100; // Convert to reais

        if ($lastMonthIncome == 0) {
            return 0;
        }

        return round((($currentMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100, 1);
    }

    /**
     * Get cashflow data for the last 6 months
     */
    protected function getCashflowData(string $userId): array
    {
        $months = [];
        $expenses = [];
        $incomes = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            // Month name in Portuguese short format
            $monthNames = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            $months[] = $monthNames[$date->month - 1];

            // Get expenses for this month (in cents, convert to reais)
            $monthExpensesInCents = Transaction::where('user_id', $userId)
                ->where('status', TransactionStatusEnum::PAID)
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('amount');
            $expenses[] = $monthExpensesInCents / 100;

            // Get income for this month (in cents, convert to reais)
            $monthIncomeInCents = IncomeTransaction::where('user_id', $userId)
                ->where('is_received', true)
                ->whereYear('received_at', $date->year)
                ->whereMonth('received_at', $date->month)
                ->sum('amount');
            $incomes[] = $monthIncomeInCents / 100;
        }

        return [
            'months' => $months,
            'expenses' => $expenses,
            'incomes' => $incomes,
        ];
    }

    /**
     * Get upcoming transactions (next 7 days)
     */
    protected function getUpcomingTransactions(string $userId): array
    {
        return Transaction::with(['category', 'wallet', 'account'])
            ->where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PENDING)
            ->whereBetween('due_date', [now(), now()->addDays(30)])
            ->orderBy('due_date', 'asc')
            ->limit(7)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->uuid,
                    'name' => $transaction->account->name ?? 'Sem nome',
                    'category' => $transaction->category->name ?? 'Sem categoria',
                    'due_date' => $transaction->due_date->format('d/m/Y'),
                    'due_date_raw' => $transaction->due_date->format('Y-m-d'),
                    'amount' => $transaction->amount,
                    'status' => $transaction->status->value,
                    'installment_info' => $transaction->total_installments > 1
                        ? "{$transaction->installment_number}/{$transaction->total_installments}"
                        : null,
                ];
            })
            ->toArray();
    }

    /**
     * Get recent activities (last 7 transactions + income transactions)
     */
    protected function getRecentActivities(string $userId): array
    {
        // Get recent paid transactions
        $transactions = Transaction::with(['category', 'wallet', 'account'])
            ->where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PAID)
            ->orderBy('paid_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'name' => $transaction->account->name ?? 'Despesa',
                    'accountNumber' => $transaction->wallet->name ?? 'Sem carteira',
                    'date' => $transaction->paid_at->format('d M'),
                    'amount' => -abs($transaction->amount),
                    'type' => 'expense',
                    'icon' => $this->getCategoryIcon($transaction->category->name ?? ''),
                ];
            });

        // Get recent received income transactions
        $incomeTransactions = IncomeTransaction::with(['category', 'income'])
            ->where('user_id', $userId)
            ->where('is_received', true)
            ->orderBy('received_at', 'desc')
            ->limit(2)
            ->get()
            ->map(function ($income) {
                return [
                    'id' => $income->id,
                    'name' => $income->income->name ?? 'Receita',
                    'accountNumber' => $income->category->name ?? 'Sem categoria',
                    'date' => $income->received_at->format('d M'),
                    'amount' => abs($income->amount),
                    'type' => 'income',
                    'icon' => 'income',
                ];
            });

        // Merge and sort by date
        $activities = $transactions->concat($incomeTransactions)
            ->sortByDesc('date')
            ->take(7)
            ->values()
            ->toArray();

        return $activities;
    }

    /**
     * Get wallets summary with balance and usage
     */
    protected function getWalletsSummary(string $userId): array
    {
        return Wallet::where('user_id', $userId)
            ->where('status', true)
            ->get()
            ->map(function ($wallet) {
                $data = [
                    'id' => $wallet->uuid,
                    'name' => $wallet->name,
                    'type' => $wallet->type->value,
                    'balance' => $wallet->balance,
                ];

                // Add credit card specific data
                if ($wallet->type->value === 'card_credit' && $wallet->card_limit > 0) {
                    $data['card_limit'] = $wallet->card_limit;
                    $data['card_limit_used'] = $wallet->card_limit_used;
                    $data['usage_percentage'] = round(($wallet->card_limit_used / $wallet->card_limit) * 100, 1);
                }

                return $data;
            })
            ->toArray();
    }

    /**
     * Get unread notifications count
     */
    protected function getUnreadNotificationsCount(string $userId): int
    {
        return AlertNotification::where('user_id', $userId)
            ->unread()
            ->count();
    }

    /**
     * Get unread notifications (last 5)
     */
    protected function getUnreadNotifications(string $userId): array
    {
        return AlertNotification::where('user_id', $userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->uuid,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            })
            ->toArray();
    }

    /**
     * Get accounts that end in the current month (last installment)
     */
    protected function getAccountsEndingThisMonth(string $userId): array
    {
        return DB::table('accounts')
            ->join('transactions', 'accounts.id', '=', 'transactions.account_id')
            ->join('categories', 'accounts.category_id', '=', 'categories.id')
            ->join('wallets', 'accounts.wallet_id', '=', 'wallets.id')
            ->where('accounts.user_id', $userId)
            ->where('accounts.recurrence_type', 'installments')
            ->where('accounts.status', 'active')
            ->whereYear('transactions.due_date', now()->year)
            ->whereMonth('transactions.due_date', now()->month)
            ->whereRaw('transactions.installment_number = transactions.total_installments')
            ->select(
                'accounts.uuid',
                'accounts.name',
                'accounts.total_amount',
                'accounts.installments',
                'categories.name as category_name',
                'wallets.name as wallet_name',
                'transactions.due_date',
                'transactions.installment_number',
                'transactions.total_installments'
            )
            ->orderBy('transactions.due_date', 'asc')
            ->get()
            ->map(function ($account) {
                return [
                    'uuid' => $account->uuid,
                    'name' => $account->name,
                    'category' => $account->category_name,
                    'wallet' => $account->wallet_name,
                    'total_amount' => $account->total_amount / 100, // Convert cents to reais
                    'installments' => $account->installments,
                    'installment_info' => "{$account->installment_number}/{$account->total_installments}",
                    'due_date' => Carbon::parse($account->due_date)->format('d/m/Y'),
                ];
            })
            ->toArray();
    }

    /**
     * Get icon name based on category name
     */
    protected function getCategoryIcon(string $categoryName): string
    {
        $icons = [
            'Alimentação' => 'food',
            'Transporte' => 'transport',
            'Moradia' => 'home',
            'Saúde' => 'health',
            'Educação' => 'education',
            'Lazer' => 'entertainment',
            'Compras' => 'shopping',
        ];

        return $icons[$categoryName] ?? 'default';
    }
}
