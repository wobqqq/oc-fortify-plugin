<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Dto;

final readonly class SensitiveFileCheckerTestResultDto
{
    public function __construct(
        public string $url,
        public string $status,
        public bool $isPositive,
    ) {
    }
}
