<?php

namespace App\Domain\Exports\Exports;

use App\Models\Budget;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BudgetsExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle
{
    public function __construct(
        protected int $userId
    ) {}

    /**
     * Query para buscar orçamentos
     */
    public function query()
    {
        return Budget::query()
            ->with(['category'])
            ->where('user_id', $this->userId)
            ->orderBy('period', 'desc');
    }

    /**
     * Cabeçalhos das colunas
     */
    public function headings(): array
    {
        return [
            'ID',
            'Categoria',
            'Valor Limite (R$)',
            'Período',
            'Recorrência',
            'Status',
            'Criado em',
        ];
    }

    /**
     * Mapeia cada linha
     */
    public function map($budget): array
    {
        return [
            $budget->uuid,
            $budget->category->name ?? '-',
            number_format($budget->amount, 2, ',', '.'),
            \Carbon\Carbon::parse($budget->period)->format('m/Y'),
            ucfirst($budget->recurrence),
            $budget->status ? 'Ativo' : 'Inativo',
            $budget->created_at->format('d/m/Y H:i'),
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
                    'startColor' => ['rgb' => 'FEF3C7']
                ],
            ],
        ];
    }

    /**
     * Título da planilha
     */
    public function title(): string
    {
        return 'Orçamentos';
    }
}
