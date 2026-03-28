<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Services;

use Wobqqq\Fortify\Client\SensitiveTcpPortCheckerClient;
use Wobqqq\Fortify\Dto\SensitiveTcpPortCheckerTestResultDto;
use Wobqqq\Fortify\Instances\SensitiveTcpPortCheckerDtoListInstance;
use Wobqqq\Fortify\Transformers\FortifyTransformer;

final readonly class SensitiveTcpPortCheckerService
{
    private const PORTS = [
        8080,
        21,
        22,
        23,
        25,
        3306,
        5432,
        6379,
        11211,
        27017,
        2375,
        9200,
    ];

    public function __construct(private SensitiveTcpPortCheckerClient $sensitiveTcpPortCheckerClient)
    {
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function generateDefaultSettingsData(): array
    {
        /** @var array<int, array<string, string>> $sensitiveTcpPortCheckerIps */
        $sensitiveTcpPortCheckerIps = [['ip' => '', 'ports' => implode(',', self::PORTS)]];

        return $sensitiveTcpPortCheckerIps;
    }

    /**
     * @return array{0: array<string, SensitiveTcpPortCheckerTestResultDto>, 1: int}
     */
    public function check(string $ip): array
    {
        $fortifySensitiveTcpPortCheckerDtoList = SensitiveTcpPortCheckerDtoListInstance::instance()->get();

        $ports = [];

        $numberOfPublicPorts = 0;

        foreach ($fortifySensitiveTcpPortCheckerDtoList as $fortifySensitiveTcpPortCheckerDto) {
            if ($fortifySensitiveTcpPortCheckerDto->ip !== $ip) {
                continue;
            }

            $responses = $this->sensitiveTcpPortCheckerClient->request(
                $fortifySensitiveTcpPortCheckerDto->ip,
                $fortifySensitiveTcpPortCheckerDto->ports,
            );

            foreach ($responses as $port => $status) {
                $isPositive = $status !== 'opened';
                $key = sprintf('%s-%s-%s', $status, $port, $fortifySensitiveTcpPortCheckerDto->ip);
                $ports[$key] = FortifyTransformer::sensitiveTcpPortCheckerTestResultDto(
                    $fortifySensitiveTcpPortCheckerDto->ip,
                    (int)$port,
                    $status,
                    $isPositive,
                );

                if (!$isPositive) {
                    $numberOfPublicPorts++;
                }
            }
        }

        krsort($ports);

        return [$ports, $numberOfPublicPorts];
    }
}
