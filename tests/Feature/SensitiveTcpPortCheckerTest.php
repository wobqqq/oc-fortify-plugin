<?php

declare(strict_types=1);

use Wobqqq\Fortify\Client\SensitiveTcpPortCheckerClient;
use Wobqqq\Fortify\Models\Fortify;
use Wobqqq\Fortify\Services\SensitiveTcpPortCheckerService;
use Wobqqq\Fortify\Tests\Support\Sockets;

it('tells an open port from a closed one', function (): void {
    [$server, $open] = Sockets::listen();
    $closed = Sockets::closedPort();

    $results = (new SensitiveTcpPortCheckerClient())->request('127.0.0.1', [$open, $closed], 1);

    fclose($server);

    expect($results)->toBe([
        $open => SensitiveTcpPortCheckerClient::OPENED,
        $closed => SensitiveTcpPortCheckerClient::CLOSED,
    ]);
});

it('checks every port within one timeout', function (): void {
    $start = microtime(true);

    (new SensitiveTcpPortCheckerClient())->request('10.255.255.1', [21, 22, 23, 25, 3306, 5432], 1);

    expect(microtime(true) - $start)->toBeLessThan(2.5);
});

it('connects to an IPv6 address', function (): void {
    [$server, $port] = Sockets::listen('[::1]');

    $results = (new SensitiveTcpPortCheckerClient())->request('::1', [$port], 1);

    fclose($server);

    expect($results)->toBe([$port => SensitiveTcpPortCheckerClient::OPENED]);
})->skip(!Sockets::supportsIpv6(), 'IPv6 loopback is not available.');

it('counts the open ports of a listed address only', function (): void {
    [$server, $open] = Sockets::listen();
    $closed = Sockets::closedPort();

    Fortify::set('tests', [
        'sensitive_tcp_ports_checker_ips' => [['ip' => '127.0.0.1', 'ports' => sprintf('%d,%d', $open, $closed)]],
    ]);

    $service = app(SensitiveTcpPortCheckerService::class);
    [$ports, $public] = $service->check('127.0.0.1');
    [$unlisted] = $service->check('192.0.2.1');

    fclose($server);

    expect($public)->toBe(1)
        ->and($ports)->toHaveCount(2)
        ->and($unlisted)->toBe([]);
});

it('suggests the commonly exposed ports', function (): void {
    expect(app(SensitiveTcpPortCheckerService::class)->generateDefaultSettingsData()[0]['ports'])
        ->toContain('22')
        ->toContain('3306')
        ->toContain('6379');
});
