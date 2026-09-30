<?php

declare(strict_types=1);

use Wobqqq\Fortify\Client\SslSecurityCheckerClient;
use Wobqqq\Fortify\Models\Fortify;
use Wobqqq\Fortify\Services\SensitiveSslCertificateCheckerService;
use Wobqqq\Fortify\Tests\Support\Sockets;

it('reports the certificate of every listed port', function (): void {
    Fortify::set('tests', ['ssl_certificate_checker_hosts' => [['host' => 'example.com', 'ports' => '443,993']]]);

    app()->instance(SslSecurityCheckerClient::class, new class () extends SslSecurityCheckerClient {
        public function request(string $host, int $port = 443): array
        {
            return $port === 443
                ? ['success' => true, 'issued_on' => 1_700_000_000, 'expires_on' => 1_900_000_000]
                : ['success' => false, 'error' => 'Connection refused'];
        }
    });

    [$ports, $failed] = app(SensitiveSslCertificateCheckerService::class)->check('example.com');

    expect($failed)->toBe(1)
        ->and($ports)->toHaveCount(2)
        ->and(array_values($ports)[0]->isPositive)->toBeTrue();
});

it('does not connect to a host that is not listed', function (): void {
    Fortify::set('tests', ['ssl_certificate_checker_hosts' => [['host' => 'example.com', 'ports' => '443']]]);

    [$ports, $failed] = app(SensitiveSslCertificateCheckerService::class)->check('internal.example');

    expect($ports)->toBe([])->and($failed)->toBe(0);
});

it('answers an unreachable host with an error', function (): void {
    $result = (new SslSecurityCheckerClient())->request('127.0.0.1', Sockets::closedPort());

    expect($result['success'])->toBeFalse()->and($result)->toHaveKey('error');
});

it('suggests the application host without www', function (): void {
    config(['app.url' => 'https://www.example.com/']);

    expect(app(SensitiveSslCertificateCheckerService::class)->generateDefaultSettingsData())
        ->toBe([['host' => 'example.com', 'ports' => '443']]);
});
