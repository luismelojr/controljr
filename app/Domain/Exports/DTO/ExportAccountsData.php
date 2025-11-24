<?php

namespace App\Domain\Exports\DTO;

use Spatie\LaravelData\Data;

class ExportAccountsData extends Data
{
    public function __construct(
        public int $user_id,
        public string $format, // pdf, excel, csv
        public ?string $status = null,
    ) {}
}
