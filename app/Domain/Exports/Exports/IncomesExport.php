<?php

namespace App\Domain\Exports\Exports;

use App\Domain\Exports\DTO\ExportFiltersData;
use App\Models\IncomeTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncomesExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle
{
    public function __construct(
        protected ExportFiltersData $filters
    ) {}

    /**
     * Query para buscar receitas
     */
    public function query()
    {
        $query = IncomeTransaction::query()
            ->with(['category', 'wallet', 'income'])
            ->where('user_id', $this->filters->user_id);

        // Aplica filtros de data
        if ($this->filters->start_date) {
            $query->where('due_date', '>=', $this->filters->start_date);
        }

        if ($this->filters->end_date) {
            $query->where('due_date', '<=', $this->filters->end_date);
        }

        if ($this->filters->category_ids) {
            $query->whereIn('category_id', $this->filters->category_ids);
        }

        if ($this->filters->wallet_ids) {
            $query->whereIn('wallet_id', $this->filters->wallet_ids);
        }

        return $query->orderBy('due_date', 'desc');
    }

    /**
     * Cabeçalhos das colunas
     */
    public function headings(): array
    {
        return [
            'ID',
            'Receita',
            'Categoria',
            'Carteira',
            'Valor (R$)',
            'Data Prevista',
            'Data Recebimento',
            'Recebido',
            'Parcela',
        ];
    }

    /**
     * Mapeia cada linha
     */
    public function map($incomeTransaction): array
    {
        return [
            $incomeTransaction->uuid,
            $incomeTransaction->income->name ?? '-',
            $incomeTransaction->category->name ?? '-',
            $incomeTransaction->wallet->name ?? '-',
            number_format($incomeTransaction->amount, 2, ',', '.'),
            $incomeTransaction->due_date->format('d/m/Y'),
            $incomeTransaction->received_at?->format('d/m/Y') ?? '-',
            $incomeTransaction->is_received ? 'Sim' : 'Não',
            $incomeTransaction->total_installments > 1
                ? "{$incomeTransaction->installment_number}/{$incomeTransaction->total_installments}"
                : '-',
        ];
    }

    /**
     * Estilos da planilha
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D1FAE5']
                ],
            ],
        ];
    }

    /**
     * Título da planilha
     */
    public function title(): string
    {
        return 'Receitas';
    }
}
