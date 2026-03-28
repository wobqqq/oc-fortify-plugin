<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Enums;

enum View: string
{
    case DENIED = 'wobqqq.fortify::denied';
    case BAD_REQUEST = 'wobqqq.fortify::bad-request';
}
