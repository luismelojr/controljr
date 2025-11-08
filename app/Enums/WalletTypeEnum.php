<?php

namespace App\Enums;

enum WalletTypeEnum: string
{
    case CARD_CREDIT = 'card_credit';
    case BANK_ACCOUNT = 'bank_account';
    case OTHER = 'other';
}
