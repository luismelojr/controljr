<?php

namespace App\Exports;

use App\Enums\ReportTypeEnum;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected array $reportData;
    protected ReportTypeEnum $reportType;

    public function __construct(array $reportData, ReportTypeEnum $reportType)
    {
        $this->reportData = $reportData;
        $this->reportType = $reportType;
    }

    /**
     * Return the data collection to export
     */
    public function collection(): Collection
    {
        return collect($this->reportData['data'] ?? []);
    }

    /**
     * Define headings based on report type
     */
    public function headings(): array
    {
        return match ($this->reportType) {
            ReportTypeEnum::EXPENSES_BY_CATEGORY => [
                'Categoria',
                'Total (R$)',
                'Quantidade',
                'Média (R$)',
                'Percentual (%)',
            ],
            ReportTypeEnum::EXPENSES_BY_WALLET => [
                'Carteira',
                'Tipo',
                'Total (R$)',
                'Quantidade',
                'Percentual (%)',
            ],
            ReportTypeEnum::EXPENSES_EVOLUTION => [
                'Período',
                'Total (R$)',
                'Quantidade',
                'Variação (%)',
            ],
            ReportTypeEnum::TOP_EXPENSES => [
                'Nome',
                'Categoria',
                'Carteira',
                'Valor (R$)',
                'Data Pagamento',
                'Parcelas',
            ],
            ReportTypeEnum::CASHFLOW => [
                'Período',
                'Despesas (R$)',
                'Receitas (R$)',
                'Saldo (R$)',
            ],
            default => ['Dados'],
        };
    }

    /**
     * Map each row based on report type
     */
    public function map($row): array
    {
        return match ($this->reportType) {
            ReportTypeEnum::EXPENSES_BY_CATEGORY => [
                $row['category_name'] ?? '-',
                $row['total'] ?? 0,
                $row['count'] ?? 0,
                $row['average'] ?? 0,
                $row['percentage'] ?? 0,
            ],
            ReportTypeEnum::EXPENSES_BY_WALLET => [
                $row['wallet_name'] ?? '-',
                $row['wallet_type'] ?? '-',
                $row['total'] ?? 0,
                $row['count'] ?? 0,
                $row['percentage'] ?? 0,
            ],
            ReportTypeEnum::EXPENSES_EVOLUTION => [
                $row['period'] ?? '-',
                $row['total'] ?? 0,
                $row['count'] ?? 0,
                $row['variation_percentage'] ?? '-',
            ],
            ReportTypeEnum::TOP_EXPENSES => [
                $row['name'] ?? '-',
                $row['category'] ?? '-',
                $row['wallet'] ?? '-',
                $row['amount'] ?? 0,
                $row['paid_at'] ?? '-',
                $row['installment_info'] ?? '-',
            ],
            ReportTypeEnum::CASHFLOW => [
                $row['period'] ?? '-',
                $row['expenses'] ?? 0,
                $row['incomes'] ?? 0,
                $row['balance'] ?? 0,
            ],
            default => [$row],
        };
    }

    /**
     * Define sheet title
     */
    public function title(): string
    {
        return substr($this->reportType->label(), 0, 31); // Excel limit is 31 chars
    }

    /**
     * Apply styles to the sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings)
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ];
    }
}
