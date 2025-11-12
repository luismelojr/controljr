<?php

namespace App\Domain\Reports\Queries;

use App\Domain\Reports\DTO\ReportFiltersData;
use App\Enums\TransactionStatusEnum;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseReportQuery
{
    /**
     * Execute the report query
     */
    abstract public function execute(string $userId, ReportFiltersData $filters): array;

    /**
     * Apply period filter (start_date and end_date)
     */
    protected function applyPeriodFilter(Builder $query, ReportFiltersData $filters, string $dateColumn = 'paid_at'): Builder
    {
        if ($filters->startDate) {
            $query->whereDate($dateColumn, '>=', $filters->startDate);
        }

        if ($filters->endDate) {
            $query->whereDate($dateColumn, '<=', $filters->endDate);
        }

        return $query;
    }

    /**
     * Apply category filter
     */
    protected function applyCategoryFilter(Builder $query, ReportFiltersData $filters): Builder
    {
        if ($filters->categoryIds && count($filters->categoryIds) > 0) {
            // Convert UUIDs to IDs
            $categoryIds = \App\Models\Category::whereIn('uuid', $filters->categoryIds)
                ->pluck('id')
                ->toArray();

            if (count($categoryIds) > 0) {
                $query->whereIn('category_id', $categoryIds);
            }
        }

        return $query;
    }

    /**
     * Apply wallet filter
     */
    protected function applyWalletFilter(Builder $query, ReportFiltersData $filters): Builder
    {
        if ($filters->walletIds && count($filters->walletIds) > 0) {
            // Convert UUIDs to IDs
            $walletIds = \App\Models\Wallet::whereIn('uuid', $filters->walletIds)
                ->pluck('id')
                ->toArray();

            if (count($walletIds) > 0) {
                $query->whereIn('wallet_id', $walletIds);
            }
        }

        return $query;
    }

    /**
     * Apply status filter
     */
    protected function applyStatusFilter(Builder $query, ReportFiltersData $filters, string $statusColumn = 'status'): Builder
    {
        if ($filters->status && $filters->status !== 'all') {
            $query->where($statusColumn, TransactionStatusEnum::from($filters->status));
        }

        return $query;
    }

    /**
     * Apply amount range filter
     */
    protected function applyAmountRangeFilter(Builder $query, ReportFiltersData $filters, string $amountColumn = 'amount'): Builder
    {
        if ($filters->minAmount !== null) {
            // Convert reais to cents
            $minAmountCents = (int) ($filters->minAmount * 100);
            $query->where($amountColumn, '>=', $minAmountCents);
        }

        if ($filters->maxAmount !== null) {
            // Convert reais to cents
            $maxAmountCents = (int) ($filters->maxAmount * 100);
            $query->where($amountColumn, '<=', $maxAmountCents);
        }

        return $query;
    }

    /**
     * Convert cents to reais
     */
    protected function centsToReais(int $cents): float
    {
        return $cents / 100;
    }

    /**
     * Format number to 2 decimal places
     */
    protected function formatNumber(float $number): float
    {
        return round($number, 2);
    }
}
