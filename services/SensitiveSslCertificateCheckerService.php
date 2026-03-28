<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Services;

use Config;
use Wobqqq\Fortify\Client\SslSecurityCheckerClient;
use Wobqqq\Fortify\Dto\SllCertificateCheckerTestResultDto;
use Wobqqq\Fortify\Instances\SslCertificateCheckerDtoListInstance;
use Wobqqq\Fortify\Transformers\FortifyTransformer;

final readonly class SensitiveSslCertificateCheckerService
{
    public function __construct(private SslSecurityCheckerClient $sslSecurityCheckerClient)
    {
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function generateDefaultSettingsData(): array
    {
        /** @var string $appUrl */
        $appUrl = Config::get('app.url');
        $appUrl = rtrim($appUrl, '/');

        $host = parse_url($appUrl, PHP_URL_HOST);

        if (!$host) {
            $host = $appUrl;
        }

        $host = preg_replace('/^www\./i', '', $host);

        /** @var array<int, array<string, string>> $sslCertificateCheckerHosts */
        $sslCertificateCheckerHosts = [['host' => $host, 'ports' => '443']];

        return $sslCertificateCheckerHosts;
    }

    /**
     * @return array{0: array<string, SllCertificateCheckerTestResultDto>, 1: int}
     */
    public function check(string $host): array
    {
        $sslCertificateCheckerDtoList = SslCertificateCheckerDtoListInstance::instance()->get();

        $ports = [];

        $numberOfHostsWithoutSsl = 0;

        foreach ($sslCertificateCheckerDtoList as $sslCertificateCheckerDto) {
            if ($sslCertificateCheckerDto->host !== $host) {
                continue;
            }

            foreach ($sslCertificateCheckerDto->ports as $port) {
                $responses = $this->sslSecurityCheckerClient->request(
                    $sslCertificateCheckerDto->host,
                    $port,
                );

                $sslCertificateCheckerTestResultDto = FortifyTransformer::sslCertificateCheckerTestResultDto(
                    $sslCertificateCheckerDto->host,
                    $port,
                    $responses,
                );

                $key = sprintf(
                    '%s-%s-%s',
                    (string)$sslCertificateCheckerTestResultDto->isPositive,
                    $port,
                    $sslCertificateCheckerTestResultDto->host,
                );

                $ports[$key] = $sslCertificateCheckerTestResultDto;
                if (!$sslCertificateCheckerTestResultDto->isPositive) {
                    $numberOfHostsWithoutSsl++;
                }
            }
        }

        krsort($ports);

        return [$ports, $numberOfHostsWithoutSsl];
    }
}
