<?php

namespace App\Domain\Reports\Queries;

use App\Domain\Reports\DTO\ReportFiltersData;
use App\Enums\TransactionStatusEnum;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ExpensesByCategoryQuery extends BaseReportQuery
{
    /**
     * Execute the report query
     * Groups paid expenses by category
     */
    public function execute(string $userId, ReportFiltersData $filters): array
    {
        // Build base query
        $query = Transaction::query()
            ->where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PAID)
            ->with('category');

        // Apply filters
        $this->applyPeriodFilter($query, $filters, 'paid_at');
        $this->applyCategoryFilter($query, $filters);
        $this->applyWalletFilter($query, $filters);
        $this->applyAmountRangeFilter($query, $filters);

        // Calculate total for percentage calculation
        $totalAmount = $query->sum('amount');

        // Group by category
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
                'category_id',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('AVG(amount) as average_amount')
            )
            ->groupBy('category_id')
            ->orderByDesc('total_amount')
            ->get();

        // Format results
        $data = $results->map(function ($item) use ($totalAmount) {
            $category = \App\Models\Category::find($item->category_id);
            $totalReais = $this->centsToReais($item->total_amount);
            $averageReais = $this->centsToReais((int) $item->average_amount);
            $percentage = $totalAmount > 0 ? ($item->total_amount / $totalAmount) * 100 : 0;

            return [
                'category_id' => $item->category_id,
                'category_name' => $category ? $category->name : 'Sem Categoria',
                'total' => $this->formatNumber($totalReais),
                'count' => $item->transaction_count,
                'average' => $this->formatNumber($averageReais),
                'percentage' => $this->formatNumber($percentage),
            ];
        })->toArray();

        return [
            'data' => $data,
            'summary' => [
                'total_amount' => $this->formatNumber($this->centsToReais($totalAmount)),
                'total_transactions' => array_sum(array_column($data, 'count')),
                'categories_count' => count($data),
            ],
        ];
    }
}
