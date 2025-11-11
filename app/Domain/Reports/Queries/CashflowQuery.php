<?php

namespace App\Domain\Reports\Queries;

use App\Domain\Reports\DTO\ReportFiltersData;
use App\Enums\TransactionStatusEnum;
use App\Models\Transaction;
use App\Models\IncomeTransaction;
use Illuminate\Support\Facades\DB;

class CashflowQuery extends BaseReportQuery
{
    /**
     * Execute the report query
     * Compares income vs expenses over time
     */
    public function execute(string $userId, ReportFiltersData $filters): array
    {
        $periodType = $filters->periodType ?? 'monthly';

        // Determine date grouping based on period type
        $dateFormat = match ($periodType) {
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%u', // Year-Week
            'monthly' => '%Y-%m',
            default => '%Y-%m',
        };

        // Get expenses grouped by period
        $expenses = Transaction::query()
            ->where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PAID)
            ->when($filters->startDate, fn($q) => $q->whereDate('paid_at', '>=', $filters->startDate))
            ->when($filters->endDate, fn($q) => $q->whereDate('paid_at', '<=', $filters->endDate))
            ->when($filters->categoryIds, fn($q) => $q->whereIn('category_id', $filters->categoryIds))
            ->when($filters->walletIds, fn($q) => $q->whereIn('wallet_id', $filters->walletIds))
            ->select(
                DB::raw("DATE_FORMAT(paid_at, '{$dateFormat}') as period"),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get()
            ->keyBy('period');

        // Get incomes grouped by period
        $incomes = IncomeTransaction::query()
            ->where('user_id', $userId)
            ->where('is_received', true)
            ->when($filters->startDate, fn($q) => $q->whereDate('received_at', '>=', $filters->startDate))
            ->when($filters->endDate, fn($q) => $q->whereDate('received_at', '<=', $filters->endDate))
            ->when($filters->walletIds, function($q) use ($filters) {
                $q->whereHas('income', function($query) use ($filters) {
                    $query->whereIn('wallet_id', $filters->walletIds);
                });
            })
            ->select(
                DB::raw("DATE_FORMAT(received_at, '{$dateFormat}') as period"),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get()
            ->keyBy('period');

        // Merge periods
        $allPeriods = collect($expenses->keys())
            ->merge($incomes->keys())
            ->unique()
            ->sort()
            ->values();

        // Build data
        $data = [];
        foreach ($allPeriods as $period) {
            $expenseAmount = $expenses->get($period)?->total_amount ?? 0;
            $incomeAmount = $incomes->get($period)?->total_amount ?? 0;

            $expenseReais = $this->centsToReais($expenseAmount);
            $incomeReais = $this->centsToReais($incomeAmount);
            $balance = $incomeReais - $expenseReais;

            $data[] = [
                'period' => $this->formatPeriodLabel($period, $periodType),
                'period_raw' => $period,
                'expenses' => $this->formatNumber($expenseReais),
                'incomes' => $this->formatNumber($incomeReais),
                'balance' => $this->formatNumber($balance),
            ];
        }

        // Calculate summary
        $totalExpenses = array_sum(array_column($data, 'expenses'));
        $totalIncomes = array_sum(array_column($data, 'incomes'));
        $totalBalance = $totalIncomes - $totalExpenses;

        return [
            'data' => $data,
            'summary' => [
                'total_expenses' => $this->formatNumber($totalExpenses),
                'total_incomes' => $this->formatNumber($totalIncomes),
                'total_balance' => $this->formatNumber($totalBalance),
                'periods_count' => count($data),
                'period_type' => $periodType,
            ],
        ];
    }

    /**
     * Format period label for display
     */
    private function formatPeriodLabel(string $period, string $periodType): string
    {
        return match ($periodType) {
            'daily' => \Carbon\Carbon::parse($period)->format('d/m/Y'),
            'weekly' => 'Semana ' . substr($period, -2) . '/' . substr($period, 0, 4),
            'monthly' => \Carbon\Carbon::parse($period . '-01')->format('M/Y'),
            default => $period,
        };
    }
}
