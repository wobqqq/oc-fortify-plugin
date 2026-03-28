<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Dto;

final readonly class SllCertificateCheckerDto
{
    public function __construct(
        public string $host,
        /** @var array<int, int> */
        public array  $ports,
    ) {
    }
}
