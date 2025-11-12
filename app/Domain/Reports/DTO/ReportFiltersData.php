<?php

namespace App\Domain\Reports\DTO;

use Illuminate\Http\Request;

readonly class ReportFiltersData
{
    public function __construct(
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?string $periodType = null, // daily, weekly, monthly
        public ?array $categoryIds = null,
        public ?array $walletIds = null,
        public ?float $minAmount = null,
        public ?float $maxAmount = null,
        public ?string $status = null, // pending, paid, all
        public ?int $limit = null, // For top N queries
    ) {}

    /**
     * Create from Request
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            startDate: $request->input('start_date'),
            endDate: $request->input('end_date'),
            periodType: $request->input('period_type', 'monthly'),
            categoryIds: $request->input('category_ids') ? (array) $request->input('category_ids') : null,
            walletIds: $request->input('wallet_ids') ? (array) $request->input('wallet_ids') : null,
            minAmount: $request->input('min_amount') ? (float) $request->input('min_amount') : null,
            maxAmount: $request->input('max_amount') ? (float) $request->input('max_amount') : null,
            status: $request->input('status', 'all'),
            limit: $request->input('limit') ? (int) $request->input('limit') : null,
        );
    }

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            startDate: $data['start_date'] ?? null,
            endDate: $data['end_date'] ?? null,
            periodType: $data['period_type'] ?? 'monthly',
            categoryIds: isset($data['category_ids']) ? (array) $data['category_ids'] : null,
            walletIds: isset($data['wallet_ids']) ? (array) $data['wallet_ids'] : null,
            minAmount: isset($data['min_amount']) ? (float) $data['min_amount'] : null,
            maxAmount: isset($data['max_amount']) ? (float) $data['max_amount'] : null,
            status: $data['status'] ?? 'all',
            limit: isset($data['limit']) ? (int) $data['limit'] : null,
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'period_type' => $this->periodType,
            'category_ids' => $this->categoryIds,
            'wallet_ids' => $this->walletIds,
            'min_amount' => $this->minAmount,
            'max_amount' => $this->maxAmount,
            'status' => $this->status,
            'limit' => $this->limit,
        ];
    }
}
