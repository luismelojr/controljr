<?php

namespace App\Enums;

enum TransactionStatusEnum: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case OVERDUE = 'overdue';

    /**
     * Get the Portuguese label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente',
            self::PAID => 'Paga',
            self::OVERDUE => 'Atrasada',
        };
    }
}
