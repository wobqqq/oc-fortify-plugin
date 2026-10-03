<?php

declare(strict_types=1);

arch('every file declares strict types')
    ->expect('Wobqqq\Fortify')
    ->toUseStrictTypes();

arch('no debugging calls are left behind')
    ->expect(['dd', 'dump', 'var_dump', 'print_r', 'ray', 'die', 'exit'])
    ->not->toBeUsed();

arch('data transfer objects are immutable')
    ->expect('Wobqqq\Fortify\Dto')
    ->toBeFinal()
    ->toBeReadonly();

arch('enums back every code the plugin shares')
    ->expect('Wobqqq\Fortify\Enums')
    ->toBeStringBackedEnums();

arch('services do not reach for the request directly')
    ->expect('Wobqqq\Fortify\Services')
    ->not->toUse(['Input', 'Request', Illuminate\Http\Request::class]);

arch('the network checks are reached through their contracts')
    ->expect('Wobqqq\Fortify\Contracts')
    ->toBeInterfaces();

arch('the network clients are not extended')
    ->expect('Wobqqq\Fortify\Client')
    ->toBeFinal();

arch('each network client implements its contract', function (): void {
    expect(Wobqqq\Fortify\Client\SensitiveFileCheckerClient::class)->toImplement(Wobqqq\Fortify\Contracts\HttpStatusProbe::class)
        ->and(Wobqqq\Fortify\Client\SensitiveTcpPortCheckerClient::class)->toImplement(Wobqqq\Fortify\Contracts\TcpPortProbe::class)
        ->and(Wobqqq\Fortify\Client\SslSecurityCheckerClient::class)->toImplement(Wobqqq\Fortify\Contracts\TlsCertificateProbe::class);
});
