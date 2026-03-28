<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Dto;

final readonly class SensitiveTcpPortCheckerTestResultDto
{
    public function __construct(
        public string $ip,
        public int $port,
        public string $status,
        public bool $isPositive,
    ) {
    }
}
