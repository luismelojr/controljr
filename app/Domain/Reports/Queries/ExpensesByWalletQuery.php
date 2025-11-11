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
                'wallet_id',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('wallet_id')
            ->orderByDesc('total_amount')
            ->get();

        // Format results
        $data = $results->map(function ($item) use ($totalAmount) {
            $wallet = \App\Models\Wallet::find($item->wallet_id);
            $totalReais = $this->centsToReais($item->total_amount);
            $percentage = $totalAmount > 0 ? ($item->total_amount / $totalAmount) * 100 : 0;

            return [
                'wallet_id' => $item->wallet_id,
                'wallet_name' => $wallet ? $wallet->name : 'Sem Carteira',
                'wallet_type' => $wallet ? $wallet->type->value : null,
                'total' => $this->formatNumber($totalReais),
                'count' => $item->transaction_count,
                'percentage' => $this->formatNumber($percentage),
            ];
        })->toArray();

        return [
            'data' => $data,
            'summary' => [
                'total_amount' => $this->formatNumber($this->centsToReais($totalAmount)),
                'total_transactions' => array_sum(array_column($data, 'count')),
                'wallets_count' => count($data),
            ],
        ];
    }
}
