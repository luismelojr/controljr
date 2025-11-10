<?php

namespace App\Enums;

enum IncomeRecurrenceTypeEnum: string
{
    case ONE_TIME = 'one_time';
    case INSTALLMENTS = 'installments';
    case RECURRING = 'recurring';
}
