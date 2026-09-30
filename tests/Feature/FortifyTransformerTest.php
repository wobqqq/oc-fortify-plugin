<?php

declare(strict_types=1);

use Wobqqq\Fortify\Enums\SessionSameSite;
use Wobqqq\Fortify\Models\Fortify;
use Wobqqq\Fortify\Transformers\FortifyTransformer;

it('falls back to safe defaults for an empty config', function (): void {
    Fortify::set('config', []);

    $dto = FortifyTransformer::configDto();

    expect($dto->enabled)->toBeFalse()
        ->and($dto->sessionSameSite)->toBeNull()
        ->and($dto->sessionHttpOnly)->toBeTrue()
        ->and($dto->sessionEncrypt)->toBeFalse()
        ->and($dto->sessionLifetime)->toBe(120)
        ->and($dto->passwordPolicyAllowReset)->toBeTrue()
        ->and($dto->passwordPolicyExpireDays)->toBe(0)
        ->and($dto->passwordPolicyMinLength)->toBe(4);
});

it('reads the values the settings form stores', function (): void {
    Fortify::set('config', [
        'enabled' => '1',
        'session_same_site' => 'lax',
        'session_lifetime' => '45',
        'password_policy_expire_days' => '60',
        'password_policy_min_length' => '12',
    ]);

    $dto = FortifyTransformer::configDto();

    expect($dto->enabled)->toBeTrue()
        ->and($dto->sessionSameSite)->toBe(SessionSameSite::LAX)
        ->and($dto->sessionLifetime)->toBe(45)
        ->and($dto->passwordPolicyExpireDays)->toBe(60)
        ->and($dto->passwordPolicyMinLength)->toBe(12);
});

it('reads the switch the previous versions stored for password expiration as off', function (mixed $stored): void {
    Fortify::set('config', ['password_policy_expire_days' => $stored]);

    expect(FortifyTransformer::configDto()->passwordPolicyExpireDays)->toBe(0);
})->with([true, false, null, '', -5]);

it('normalizes the sensitive files checker lists', function (): void {
    Fortify::set('tests', [
        'sensitive_files_checker_urls' => [['url' => 'https://example.com/'], ['url' => 'https://example.com/'], ['url' => '']],
        'sensitive_files_checker_paths' => [['path' => '/.env/'], ['path' => '.env'], ['path' => null], ['path' => 'config/app.php']],
    ]);

    $dto = FortifyTransformer::sensitiveFileCheckerDto();

    expect($dto->urls)->toBe(['https://example.com'])
        ->and($dto->paths)->toBe(['.env', 'config/app.php']);
});

it('parses the port lists and skips incomplete rows', function (): void {
    Fortify::set('tests', [
        'sensitive_tcp_ports_checker_ips' => [
            ['ip' => '10.0.0.1', 'ports' => '22,22,3306,'],
            ['ip' => '', 'ports' => '80'],
            ['ip' => '10.0.0.2', 'ports' => ''],
        ],
        'ssl_certificate_checker_hosts' => [
            ['host' => 'example.com', 'ports' => '443,993'],
            ['host' => 'example.org', 'ports' => null],
        ],
    ]);

    $tcp = FortifyTransformer::sensitiveTcpPortCheckerDtoList();
    $ssl = FortifyTransformer::sslCertificateCheckerDtoList();

    expect($tcp)->toHaveCount(1)
        ->and($tcp[0]->ip)->toBe('10.0.0.1')
        ->and(array_values($tcp[0]->ports))->toBe([22, 3306])
        ->and($ssl)->toHaveCount(1)
        ->and(array_values($ssl[0]->ports))->toBe([443, 993]);
});

it('marks a certificate check without dates as failed', function (): void {
    $failed = FortifyTransformer::sslCertificateCheckerTestResultDto('example.com', 443, ['success' => false, 'error' => 'refused']);
    $passed = FortifyTransformer::sslCertificateCheckerTestResultDto('example.com', 443, [
        'success' => true,
        'issued_on' => 1_700_000_000,
        'expires_on' => 1_800_000_000,
    ]);

    expect($failed->isPositive)->toBeFalse()
        ->and($failed->expiresOn)->toBeNull()
        ->and($passed->isPositive)->toBeTrue()
        ->and($passed->expiresOn?->getTimestamp())->toBe(1_800_000_000);
});
