<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Validator;
use Wobqqq\Fortify\Enums\FortifyEvent;
use Wobqqq\Fortify\Models\Fortify;

/**
 * @param array<string, mixed> $data
 */
function fortifyValidation(array $data): Illuminate\Validation\Validator
{
    /** @var Illuminate\Validation\Validator $validator */
    $validator = Validator::make($data, (new Fortify())->rules);

    return $validator;
}

it('starts from the application config and the default checks', function (): void {
    config(['backend.password_policy.expire_days' => 45, 'session.lifetime' => 60]);

    Fortify::clearInternalCache();
    $settings = Fortify::instance();

    expect($settings->config)->toMatchArray([
        'enabled' => false,
        'password_policy_expire_after_days' => 45,
        'session_lifetime' => 60,
    ])->and($settings->tests)->toMatchArray(['sensitive_files_checker_urls' => [['url' => 'https://fortify.test']]]);
});

it('lets the modules add their default settings', function (): void {
    Event::listen(FortifyEvent::MODEL_FORTIFY_INIT_SETTINGS_DATA->value, function (Fortify &$fortify): void {
        $fortify->csp = ['cms_enabled' => false];
    });

    Fortify::clearInternalCache();

    expect(Fortify::instance()->csp)->toBe(['cms_enabled' => false]);
});

it('lets the modules offer their own pages', function (): void {
    Event::listen(FortifyEvent::VIEW_DENIED->value, function (array &$views): void {
        $views['acme.theme::blocked'] = 'acme.theme::blocked';
    });

    expect((new Fortify())->getViewOptions())->toHaveKeys(['wobqqq.fortify::denied', 'wobqqq.fortify::bad-request', 'acme.theme::blocked'])
        ->and((new Fortify())->getSessionSameSiteOptions())->toBe(['lax' => 'lax', 'strict' => 'strict']);
});

it('saves the defaults of a site served from an IP address or localhost', function (string $url): void {
    config(['app.url' => $url]);
    Fortify::clearInternalCache();

    $settings = Fortify::instance();

    expect($settings->validate())->toBeTrue();
})->with(['http://127.0.0.1:8080', 'http://localhost', 'https://www.example.com']);

it('validates the password expiration as a number of days', function (mixed $days, bool $passes): void {
    $validator = fortifyValidation([
        'config' => ['password_policy_min_length' => 12, 'session_lifetime' => 30, 'password_policy_expire_after_days' => $days],
    ]);

    expect($validator->passes())->toBe($passes);
})->with([
    [0, true],
    [90, true],
    [3650, true],
    [3651, false],
    [-1, false],
    ['soon', false],
]);

it('only accepts web addresses, IP addresses and host names for the checks', function (string $field, string $value, bool $passes): void {
    /** @var array<string, mixed> $data */
    $data = ['config' => ['password_policy_min_length' => 12, 'session_lifetime' => 30]];
    data_set($data, $field, $value);
    /** @var array<string, mixed> $data */

    expect(fortifyValidation($data)->passes())->toBe($passes);
})->with([
    ['tests.sensitive_files_checker_urls.0.url', 'https://example.com/a-rather-long-path/that-is-still-a-valid-site', true],
    ['tests.sensitive_files_checker_urls.0.url', 'file:///etc/passwd', false],
    ['tests.sensitive_files_checker_urls.0.url', 'gopher://example.com', false],
    ['tests.sensitive_tcp_ports_checker_ips.0.ip', '2001:db8::1', true],
    ['tests.sensitive_tcp_ports_checker_ips.0.ip', '10.0.0.0/24', false],
    ['tests.sensitive_tcp_ports_checker_ips.0.ports', '22,3306', true],
    ['tests.sensitive_tcp_ports_checker_ips.0.ports', '22;rm -rf', false],
    ['tests.ssl_certificate_checker_hosts.0.host', 'mail.example.com', true],
    ['tests.ssl_certificate_checker_hosts.0.host', 'example.com/../', false],
    ['tests.ssl_certificate_checker_hosts.0.host', 'localhost', true],
    ['tests.ssl_certificate_checker_hosts.0.host', '127.0.0.1', true],
    ['tests.ssl_certificate_checker_hosts.0.host', 'staging-01.example.co.uk', true],
    ['tests.ssl_certificate_checker_hosts.0.host', '-bad.example.com', false],
    ['tests.ssl_certificate_checker_hosts.0.host', 'example.com; rm', false],
]);
