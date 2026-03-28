<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Services;

use Backend;
use Config;
use Event;
use Lang;
use System\Classes\PluginManager;
use System\Classes\UpdateManager;
use Wobqqq\Fortify\Cache\BackendUserCache;
use Wobqqq\Fortify\Enums\ButtonAction;
use Wobqqq\Fortify\Enums\FortifyEvent;
use Wobqqq\Fortify\Enums\InsecureAdminUri;
use Wobqqq\Fortify\Enums\SessionSameSite;
use Wobqqq\Fortify\Enums\WidgetItemColor;
use Wobqqq\Fortify\Instances\ConfigDtoInstance;
use Wobqqq\Fortify\Transformers\FortifyTransformer;

final readonly class WidgetService
{
    /**
     * @return array<int, mixed>
     */
    public function getWidgetGroups(): array
    {
        $groups = [];

        $this->initInfoData($groups);
        $this->initModulesData($groups);
        $this->initConfigData($groups);

        return $groups;
    }

    /**
     * @param array<int, mixed> $groups
     * @return void
     */
    private function initInfoData(array &$groups): void
    {
        $testsLink = FortifyTransformer::widgetItemLinkDto(
            'wobqqq.fortify::lang.buttons.edit',
            Backend::url('system/settings/update/wobqqq/fortify/fortify#primarytab-tests'),
            'icon-wrench',
        );


        $list = [];

        $color = Config::get('app.debug') === false
            ? WidgetItemColor::SUCCESS
            : WidgetItemColor::DANGER;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.debug_mode_is_disabled',
            [],
            $color,
            'icon-info-circle',
        );

        $color = Config::get('app.env') === 'production'
            ? WidgetItemColor::SUCCESS
            : WidgetItemColor::DANGER;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.app_production_env_is_enabled',
            [],
            $color,
            'icon-info-circle',
        );

        /** @var string|null $backendUri */
        $backendUri = Config::get('backend.uri');
        $backendUri = trim((string)$backendUri, '/');
        if (InsecureAdminUri::tryFrom($backendUri) === null) {
            $color = WidgetItemColor::SUCCESS;
            /** @var string $name */
            $name = Lang::get(
                'wobqqq.fortify::lang.fields.vulnerable_backend_uri_success',
                ['uri' => sprintf('/%s', $backendUri),
                ]
            );
        } else {
            $color = WidgetItemColor::DANGER;
            /** @var string $name */
            $name = Lang::get(
                'wobqqq.fortify::lang.fields.vulnerable_backend_uri_warning',
                ['uri' => sprintf('/%s', $backendUri),
                ]
            );
        }
        $list[] = FortifyTransformer::widgetGroupItemDto(
            $name,
            [],
            $color,
            'icon-info-circle',
        );

        /** @var BackendUserCache $backendUserCache */
        $backendUserCache = app(BackendUserCache::class);
        $numberOfSuperusers = $backendUserCache->countSuperusers();
        if ($numberOfSuperusers <= 3) {
            $color = WidgetItemColor::SUCCESS;
            /** @var string $name */
            $name = Lang::get(
                'wobqqq.fortify::lang.fields.vulnerable_number_of_superusers_success',
                ['number_of_superusers' => $numberOfSuperusers],
            );
        } else {
            $color = WidgetItemColor::WARNING;
            /** @var string $name */
            $name = Lang::get(
                'wobqqq.fortify::lang.fields.vulnerable_number_of_superusers_warning',
                ['number_of_superusers' => $numberOfSuperusers],
            );
        }
        $numberOfSuperusersLink = FortifyTransformer::widgetItemLinkDto(
            'wobqqq.fortify::lang.buttons.view',
            Backend::url('backend/users'),
            'icon-eye',
        );
        $list[] = FortifyTransformer::widgetGroupItemDto(
            $name,
            [$numberOfSuperusersLink],
            $color,
            'icon-info-circle',
        );

        $numberOfOutdatedAdmins = $backendUserCache->countOutdatedAdmins();
        $color = $numberOfOutdatedAdmins <= 0 ? WidgetItemColor::SUCCESS : WidgetItemColor::DANGER  ;
        /** @var string $name */
        $name = Lang::get(
            'wobqqq.fortify::lang.fields.vulnerable_number_of_outdated_administrators',
            ['number' => $numberOfOutdatedAdmins],
        );
        $numberOfOutdatedAdminsLink = FortifyTransformer::widgetItemLinkDto(
            'wobqqq.fortify::lang.buttons.view',
            Backend::url('backend/users'),
            'icon-eye',
        );
        $list[] = FortifyTransformer::widgetGroupItemDto(
            $name,
            [$numberOfOutdatedAdminsLink],
            $color,
            'icon-info-circle',
        );

        /** @var BackendUserCache $backendUserCache */
        $backendUserCache = app(BackendUserCache::class);
        $logins = $backendUserCache->getLoginsByLogins(SensitiveAdministratorLoginCheckerService::LOGINS);
        $color = empty($logins) ? WidgetItemColor::SUCCESS : WidgetItemColor::DANGER;
        /** @var string $name */
        $name = Lang::get(
            'wobqqq.fortify::lang.fields.sensitive_administrator_login_checker',
            ['logins' => (empty($logins) ? '0' : implode(', ', $logins))],
        );
        $list[] = FortifyTransformer::widgetGroupItemDto(
            $name,
            [
                FortifyTransformer::widgetItemLinkDto(
                    'wobqqq.fortify::lang.buttons.view',
                    Backend::url('backend/users'),
                    'icon-eye',
                ),
            ],
            $color,
            'icon-info-circle',
        );

        $color = (bool)UpdateManager::instance()->check() === false
            ? WidgetItemColor::SUCCESS
            : WidgetItemColor::WARNING;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.status_updates_pending',
            [
                FortifyTransformer::widgetItemLinkDto(
                    'wobqqq.fortify::lang.buttons.update',
                    Backend::url('system/updates'),
                    'icon-refresh',
                ),
            ],
            $color,
            'icon-info-circle',
        );

        $sensitiveFilesCheckerButton = FortifyTransformer::widgetItemButtonDto(
            'wobqqq.fortify::lang.buttons.run_test',
            ButtonAction::SENSITIVE_FILES_CHECKER_OPEN_MODAL->value,
            'icon-rocket',
        );
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.sensitive_files_checker',
            [
                $testsLink,
                $sensitiveFilesCheckerButton,
            ],
            WidgetItemColor::INFO,
            'icon-info-circle',
        );

        $sensitiveFilesCheckerButton = FortifyTransformer::widgetItemButtonDto(
            'wobqqq.fortify::lang.buttons.run_test',
            ButtonAction::SENSITIVE_TCP_PORTS_CHECKER_OPEN_MODAL->value,
            'icon-rocket',
        );
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.sensitive_tcp_ports_checker',
            [
                $testsLink,
                $sensitiveFilesCheckerButton,
            ],
            WidgetItemColor::INFO,
            'icon-info-circle',
        );

        $sensitiveFilesCheckerButton = FortifyTransformer::widgetItemButtonDto(
            'wobqqq.fortify::lang.buttons.run_test',
            ButtonAction::SSL_CERTIFICATE_CHECKER_OPEN_MODAL->value,
            'icon-rocket',
        );
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.ssl_certificate_checker',
            [
                $testsLink,
                $sensitiveFilesCheckerButton,
            ],
            WidgetItemColor::INFO,
            'icon-info-circle',
        );

        $groups[] = FortifyTransformer::widgetGroupDto(
            'wobqqq.fortify::lang.fields.info',
            $list,
            'icon-info-circle',
        );
    }

    /**
     * @param array<int, mixed> $groups
     * @return void
     */
    private function initModulesData(array &$groups): void
    {
        $list = [];

        $buttons = [];
        if (PluginManager::instance()->hasPlugin('wobqqq.fortifyadminipaccess')) {
            if (PluginManager::instance()->isDisabled('Wobqqq.FortifyAdminIpAccess')) {
                $buttons = [
                    FortifyTransformer::widgetItemLinkDto(
                        'wobqqq.fortify::lang.buttons.enable_plugin',
                        Backend::url('system/updates/manage'),
                        'icon-plug',
                    ),
                ];
            }
        } else {
            $buttons = [
                FortifyTransformer::widgetItemLinkDto(
                    'wobqqq.fortify::lang.buttons.install_plugin',
                    'https://octobercms.com/plugin/wobqqq-fortifyadminipaccess',
                    'icon-plus',
                    '_blank',
                ),
            ];
        }
        /** @var string $name */
        $name = Lang::get('wobqqq.fortify::lang.fields.admin_ip_access');
        $widgetGroupItemDto = FortifyTransformer::widgetGroupItemDto(
            $name,
            $buttons,
            WidgetItemColor::DANGER,
            'icon-ban',
        );
        /** @phpstan-ignore-next-line */
        Event::fire(FortifyEvent::SERVICES_WIDGET_GROUP_ITEM_ADMIN_IP_ACCESS->value, [&$widgetGroupItemDto]);
        $list[] = $widgetGroupItemDto;

        $buttons = [];
        if (PluginManager::instance()->hasPlugin('wobqqq.fortifyipblocker')) {
            if (PluginManager::instance()->isDisabled('Wobqqq.FortifyIpBlocker')) {
                $buttons = [
                    FortifyTransformer::widgetItemLinkDto(
                        'wobqqq.fortify::lang.buttons.enable_plugin',
                        Backend::url('system/updates/manage'),
                        'icon-plug',
                    ),
                ];
            }
        } else {
            $buttons = [
                FortifyTransformer::widgetItemLinkDto(
                    'wobqqq.fortify::lang.buttons.install_plugin',
                    'https://octobercms.com/plugin/wobqqq-fortifyipblocker',
                    'icon-plus',
                    '_blank',
                ),
            ];
        }
        /** @var string $name */
        $name = Lang::get('wobqqq.fortify::lang.fields.ip_blocker');
        $widgetGroupItemDto = FortifyTransformer::widgetGroupItemDto(
            $name,
            $buttons,
            WidgetItemColor::DANGER,
            'icon-ban',
        );
        /** @phpstan-ignore-next-line */
        Event::fire(FortifyEvent::SERVICES_WIDGET_GROUP_ITEM_IP_BLOCKER->value, [&$widgetGroupItemDto]);
        $list[] = $widgetGroupItemDto;

        $buttons = [];
        if (PluginManager::instance()->hasPlugin('wobqqq.fortifysmartipblocker')) {
            if (PluginManager::instance()->isDisabled('Wobqqq.FortifySmartIpBlocker')) {
                $buttons = [
                    FortifyTransformer::widgetItemLinkDto(
                        'wobqqq.fortify::lang.buttons.enable_plugin',
                        Backend::url('system/updates/manage'),
                        'icon-plug',
                    ),
                ];
            }
        } else {
            $buttons = [
                FortifyTransformer::widgetItemLinkDto(
                    'wobqqq.fortify::lang.buttons.install_plugin',
                    'https://octobercms.com/plugin/wobqqq-fortifysmartipblocker',
                    'icon-plus',
                    '_blank',
                ),
            ];
        }
        $widgetGroupItemDto = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.smart_ip_blocker',
            $buttons,
            WidgetItemColor::DANGER,
            'icon-ban',
        );
        /** @phpstan-ignore-next-line */
        Event::fire(FortifyEvent::SERVICES_WIDGET_GROUP_ITEM_SMART_IP_BLOCKER->value, [&$widgetGroupItemDto]);
        $list[] = $widgetGroupItemDto;

        $buttons = [];
        if (PluginManager::instance()->hasPlugin('wobqqq.fortifycsp')) {
            if (PluginManager::instance()->isDisabled('Wobqqq.FortifyCsp')) {
                $buttons = [
                    FortifyTransformer::widgetItemLinkDto(
                        'wobqqq.fortify::lang.buttons.enable_plugin',
                        Backend::url('system/updates/manage'),
                        'icon-plug',
                    ),
                ];
            }
        } else {
            $buttons = [
                FortifyTransformer::widgetItemLinkDto(
                    'wobqqq.fortify::lang.buttons.install_plugin',
                    'https://octobercms.com/plugin/wobqqq-fortifycsp',
                    'icon-plus',
                    '_blank',
                ),
            ];
        }
        $widgetGroupItemDto = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.csp',
            $buttons,
            WidgetItemColor::DANGER,
            'icon-lock',
        );
        /** @phpstan-ignore-next-line */
        Event::fire(FortifyEvent::SERVICES_WIDGET_GROUP_ITEM_CSP->value, [&$widgetGroupItemDto]);
        $list[] = $widgetGroupItemDto;

        $buttons = [];
        if (PluginManager::instance()->hasPlugin('wobqqq.fortifyinputsanitizer')) {
            if (PluginManager::instance()->isDisabled('Wobqqq.FortifyInputSanitizer')) {
                $buttons = [
                    FortifyTransformer::widgetItemLinkDto(
                        'wobqqq.fortify::lang.buttons.enable_plugin',
                        Backend::url('system/updates/manage'),
                        'icon-plug',
                    ),
                ];
            }
        } else {
            $buttons = [
                FortifyTransformer::widgetItemLinkDto(
                    'wobqqq.fortify::lang.buttons.install_plugin',
                    'https://octobercms.com/plugin/wobqqq-fortifyinputsanitizer',
                    'icon-plus',
                    '_blank',
                ),
            ];
        }
        $widgetGroupItemDto = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.input_sanitizer',
            $buttons,
            WidgetItemColor::DANGER,
            'icon-crosshairs',
        );
        /** @phpstan-ignore-next-line */
        Event::fire(FortifyEvent::SERVICES_WIDGET_GROUP_ITEM_INPUT_SANITIZER->value, [&$widgetGroupItemDto]);
        $list[] = $widgetGroupItemDto;

        $groups[] = FortifyTransformer::widgetGroupDto(
            'wobqqq.fortify::lang.fields.modules',
            $list,
            'icon-puzzle-piece',
        );
    }

    /**
     * @param array<int, mixed> $groups
     * @return void
     */
    private function initConfigData(array &$groups): void
    {
        $settingsLink = FortifyTransformer::widgetItemLinkDto(
            'wobqqq.fortify::lang.buttons.edit',
            Backend::url('system/settings/update/wobqqq/fortify/fortify#primarytab-config'),
            'icon-wrench',
        );

        $fortifyConfigDto = ConfigDtoInstance::instance()->get();

        $list = [];

        /** @var string $sessionSameSite */
        $sessionSameSite = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->sessionSameSite?->value
            : Config::get('session.same_site');
        $color = $sessionSameSite === SessionSameSite::STRICT->value
            ? WidgetItemColor::SUCCESS
            : WidgetItemColor::DANGER;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.session_same_site',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        /** @var int|string $sessionLifetime */
        $sessionLifetime = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->sessionLifetime
            : Config::get('session.lifetime');
        $color = (int)$sessionLifetime <= 30
            ? WidgetItemColor::SUCCESS
            : WidgetItemColor::WARNING;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.session_lifetime',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        /** @var bool|int|null $sessionSecure */
        $sessionSecure = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->sessionSecure
            : Config::get('session.secure');
        $color = (bool)$sessionSecure ? WidgetItemColor::SUCCESS : WidgetItemColor::DANGER;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.session_secure',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        /** @var bool|int|null $sessionHttpOnly */
        $sessionHttpOnly = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->sessionHttpOnly
            : Config::get('session.http_only');
        $color = (bool)$sessionHttpOnly ? WidgetItemColor::SUCCESS : WidgetItemColor::DANGER;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.session_http_only',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        /** @var bool|int|null $sessionEncrypt */
        $sessionEncrypt = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->sessionEncrypt
            : Config::get('session.encrypt');
        $color = (bool)$sessionEncrypt ? WidgetItemColor::SUCCESS : WidgetItemColor::WARNING;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.session_encrypt',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        /** @var bool|int|null $passwordPolicyAllowReset */
        $passwordPolicyAllowReset = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->passwordPolicyAllowReset
            : Config::get('backend.password_policy.allow_reset');
        $color = !(bool)$passwordPolicyAllowReset ? WidgetItemColor::SUCCESS : WidgetItemColor::WARNING;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.password_policy_allow_reset',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        /** @var bool|int|null $passwordPolicyRequireUppercase */
        $passwordPolicyRequireUppercase = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->passwordPolicyRequireUppercase
            : Config::get('backend.password_policy.require_uppercase');
        $color = (bool)$passwordPolicyRequireUppercase
            ? WidgetItemColor::SUCCESS
            : WidgetItemColor::DANGER;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.password_policy_require_uppercase',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        /** @var bool|int|null $passwordPolicyRequireLowercase */
        $passwordPolicyRequireLowercase = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->passwordPolicyRequireLowercase
            : Config::get('backend.password_policy.require_lowercase');
        $color = (bool)$passwordPolicyRequireLowercase
            ? WidgetItemColor::SUCCESS
            : WidgetItemColor::DANGER;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.password_policy_require_lowercase',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        /** @var bool|int|null $passwordPolicyRequireNumber */
        $passwordPolicyRequireNumber = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->passwordPolicyRequireNumber
            : Config::get('backend.password_policy.require_number');
        $color = (bool)$passwordPolicyRequireNumber ? WidgetItemColor::SUCCESS : WidgetItemColor::DANGER;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.password_policy_require_number',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        /** @var bool|int|null $passwordPolicyRequireNonAlpha */
        $passwordPolicyRequireNonAlpha = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->passwordPolicyRequireNonAlpha
            : Config::get('backend.password_policy.require_nonalpha');
        $color = (bool)$passwordPolicyRequireNonAlpha
            ? WidgetItemColor::SUCCESS
            : WidgetItemColor::DANGER;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.password_policy_require_nonalpha',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        /** @var bool|int|null $passwordPolicyExpireDays */
        $passwordPolicyExpireDays = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->passwordPolicyExpireDays
            : Config::get('backend.password_policy.expire_days');
        $color = (bool)$passwordPolicyExpireDays
            ? WidgetItemColor::SUCCESS
            : WidgetItemColor::WARNING;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.password_policy_expire_days',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        /** @var string|int $passwordPolicyMinLength */
        $passwordPolicyMinLength = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->passwordPolicyMinLength
            : Config::get('backend.password_policy.min_length');
        $color = (int)$passwordPolicyMinLength >= 12
            ? WidgetItemColor::SUCCESS
            : WidgetItemColor::DANGER;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.password_policy_min_length',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        /** @var bool|int|null $backendForceSecure */
        $backendForceSecure = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->backendForceSecure
            : Config::get('backend.force_secure');
        $color = (bool)$backendForceSecure ? WidgetItemColor::SUCCESS : WidgetItemColor::DANGER;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.backend_force_secure',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        /** @var bool|int|null $backendForceSingleSession */
        $backendForceSingleSession = $fortifyConfigDto->enabled
            ? $fortifyConfigDto->backendForceSingleSession
            : Config::get('backend.force_single_session');
        $color = (bool)$backendForceSingleSession ? WidgetItemColor::SUCCESS : WidgetItemColor::DANGER;
        $list[] = FortifyTransformer::widgetGroupItemDto(
            'wobqqq.fortify::lang.fields.backend_force_single_session',
            [$settingsLink],
            $color,
            'icon-cog',
        );

        $groups[] = FortifyTransformer::widgetGroupDto(
            'wobqqq.fortify::lang.fields.config',
            $list,
            'icon-cog',
        );
    }
}
