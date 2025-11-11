<?php

namespace App\Enums;

enum AlertTypeEnum: string
{
    case CREDIT_CARD_USAGE = 'credit_card_usage';
    case BILL_DUE_DATE = 'bill_due_date';
    case ACCOUNT_BALANCE = 'account_balance';
    case BUDGET_EXCEEDED = 'budget_exceeded';
}
