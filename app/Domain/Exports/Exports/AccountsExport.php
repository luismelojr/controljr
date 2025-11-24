<?php

namespace App\Domain\Exports\Exports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountsExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle
{
    public function __construct(
        protected int $userId,
        protected ?string $status = null
    ) {}

    /**
     * Query para buscar contas
     */
    public function query()
    {
        $query = Account::query()
            ->with(['category', 'wallet'])
            ->where('user_id', $this->userId);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Cabeçalhos das colunas
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Categoria',
            'Carteira',
            'Valor Total (R$)',
            'Tipo Recorrência',
            'Parcelas',
            'Data Início',
            'Status',
            'Criado em',
        ];
    }

    /**
     * Mapeia cada linha
     */
    public function map($account): array
    {
        return [
            $account->uuid,
            $account->name,
            $account->category->name ?? '-',
            $account->wallet->name ?? '-',
            number_format($account->total_amount, 2, ',', '.'),
            $account->recurrence_type->label(),
            $account->installments ?? '-',
            $account->start_date->format('d/m/Y'),
            $account->status->label(),
            $account->created_at->format('d/m/Y H:i'),
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
                    'startColor' => ['rgb' => 'DBEAFE']
                ],
            ],
        ];
    }

    /**
     * Título da planilha
     */
    public function title(): string
    {
        return 'Contas';
    }
}
