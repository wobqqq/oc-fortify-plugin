<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Client;

final class SensitiveTcpPortCheckerClient
{
    /**
     * @param string $ip
     * @param array<int, int> $ports
     * @param int $timeout
     * @return array<int, string>
     */
    public function request(string $ip, array $ports, int $timeout = 2): array
    {
        $results = [];

        foreach ($ports as $port) {
            $context = stream_context_create();

            $connection = @stream_socket_client(
                "tcp://{$ip}:{$port}",
                $errorCode,
                $errorMessage,
                $timeout,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if ($connection !== false) {
                $results[$port] = 'opened';
                fclose($connection);
            } else {
                $results[$port] = 'closed';
            }
        }

        return $results;
    }
}
