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
        $query = Transaction::query()
            ->where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PAID);

        // Apply filters using helper methods
        $this->applyPeriodFilter($query, $filters, 'paid_at');
        $this->applyCategoryFilter($query, $filters);
        $this->applyWalletFilter($query, $filters);
        $this->applyAmountRangeFilter($query, $filters);

        $results = $query
            ->select(
                DB::raw("DATE_FORMAT(paid_at, '{$dateFormat}') as period"),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        // Calculate variation percentage (convert to reais)
        $data = [];
        $previousAmount = null;

        foreach ($results as $item) {
            $amountInReais = $this->centsToReais($item->total_amount);
            $variation = null;

            if ($previousAmount !== null && $previousAmount > 0) {
                $variation = (($amountInReais - $previousAmount) / $previousAmount) * 100;
            }

            $data[] = [
                'period' => $this->formatPeriodLabel($item->period, $periodType),
                'period_raw' => $item->period,
                'value' => $this->formatNumber($amountInReais),
                'count' => $item->transaction_count,
                'variation_percentage' => $variation !== null ? $this->formatNumber($variation) : null,
            ];

            $previousAmount = $amountInReais;
        }

        // Calculate summary
        $totalAmount = array_sum(array_column($data, 'value'));
        $totalTransactions = array_sum(array_column($data, 'count'));
        $averagePerPeriod = count($data) > 0 ? $this->formatNumber($totalAmount / count($data)) : 0;

        return [
            'data' => $data,
            'summary' => [
                'total' => $this->formatNumber($totalAmount),
                'count' => $totalTransactions,
                'periods_count' => count($data),
                'average' => $averagePerPeriod,
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
