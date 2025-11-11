<?php

namespace App\Enums;

enum NotificationTypeEnum: string
{
    case INFO = 'info';
    case WARNING = 'warning';
    case DANGER = 'danger';
    case SUCCESS = 'success';
}
