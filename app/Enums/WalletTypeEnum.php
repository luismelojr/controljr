<?php

namespace App\Enums;

enum WalletTypeEnum: string
{
    case CARD_CREDIT = 'card_credit';
    case BANK_ACCOUNT = 'bank_account';
    case OTHER = 'other';

    /**
     * Get the Portuguese label for the wallet type
     */
    public function label(): string
    {
        return match ($this) {
            self::CARD_CREDIT => 'Cartão de Crédito',
            self::BANK_ACCOUNT => 'Conta Bancária',
            self::OTHER => 'Outro',
        };
    }
}
