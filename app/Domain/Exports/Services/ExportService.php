<?php

namespace App\Domain\Exports\Services;

use App\Domain\Exports\DTO\ExportTransactionsData;
use App\Domain\Exports\DTO\ExportIncomesData;
use App\Domain\Exports\DTO\ExportAccountsData;

class ExportService
{
    public function __construct(
        protected ExcelExportService $excelService,
        protected CsvExportService $csvService,
    ) {}

    /**
     * Exporta transações no formato especificado
     */
    public function exportTransactions(ExportTransactionsData $data): string
    {
        return match($data->format) {
            'excel' => $this->excelService->exportTransactions($data->filters),
            'csv' => $this->csvService->exportTransactions($data->filters),
            default => throw new \Exception("Formato não suportado: {$data->format}"),
        };
    }

    /**
     * Exporta receitas no formato especificado
     */
    public function exportIncomes(ExportIncomesData $data): string
    {
        return match($data->format) {
            'excel' => $this->excelService->exportIncomes($data->filters),
            'csv' => $this->csvService->exportIncomes($data->filters),
            default => throw new \Exception("Formato não suportado: {$data->format}"),
        };
    }

    /**
     * Exporta contas no formato especificado
     */
    public function exportAccounts(ExportAccountsData $data): string
    {
        return match($data->format) {
            'excel' => $this->excelService->exportAccounts($data->user_id, $data->status),
            'csv' => $this->csvService->exportAccounts($data->user_id, $data->status),
            default => throw new \Exception("Formato não suportado: {$data->format}"),
        };
    }

    /**
     * Exporta orçamentos no formato especificado
     */
    public function exportBudgets(int $userId, string $format): string
    {
        return match($format) {
            'excel' => $this->excelService->exportBudgets($userId),
            'csv' => $this->csvService->exportBudgets($userId),
            default => throw new \Exception("Formato não suportado: {$format}"),
        };
    }
}
