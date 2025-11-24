<?php

namespace App\Enums;

enum AccountStatusEnum: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    /**
     * Get the Portuguese label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Ativa',
            self::COMPLETED => 'Completa',
            self::CANCELLED => 'Cancelada',
        };
    }
}
