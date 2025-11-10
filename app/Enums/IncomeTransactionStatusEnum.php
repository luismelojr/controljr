<?php

namespace App\Enums;

enum IncomeTransactionStatusEnum: string
{
    case PENDING = 'pending';
    case RECEIVED = 'received';
    case OVERDUE = 'overdue';
}
