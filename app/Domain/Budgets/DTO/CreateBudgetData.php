<?php

namespace App\Domain\Budgets\DTO;

use Spatie\LaravelData\Data;

class CreateBudgetData extends Data
{
    public function __construct(
        public string $category_id,
        public float $amount,
        public string $period, // YYYY-MM-DD
        public string $recurrence = 'monthly',
    ) {}
}
