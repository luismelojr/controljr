<?php

namespace App\Enums;

enum IncomeTransactionStatusEnum: string
{
    case PENDING = 'pending';
    case RECEIVED = 'received';
    case OVERDUE = 'overdue';

    /**
     * Get the Portuguese label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente',
            self::RECEIVED => 'Recebida',
            self::OVERDUE => 'Atrasada',
        };
    }
}
