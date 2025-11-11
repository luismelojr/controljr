<?php

namespace App\Domain\Reports\Queries;

use App\Domain\Reports\DTO\ReportFiltersData;
use App\Enums\TransactionStatusEnum;
use App\Models\Transaction;

class TopExpensesQuery extends BaseReportQuery
{
    /**
     * Execute the report query
     * Returns top N expenses ordered by amount
     */
    public function execute(string $userId, ReportFiltersData $filters): array
    {
        $limit = $filters->limit ?? 10; // Default top 10

        // Build query
        $query = Transaction::query()
            ->where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PAID)
            ->with(['category', 'wallet', 'account']);

        // Apply filters
        $this->applyPeriodFilter($query, $filters, 'paid_at');
        $this->applyCategoryFilter($query, $filters);
        $this->applyWalletFilter($query, $filters);
        $this->applyAmountRangeFilter($query, $filters);

        // Get top expenses
        $results = $query
            ->orderByDesc('amount')
            ->limit($limit)
            ->get();

        // Format results
        $data = $results->map(function ($transaction) {
            return [
                'id' => $transaction->uuid,
                'name' => $transaction->account->name ?? 'Sem nome',
                'description' => $transaction->account->description ?? null,
                'category' => $transaction->category->name ?? 'Sem Categoria',
                'wallet' => $transaction->wallet->name ?? 'Sem Carteira',
                'amount' => $this->formatNumber($this->centsToReais($transaction->amount)),
                'paid_at' => $transaction->paid_at->format('d/m/Y'),
                'installment_info' => $transaction->total_installments > 1
                    ? "{$transaction->installment_number}/{$transaction->total_installments}"
                    : null,
            ];
        })->toArray();

        // Calculate summary
        $totalAmount = $results->sum('amount');
        $averageAmount = $results->count() > 0 ? $totalAmount / $results->count() : 0;

        return [
            'data' => $data,
            'summary' => [
                'total_amount' => $this->formatNumber($this->centsToReais($totalAmount)),
                'expenses_count' => count($data),
                'average_amount' => $this->formatNumber($this->centsToReais((int) $averageAmount)),
                'limit' => $limit,
            ],
        ];
    }
}
