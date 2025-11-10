<?php

namespace App\Enums;

enum RecurrenceTypeEnum: string
{
    case ONE_TIME = 'one_time';
    case INSTALLMENTS = 'installments';
    case RECURRING = 'recurring';
}
