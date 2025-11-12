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
            ->where('user_id', $userId);

        // Apply filters
        $this->applyPeriodFilter($query, $filters, 'paid_at');
        $this->applyCategoryFilter($query, $filters);
        $this->applyWalletFilter($query, $filters);
        $this->applyStatusFilter($query, $filters);
        $this->applyAmountRangeFilter($query, $filters);

        // Calculate total for percentage calculation
        $totalAmount = (clone $query)->sum('amount');

        // Group by category
        $results = (clone $query)
            ->select(
                'category_id',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('AVG(amount) as average_amount')
            )
            ->groupBy('category_id')
            ->orderByDesc('total_amount')
            ->get();

        // Format results for frontend (values in reais)
        $data = $results->map(function ($item) use ($totalAmount) {
            $category = \App\Models\Category::find($item->category_id);
            $percentage = $totalAmount > 0 ? ($item->total_amount / $totalAmount) * 100 : 0;

            return [
                'name' => $category ? $category->name : 'Sem Categoria',
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
                'categories_count' => count($data),
            ],
        ];
    }
}
