<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Wobqqq\Fortify\Instances\ConfigDtoInstance;
use Wobqqq\Fortify\Models\Fortify;
use Wobqqq\Fortify\Services\ConfigService;

function bootConfigService(): void
{
    ConfigDtoInstance::forgetInstance();
    (new ReflectionProperty(ConfigService::class, 'overrideConfig'))->setValue(null, false);

    app(ConfigService::class)->overrideConfig();
}

/**
 * @param array<string, mixed> $config
 */
function saveConfig(array $config): void
{
    Fortify::set('config', array_merge([
        'enabled' => true,
        'session_same_site' => 'strict',
        'session_lifetime' => 30,
        'session_secure' => true,
        'session_http_only' => true,
        'session_encrypt' => true,
        'password_policy_allow_reset' => false,
        'password_policy_require_uppercase' => true,
        'password_policy_require_lowercase' => true,
        'password_policy_require_number' => true,
        'password_policy_require_nonalpha' => true,
        'password_policy_expire_days' => 90,
        'password_policy_min_length' => 14,
        'backend_force_secure' => true,
        'backend_force_single_session' => true,
    ], $config));
}

it('leaves the application config alone while Fortify is disabled', function (): void {
    saveConfig(['enabled' => false]);

    bootConfigService();

    expect(Config::get('backend.password_policy.min_length'))->toBe(4)
        ->and(Config::get('backend.force_secure'))->toBeFalse();
});

it('applies every setting while Fortify is enabled', function (): void {
    saveConfig([]);

    bootConfigService();

    expect(Config::get('session.same_site'))->toBe('strict')
        ->and(Config::get('session.lifetime'))->toBe(30)
        ->and(Config::get('session.secure'))->toBeTrue()
        ->and(Config::get('session.http_only'))->toBeTrue()
        ->and(Config::get('session.encrypt'))->toBeTrue()
        ->and(Config::get('backend.password_policy'))->toMatchArray([
            'allow_reset' => false,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_number' => true,
            'require_nonalpha' => true,
            'expire_days' => 90,
            'min_length' => 14,
        ])
        ->and(Config::get('backend.force_secure'))->toBeTrue()
        ->and(Config::get('backend.force_single_session'))->toBeTrue();
});

it('writes the non-alphanumeric rule under the key October reads', function (): void {
    saveConfig(['password_policy_require_nonalpha' => true]);

    bootConfigService();

    expect(Config::get('backend.password_policy.require_nonalpha'))->toBeTrue()
        ->and(Config::has('backend.password_policy.require_non_alpha'))->toBeFalse();
});

it('turns password expiration off with zero days', function (): void {
    saveConfig(['password_policy_expire_days' => 0]);

    bootConfigService();

    expect(Config::get('backend.password_policy.expire_days'))->toBeFalse();
});

it('overrides the config once per request', function (): void {
    saveConfig(['password_policy_min_length' => 20]);
    bootConfigService();

    Config::set('backend.password_policy.min_length', 8);
    app(ConfigService::class)->overrideConfig();

    expect(Config::get('backend.password_policy.min_length'))->toBe(8);
});

it('disables Fortify and keeps the other settings', function (): void {
    saveConfig(['password_policy_min_length' => 16]);

    app(ConfigService::class)->disable();

    expect(Fortify::get('config.enabled'))->toBe(0)
        ->and(Fortify::get('config.password_policy_min_length'))->toBe(16);
});

it('disables Fortify from the console', function (): void {
    saveConfig([]);

    expect(Artisan::call('wobqqq.fortify:config:disable'))->toBe(0)
        ->and(Artisan::output())->toContain('Config disabled.');

    expect(Fortify::get('config.enabled'))->toBe(0);
});
