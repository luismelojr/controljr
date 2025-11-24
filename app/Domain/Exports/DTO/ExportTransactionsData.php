<?php

namespace App\Domain\Exports\DTO;

use Spatie\LaravelData\Data;

class ExportTransactionsData extends Data
{
    public function __construct(
        public ExportFiltersData $filters,
        public string $format, // pdf, excel, csv
    ) {}
}
