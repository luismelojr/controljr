<?php

namespace App\Enums;

enum RecurrenceTypeEnum: string
{
    case ONE_TIME = 'one_time';
    case INSTALLMENTS = 'installments';
    case RECURRING = 'recurring';

    /**
     * Get the Portuguese label for the recurrence type
     */
    public function label(): string
    {
        return match ($this) {
            self::ONE_TIME => 'Única',
            self::INSTALLMENTS => 'Parcelada',
            self::RECURRING => 'Recorrente',
        };
    }
}
