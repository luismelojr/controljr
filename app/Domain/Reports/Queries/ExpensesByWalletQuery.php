<?php

namespace App\Domain\Reports\Queries;

use App\Domain\Reports\DTO\ReportFiltersData;
use App\Enums\TransactionStatusEnum;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ExpensesByWalletQuery extends BaseReportQuery
{
    /**
     * Execute the report query
     * Groups paid expenses by wallet
     */
    public function execute(string $userId, ReportFiltersData $filters): array
    {
        // Calculate total for percentage calculation
        $totalQuery = Transaction::query()
            ->where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PAID);

        $this->applyPeriodFilter($totalQuery, $filters, 'paid_at');
        $this->applyCategoryFilter($totalQuery, $filters);
        $this->applyWalletFilter($totalQuery, $filters);
        $this->applyAmountRangeFilter($totalQuery, $filters);

        $totalAmount = $totalQuery->sum('amount');

        // Group by wallet
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
                'wallet_id',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('wallet_id')
            ->orderByDesc('total_amount')
            ->get();

        // Format results (values in reais)
        $data = $results->map(function ($item) use ($totalAmount) {
            $wallet = \App\Models\Wallet::find($item->wallet_id);
            $percentage = $totalAmount > 0 ? ($item->total_amount / $totalAmount) * 100 : 0;

            return [
                'name' => $wallet ? $wallet->name : 'Sem Carteira',
                'wallet_type' => $wallet ? $wallet->type->value : null,
                'value' => $this->formatNumber($this->centsToReais($item->total_amount)),
                'count' => $item->transaction_count,
                'percentage' => $this->formatNumber($percentage),
            ];
        })->toArray();

        return [
            'data' => $data,
            'summary' => [
                'total' => $this->formatNumber($this->centsToReais($totalAmount)),
                'count' => array_sum(array_column($data, 'count')),
                'average' => count($data) > 0 ? $this->formatNumber($this->centsToReais($totalAmount / count($data))) : 0,
                'wallets_count' => count($data),
            ],
        ];
    }
}
