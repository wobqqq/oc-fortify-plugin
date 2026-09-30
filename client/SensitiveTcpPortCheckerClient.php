<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Client;

class SensitiveTcpPortCheckerClient
{
    public const OPENED = 'opened';

    public const CLOSED = 'closed';

    /**
     * Every port is dialled at once and the whole check waits at most $timeout seconds,
     * however many ports are listed.
     *
     * @param array<int, int> $ports
     *
     * @return array<int, string>
     */
    public function request(string $ip, array $ports, int $timeout = 2): array
    {
        $host = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false ? sprintf('[%s]', $ip) : $ip;

        $results = [];
        $pending = [];

        foreach ($ports as $port) {
            $results[$port] = self::CLOSED;

            $socket = @stream_socket_client(
                sprintf('tcp://%s:%d', $host, $port),
                $errorCode,
                $errorMessage,
                $timeout,
                STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT,
            );

            if ($socket !== false) {
                $pending[$port] = $socket;
            }
        }

        $deadline = microtime(true) + $timeout;

        while ($pending !== [] && ($left = $deadline - microtime(true)) > 0) {
            $read = null;
            $write = array_values($pending);
            $except = null;

            $seconds = (int)$left;
            $microseconds = (int)(($left - $seconds) * 1_000_000);

            if (@stream_select($read, $write, $except, $seconds, $microseconds) < 1) {
                break;
            }

            foreach ($write as $socket) {
                $port = array_search($socket, $pending, true);

                if ($port === false) {
                    continue;
                }

                if (stream_socket_get_name($socket, true) !== false) {
                    $results[$port] = self::OPENED;
                }

                fclose($socket);
                unset($pending[$port]);
            }
        }

        foreach ($pending as $socket) {
            fclose($socket);
        }

        return $results;
    }
}
