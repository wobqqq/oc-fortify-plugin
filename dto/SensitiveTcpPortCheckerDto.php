<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Dto;

final readonly class SensitiveTcpPortCheckerDto
{
    public function __construct(
        public string $ip,
        /** @var array<int, int> */
        public array  $ports,
    ) {
    }
}
