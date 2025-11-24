<?php

namespace App\Services\Reporting;

use App\Models\Transaction;
use App\Models\IncomeTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getFinancialOverview(Carbon $startDate, Carbon $endDate)
    {
        $userId = auth()->id();

        $totalIncome = IncomeTransaction::where('user_id', $userId)
            ->whereBetween(DB::raw('COALESCE(received_at, expected_date)'), [$startDate, $endDate])
            ->sum('amount') / 100;

        $totalExpenses = Transaction::where('user_id', $userId)
            ->whereBetween(DB::raw('COALESCE(paid_at, due_date)'), [$startDate, $endDate])
            ->sum('amount') / 100;

        $netResult = $totalIncome - $totalExpenses;
        
        $savingsRate = $totalIncome > 0 ? ($netResult / $totalIncome) * 100 : 0;

        return [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_result' => $netResult,
            'savings_rate' => round($savingsRate, 2),
        ];
    }

    public function getCashFlow(Carbon $startDate, Carbon $endDate)
    {
        $userId = auth()->id();

        // Group by date
        $incomes = IncomeTransaction::selectRaw('COALESCE(received_at, expected_date) as date, SUM(amount) as total')
            ->where('user_id', $userId)
            ->whereBetween(DB::raw('COALESCE(received_at, expected_date)'), [$startDate, $endDate])
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $expenses = Transaction::selectRaw('COALESCE(paid_at, due_date) as date, SUM(amount) as total')
            ->where('user_id', $userId)
            ->whereBetween(DB::raw('COALESCE(paid_at, due_date)'), [$startDate, $endDate])
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $data = [];

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            
            $incomeRaw = $incomes->first(fn($item) => Carbon::parse($item->date)->format('Y-m-d') === $dateStr)?->total ?? 0;
            $expenseRaw = $expenses->first(fn($item) => Carbon::parse($item->date)->format('Y-m-d') === $dateStr)?->total ?? 0;

            $income = $incomeRaw / 100;
            $expense = $expenseRaw / 100;

            $data[] = [
                'date' => $dateStr,
                'income' => (float) $income,
                'expense' => (float) $expense,
                'balance' => (float) ($income - $expense),
            ];
        }

        return $data;
    }

    public function getExpensesByCategory(Carbon $startDate, Carbon $endDate)
    {
        $userId = auth()->id();

        $categories = Transaction::selectRaw('COALESCE(categories.name, "Sem Categoria") as category_name, SUM(transactions.amount) as total')
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->whereBetween(DB::raw('COALESCE(transactions.paid_at, transactions.due_date)'), [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get();

        $colors = [
            '#ef4444', '#f97316', '#f59e0b', '#84cc16', '#10b981',
            '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', '#d946ef',
            '#f43f5e', '#ec4899'
        ];

        return $categories->map(function ($category, $index) use ($colors) {
            $category->total = $category->total / 100;
            $category->category_color = $colors[$index % count($colors)];
            return $category;
        });
    }

    public function getIncomeByCategory(Carbon $startDate, Carbon $endDate)
    {
        $userId = auth()->id();

        $categories = IncomeTransaction::selectRaw('COALESCE(categories.name, "Sem Categoria") as category_name, SUM(income_transactions.amount) as total')
            ->leftJoin('categories', 'income_transactions.category_id', '=', 'categories.id')
            ->where('income_transactions.user_id', $userId)
            ->whereBetween(DB::raw('COALESCE(income_transactions.received_at, income_transactions.expected_date)'), [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get();

        $colors = [
            '#10b981', '#34d399', '#6ee7b7', '#059669', '#047857',
            '#065f46', '#064e3b', '#3b82f6', '#2563eb', '#1d4ed8'
        ];

        return $categories->map(function ($category, $index) use ($colors) {
            $category->total = $category->total / 100;
            $category->category_color = $colors[$index % count($colors)];
            return $category;
        });
    }
}
