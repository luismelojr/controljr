<?php

namespace App\Domain\Reports\Queries;

use App\Domain\Reports\DTO\ReportFiltersData;
use App\Enums\TransactionStatusEnum;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ExpensesEvolutionQuery extends BaseReportQuery
{
    /**
     * Execute the report query
     * Shows expenses evolution over time
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

        // Group by period
        $results = Transaction::query()
            ->where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PAID)
            ->when($filters->startDate, fn($q) => $q->whereDate('paid_at', '>=', $filters->startDate))
            ->when($filters->endDate, fn($q) => $q->whereDate('paid_at', '<=', $filters->endDate))
            ->when($filters->categoryIds, fn($q) => $q->whereIn('category_id', $filters->categoryIds))
            ->when($filters->walletIds, fn($q) => $q->whereIn('wallet_id', $filters->walletIds))
            ->when($filters->minAmount, fn($q) => $q->where('amount', '>=', (int) ($filters->minAmount * 100)))
            ->when($filters->maxAmount, fn($q) => $q->where('amount', '<=', (int) ($filters->maxAmount * 100)))
            ->select(
                DB::raw("DATE_FORMAT(paid_at, '{$dateFormat}') as period"),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        // Calculate variation percentage
        $data = [];
        $previousAmount = null;

        foreach ($results as $item) {
            $totalReais = $this->centsToReais($item->total_amount);
            $variation = null;

            if ($previousAmount !== null && $previousAmount > 0) {
                $variation = (($totalReais - $previousAmount) / $previousAmount) * 100;
            }

            $data[] = [
                'period' => $this->formatPeriodLabel($item->period, $periodType),
                'period_raw' => $item->period,
                'total' => $this->formatNumber($totalReais),
                'count' => $item->transaction_count,
                'variation_percentage' => $variation !== null ? $this->formatNumber($variation) : null,
            ];

            $previousAmount = $totalReais;
        }

        // Calculate summary
        $totalAmount = array_sum(array_column($data, 'total'));
        $totalTransactions = array_sum(array_column($data, 'count'));
        $averagePerPeriod = count($data) > 0 ? $totalAmount / count($data) : 0;

        return [
            'data' => $data,
            'summary' => [
                'total_amount' => $this->formatNumber($totalAmount),
                'total_transactions' => $totalTransactions,
                'periods_count' => count($data),
                'average_per_period' => $this->formatNumber($averagePerPeriod),
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
