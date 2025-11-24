<?php

namespace App\Domain\Exports\DTO;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class ExportFiltersData extends Data
{
    public function __construct(
        public int $user_id,
        public ?Carbon $start_date = null,
        public ?Carbon $end_date = null,
        public ?array $category_ids = null,
        public ?array $wallet_ids = null,
        public ?string $status = null,
    ) {}
}
