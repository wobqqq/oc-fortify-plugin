<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Services;

use Config;
use Wobqqq\Fortify\Instances\ConfigDtoInstance;
use Wobqqq\Fortify\Models\Fortify;

final class ConfigService
{
    private static bool $overrideConfig = false;

    public function overrideConfig(): void
    {
        if (self::$overrideConfig) {
            return;
        }

        self::$overrideConfig = true;

        $fortifyConfigDto = ConfigDtoInstance::instance()->get();

        if (!$fortifyConfigDto->enabled) {
            return;
        }

        Config::set('session.same_site', $fortifyConfigDto->sessionSameSite?->value);
        Config::set('session.secure', $fortifyConfigDto->sessionSecure);
        Config::set('session.http_only', $fortifyConfigDto->sessionHttpOnly);
        Config::set('session.encrypt', $fortifyConfigDto->sessionEncrypt);
        Config::set('session.lifetime', $fortifyConfigDto->sessionLifetime);

        Config::set('backend.password_policy.allow_reset', $fortifyConfigDto->passwordPolicyAllowReset);
        Config::set('backend.password_policy.require_uppercase', $fortifyConfigDto->passwordPolicyRequireUppercase);
        Config::set('backend.password_policy.require_lowercase', $fortifyConfigDto->passwordPolicyRequireLowercase);
        Config::set('backend.password_policy.require_number', $fortifyConfigDto->passwordPolicyRequireNumber);
        Config::set('backend.password_policy.require_nonalpha', $fortifyConfigDto->passwordPolicyRequireNonAlpha);
        Config::set(
            'backend.password_policy.expire_days',
            $fortifyConfigDto->passwordPolicyExpireDays > 0 ? $fortifyConfigDto->passwordPolicyExpireDays : false,
        );
        Config::set('backend.password_policy.min_length', $fortifyConfigDto->passwordPolicyMinLength);

        Config::set('backend.force_secure', $fortifyConfigDto->backendForceSecure);
        Config::set('backend.force_single_session', $fortifyConfigDto->backendForceSingleSession);
    }

    public function disable(): void
    {
        /** @var array<string, mixed>|\Illuminate\Support\Collection<int, mixed> $config */
        $config = Fortify::get('config');

        if ($config instanceof \Illuminate\Support\Collection) {
            $config = $config->toArray();
        }

        $config = !is_array($config) ? [] : $config;

        $config['enabled'] = 0;

        Fortify::set('config', $config);
    }
}
