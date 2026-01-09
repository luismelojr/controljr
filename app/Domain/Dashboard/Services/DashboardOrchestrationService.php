<?php

namespace App\Domain\Dashboard\Services;

/**
 * Dashboard Orchestration Service
 *
 * This service orchestrates multiple specialized services to provide
 * complete dashboard data. It follows the Single Responsibility Principle
 * by delegating specific calculations to focused services.
 *
 * Refactored from the original DashboardService to improve maintainability
 * and testability by separating concerns.
 */
class DashboardOrchestrationService
{
    public function __construct(
        protected WalletBalanceService $walletBalanceService,
        protected FinancialAnalysisService $financialAnalysisService,
        protected TransactionAnalysisService $transactionAnalysisService,
        protected NotificationAnalysisService $notificationAnalysisService,
    ) {}

    /**
     * Get all dashboard data (real-time, no cache)
     *
     * This method orchestrates calls to multiple services to provide
     * a complete dashboard view.
     */
    public function getDashboardData(string $userId): array
    {
        // Balance calculations
        $totalBalance = $this->walletBalanceService->getTotalBalance($userId);

        // Financial analysis
        $monthlyExpenses = $this->financialAnalysisService->getMonthlyExpenses($userId);
        $monthlyIncome = $this->financialAnalysisService->getMonthlyIncome($userId);

        // Percentage changes (requires previous calculations)
        $balancePercentageChange = $this->walletBalanceService->getBalancePercentageChange(
            $userId,
            $totalBalance,
            $monthlyIncome,
            $monthlyExpenses
        );

        $expensesPercentageChange = $this->financialAnalysisService->getExpensesPercentageChange(
            $userId,
            $monthlyExpenses
        );

        $incomePercentageChange = $this->financialAnalysisService->getIncomePercentageChange(
            $userId,
            $monthlyIncome
        );

        return [
            // Balance data
            'total_balance' => $totalBalance,
            'balance_percentage_change' => $balancePercentageChange,

            // Expenses data
            'monthly_expenses' => $monthlyExpenses,
            'expenses_percentage_change' => $expensesPercentageChange,

            // Income data
            'monthly_income' => $monthlyIncome,
            'income_percentage_change' => $incomePercentageChange,

            // Transaction analysis
            'upcoming_transactions' => $this->transactionAnalysisService->getUpcomingTransactions($userId),

            // Wallet summary
            'wallets_summary' => $this->walletBalanceService->getWalletsSummary($userId),

            // Accounts ending
            'accounts_ending_this_month' => $this->transactionAnalysisService->getAccountsEndingThisMonth($userId),

            // Notifications
            'unread_notifications_count' => $this->notificationAnalysisService->getUnreadNotificationsCount($userId),
            'unread_notifications' => $this->notificationAnalysisService->getUnreadNotifications($userId),
        ];
    }

    /**
     * Get cashflow data for charts
     *
     * Delegates to FinancialAnalysisService
     */
    public function getCashflowData(string $userId): array
    {
        return $this->financialAnalysisService->getCashflowData($userId);
    }

    /**
     * Get recent activities
     *
     * Delegates to TransactionAnalysisService
     */
    public function getRecentActivities(string $userId): array
    {
        return $this->transactionAnalysisService->getRecentActivities($userId);
    }
}
