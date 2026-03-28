<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Enums;

enum WidgetItemColor: string
{
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case DANGER = 'danger';
    case INFO = 'info';
    case DEFAULT = 'default';
}
