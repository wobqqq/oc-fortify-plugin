<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Contracts;

interface TcpPortProbe
{
    public const OPENED = 'opened';

    public const CLOSED = 'closed';

    /**
     * @param array<int, int> $ports
     *
     * @return array<int, string> port => self::OPENED or self::CLOSED
     */
    public function request(string $ip, array $ports, int $timeout = 2): array;
}
