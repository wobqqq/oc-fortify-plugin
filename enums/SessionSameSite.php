<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Enums;

enum SessionSameSite: string
{
    case STRICT = 'strict';
    case LAX = 'lax';
}
