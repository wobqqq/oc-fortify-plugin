<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Contracts;

interface TlsCertificateProbe
{
    /**
     * @return array<string, mixed> success, and issued_on / expires_on timestamps or an error
     */
    public function request(string $host, int $port = 443): array;
}
