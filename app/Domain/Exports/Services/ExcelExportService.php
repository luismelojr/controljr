<?php

namespace App\Domain\Exports\Services;

use App\Domain\Exports\DTO\ExportFiltersData;
use App\Domain\Exports\Exports\TransactionsExport;
use App\Domain\Exports\Exports\IncomesExport;
use App\Domain\Exports\Exports\AccountsExport;
use App\Domain\Exports\Exports\BudgetsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ExcelExportService
{
    /**
     * Exporta transações para Excel
     */
    public function exportTransactions(ExportFiltersData $filters): string
    {
        $fileName = "transacoes_" . now()->format('Y-m-d_His') . '.xlsx';
        $path = "exports/transactions/{$fileName}";

        Excel::store(
            new TransactionsExport($filters),
            $path,
            'public'
        );

        return Storage::disk('public')->path($path);
    }

    /**
     * Exporta receitas para Excel
     */
    public function exportIncomes(ExportFiltersData $filters): string
    {
        $fileName = "receitas_" . now()->format('Y-m-d_His') . '.xlsx';
        $path = "exports/incomes/{$fileName}";

        Excel::store(
            new IncomesExport($filters),
            $path,
            'public'
        );

        return Storage::disk('public')->path($path);
    }

    /**
     * Exporta contas para Excel
     */
    public function exportAccounts(int $userId, ?string $status = null): string
    {
        $fileName = "contas_" . now()->format('Y-m-d_His') . '.xlsx';
        $path = "exports/accounts/{$fileName}";

        Excel::store(
            new AccountsExport($userId, $status),
            $path,
            'public'
        );

        return Storage::disk('public')->path($path);
    }

    /**
     * Exporta orçamentos para Excel
     */
    public function exportBudgets(int $userId): string
    {
        $fileName = "orcamentos_" . now()->format('Y-m-d_His') . '.xlsx';
        $path = "exports/budgets/{$fileName}";

        Excel::store(
            new BudgetsExport($userId),
            $path,
            'public'
        );

        return Storage::disk('public')->path($path);
    }
}
