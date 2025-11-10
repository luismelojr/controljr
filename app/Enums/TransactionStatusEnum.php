<?php

namespace App\Enums;

enum TransactionStatusEnum: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
}
