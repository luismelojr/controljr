<?php

namespace App\Enums;

enum AccountStatusEnum: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
