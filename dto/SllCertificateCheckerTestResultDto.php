<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Dto;

use Illuminate\Support\Carbon;

final readonly class SllCertificateCheckerTestResultDto
{
    public function __construct(
        public string $host,
        public int $port,
        public ?Carbon $issuedOn,
        public ?Carbon $expiresOn,
        public bool $isPositive,
    ) {
    }
}
