<?php

namespace App\Domain\Budgets\DTO;

use Spatie\LaravelData\Data;

class UpdateBudgetData extends Data
{
    public function __construct(
        public ?float $amount,
        public ?string $recurrence,
        public ?bool $status,
    ) {}
}
