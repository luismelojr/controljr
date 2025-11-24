<?php

namespace App\Domain\Exports\Exports;

use App\Domain\Exports\DTO\ExportFiltersData;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements
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
     * Query para buscar transações
     */
    public function query()
    {
        $query = Transaction::query()
            ->with(['category', 'wallet', 'account'])
            ->where('user_id', $this->filters->user_id);

        // Aplica filtros
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

        if ($this->filters->status) {
            $query->where('status', $this->filters->status);
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
            'Conta',
            'Categoria',
            'Carteira',
            'Valor (R$)',
            'Data Vencimento',
            'Data Pagamento',
            'Status',
            'Parcela',
            'Conciliado',
            'ID Externo',
        ];
    }

    /**
     * Mapeia cada linha
     */
    public function map($transaction): array
    {
        return [
            $transaction->uuid,
            $transaction->account->name ?? '-',
            $transaction->category->name ?? '-',
            $transaction->wallet->name ?? '-',
            number_format($transaction->amount, 2, ',', '.'),
            $transaction->due_date->format('d/m/Y'),
            $transaction->paid_at?->format('d/m/Y') ?? '-',
            $transaction->status->label(),
            $transaction->total_installments > 1
                ? "{$transaction->installment_number}/{$transaction->total_installments}"
                : '-',
            $transaction->is_reconciled ? 'Sim' : 'Não',
            $transaction->external_id ?? '-',
        ];
    }

    /**
     * Estilos da planilha
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header em negrito com fundo cinza
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB']
                ],
            ],
        ];
    }

    /**
     * Título da planilha
     */
    public function title(): string
    {
        return 'Transações';
    }
}
