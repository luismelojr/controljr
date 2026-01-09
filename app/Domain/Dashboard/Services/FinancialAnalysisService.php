<?php

namespace App\Domain\Dashboard\Services;

use App\Enums\TransactionStatusEnum;
use App\Models\Transaction;
use App\Models\IncomeTransaction;

/**
 * Service responsible for financial analysis (income, expenses, cashflow)
 */
class FinancialAnalysisService
{
    /**
     * Get total expenses for current month
     */
    public function getMonthlyExpenses(string $userId): float
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
    public function getExpensesPercentageChange(string $userId, float $currentMonthExpenses): float
    {
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
    public function getMonthlyIncome(string $userId): float
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
    public function getIncomePercentageChange(string $userId, float $currentMonthIncome): float
    {
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
    public function getCashflowData(string $userId): array
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
}
