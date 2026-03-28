<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Transformers;

use Arr;
use Illuminate\Support\Carbon;
use Wobqqq\Fortify\Dto\ConfigDto;
use Wobqqq\Fortify\Dto\SensitiveFileCheckerDto;
use Wobqqq\Fortify\Dto\SensitiveFileCheckerTestResultDto;
use Wobqqq\Fortify\Dto\SensitiveTcpPortCheckerDto;
use Wobqqq\Fortify\Dto\SensitiveTcpPortCheckerTestResultDto;
use Wobqqq\Fortify\Dto\SllCertificateCheckerDto;
use Wobqqq\Fortify\Dto\SllCertificateCheckerTestResultDto;
use Wobqqq\Fortify\Dto\WidgetGroupDto;
use Wobqqq\Fortify\Dto\WidgetGroupItemDto;
use Wobqqq\Fortify\Dto\WidgetItemButtonDto;
use Wobqqq\Fortify\Dto\WidgetItemLinkDto;
use Wobqqq\Fortify\Enums\SessionSameSite;
use Wobqqq\Fortify\Enums\WidgetItemColor;
use Wobqqq\Fortify\Models\Fortify;

final readonly class FortifyTransformer
{
    public static function configDto(): ConfigDto
    {
        /** @var bool|int|null $enabled */
        $enabled = Fortify::get('config.enabled');
        $enabled = (bool)$enabled;

        /** @var string|null $sessionSameSite */
        $sessionSameSite = Fortify::get('config.session_same_site');
        $sessionSameSite = SessionSameSite::tryFrom((string)$sessionSameSite);

        /** @var bool|int|null $sessionSecure */
        $sessionSecure = Fortify::get('config.session_secure');
        $sessionSecure = (bool)$sessionSecure;

        /** @var bool|int|null $sessionHttpOnly */
        $sessionHttpOnly = Fortify::get('config.session_http_only');
        $sessionHttpOnly = $sessionHttpOnly === null ? true : (bool)$sessionHttpOnly;

        /** @var bool|int|null $sessionEncrypt */
        $sessionEncrypt = Fortify::get('config.session_encrypt');
        $sessionEncrypt = $sessionEncrypt === null ? false : (bool)$sessionEncrypt;

        /** @var int|string|null $sessionLifetime */
        $sessionLifetime = Fortify::get('config.session_lifetime');
        $sessionLifetime = (int)$sessionLifetime;
        $sessionLifetime = $sessionLifetime === 0 ? 120 : $sessionLifetime;

        /** @var bool|int|null $passwordPolicyAllowReset */
        $passwordPolicyAllowReset = Fortify::get('config.password_policy_allow_reset');
        $passwordPolicyAllowReset = $passwordPolicyAllowReset === null ? true : (bool)$passwordPolicyAllowReset;

        /** @var bool|int|null $passwordPolicyRequireUppercase */
        $passwordPolicyRequireUppercase = Fortify::get('config.password_policy_require_uppercase');
        $passwordPolicyRequireUppercase = (bool)$passwordPolicyRequireUppercase;

        /** @var bool|int|null $passwordPolicyRequireLowercase */
        $passwordPolicyRequireLowercase = Fortify::get('config.password_policy_require_lowercase');
        $passwordPolicyRequireLowercase = (bool)$passwordPolicyRequireLowercase;

        /** @var bool|int|null $passwordPolicyRequireNumber */
        $passwordPolicyRequireNumber = Fortify::get('config.password_policy_require_number');
        $passwordPolicyRequireNumber = (bool)$passwordPolicyRequireNumber;

        /** @var bool|int|null $passwordPolicyRequireNonAlpha */
        $passwordPolicyRequireNonAlpha = Fortify::get('config.password_policy_require_nonalpha');
        $passwordPolicyRequireNonAlpha = (bool)$passwordPolicyRequireNonAlpha;

        /** @var bool|int|null $passwordPolicyExpireDays */
        $passwordPolicyExpireDays = Fortify::get('config.password_policy_expire_days');
        $passwordPolicyExpireDays = (bool)$passwordPolicyExpireDays;

        /** @var int|string|null $passwordPolicyMinLength */
        $passwordPolicyMinLength = Fortify::get('config.password_policy_min_length');
        $passwordPolicyMinLength = (int)$passwordPolicyMinLength;
        $passwordPolicyMinLength = $passwordPolicyMinLength === 0 ? 4 : $passwordPolicyMinLength;

        /** @var bool|int|null $backendForceSecure */
        $backendForceSecure = Fortify::get('config.backend_force_secure');
        $backendForceSecure = (bool)$backendForceSecure;

        /** @var bool|int|null $backendForceSingleSession */
        $backendForceSingleSession = Fortify::get('config.backend_force_single_session');
        $backendForceSingleSession = (bool)$backendForceSingleSession;

        return new ConfigDto(
            $enabled,
            $sessionSameSite,
            $sessionSecure,
            $sessionHttpOnly,
            $sessionEncrypt,
            $sessionLifetime,
            $passwordPolicyAllowReset,
            $passwordPolicyRequireUppercase,
            $passwordPolicyRequireLowercase,
            $passwordPolicyRequireNumber,
            $passwordPolicyRequireNonAlpha,
            $passwordPolicyExpireDays,
            $passwordPolicyMinLength,
            $backendForceSecure,
            $backendForceSingleSession,
        );
    }

    /**
     * @param string $name
     * @param array<int, mixed> $list
     * @param string|null $icon
     * @return WidgetGroupDto
     */
    public static function widgetGroupDto(
        string $name,
        array $list = [],
        ?string $icon = null,
    ): WidgetGroupDto {
        return new WidgetGroupDto(
            $name,
            $list,
            $icon,
        );
    }

    /**
     * @param string $name
     * @param array<int, WidgetItemLinkDto|WidgetItemButtonDto> $buttons
     * @param WidgetItemColor $color
     * @param string|null $icon
     * @return WidgetGroupItemDto
     */
    public static function widgetGroupItemDto(
        string          $name,
        array           $buttons = [],
        WidgetItemColor $color = WidgetItemColor::DEFAULT,
        ?string         $icon = null,
    ): WidgetGroupItemDto {
        return new WidgetGroupItemDto(
            $name,
            $buttons,
            $color,
            $icon,
        );
    }

    public static function widgetItemLinkDto(
        string $name,
        string $link,
        ?string $icon = null,
        ?string $target = null,
    ): WidgetItemLinkDto {
        return new WidgetItemLinkDto(
            $name,
            $link,
            $icon,
            $target,
        );
    }

    public static function widgetItemButtonDto(
        string $name,
        string $action,
        ?string $icon = null,
    ): WidgetItemButtonDto {
        return new WidgetItemButtonDto(
            $name,
            $action,
            $icon,
        );
    }

    public static function sensitiveFileCheckerDto(): SensitiveFileCheckerDto
    {
        /** @var array<int, string>|null $urls */
        $urls = Fortify::get('tests.sensitive_files_checker_urls');
        $urls = empty($urls) ? [] : $urls;
        /** @var array<int, string> $urls */
        $urls = array_column($urls, 'url');
        $urls = array_unique($urls);
        $urls = array_filter($urls);
        /** @var array<int, string> $urls */
        $urls = array_map(function (string|null|int $url) {
            return rtrim((string)$url, '/');
        }, $urls);

        /** @var array<int, string>|null $paths */
        $paths = Fortify::get('tests.sensitive_files_checker_paths');
        $paths = empty($paths) ? [] : $paths;
        /** @var array<int, string> $paths */
        $paths = array_column($paths, 'path');
        $paths = array_unique($paths);
        $paths = array_filter($paths);
        /** @var array<int, string> $paths */
        $paths = array_map(function (string|null|int $path) {
            return trim((string)$path, '/');
        }, $paths);

        return new SensitiveFileCheckerDto($urls, $paths);
    }

    public static function sensitiveFileCheckerTestResultDto(
        string $url,
        string $status,
        bool $isPositive,
    ): SensitiveFileCheckerTestResultDto {
        return new SensitiveFileCheckerTestResultDto(
            $url,
            $status,
            $isPositive,
        );
    }

    /**
     * @return array<int, SensitiveTcpPortCheckerDto>
     */
    public static function sensitiveTcpPortCheckerDtoList(): array
    {
        $dtoList = [];

        /** @var array<int, array<string, mixed>>|null $ips */
        $ips = Fortify::get('tests.sensitive_tcp_ports_checker_ips');
        $ips = empty($ips) ? [] : $ips;

        foreach ($ips as $ipData) {
            /** @var string|null $ip */
            $ip = Arr::get($ipData, 'ip');
            /** @var string|null $ports */
            $ports = Arr::get($ipData, 'ports');
            $ports = explode(',', (string)$ports);
            /** @var array<int, string|int|null> $ports */
            $ports = array_unique($ports);
            $ports = array_filter($ports);
            /** @var array<int, int> $ports */
            $ports = array_map(function (string|null|int $port) {
                return (int)$port;
            }, $ports);

            if (empty($ip) || empty($ports)) {
                continue;
            }

            $dtoList[] = new SensitiveTcpPortCheckerDto($ip, $ports);
        }

        return $dtoList;
    }

    public static function sensitiveTcpPortCheckerTestResultDto(
        string $ip,
        int    $port,
        string $status,
        bool   $isPositive,
    ): SensitiveTcpPortCheckerTestResultDto {
        return new SensitiveTcpPortCheckerTestResultDto(
            $ip,
            $port,
            $status,
            $isPositive,
        );
    }

    /**
     * @return array<int, SllCertificateCheckerDto>
     */
    public static function sslCertificateCheckerDtoList(): array
    {
        $dtoList = [];

        /** @var array<int, array<string, mixed>>|null $hosts */
        $hosts = Fortify::get('tests.ssl_certificate_checker_hosts');
        $hosts = empty($hosts) ? [] : $hosts;

        foreach ($hosts as $hostData) {
            /** @var string|null $host */
            $host = Arr::get($hostData, 'host');
            /** @var string|null $ports */
            $ports = Arr::get($hostData, 'ports');
            $ports = explode(',', (string)$ports);
            /** @var array<int, string|int|null> $ports */
            $ports = array_unique($ports);
            $ports = array_filter($ports);
            /** @var array<int, int> $ports */
            $ports = array_map(function (string|null|int $port) {
                return (int)$port;
            }, $ports);

            if (empty($host) || empty($ports)) {
                continue;
            }

            $dtoList[] = new SllCertificateCheckerDto($host, $ports);
        }

        return $dtoList;
    }

    /**
     * @param string $host
     * @param int $port
     * @param array<string, mixed> $response
     * @return SllCertificateCheckerTestResultDto
     */
    public static function sslCertificateCheckerTestResultDto(
        string $host,
        int $port,
        array $response,
    ): SllCertificateCheckerTestResultDto {
        /** @var bool|null $isSuccess */
        $isSuccess = Arr::get($response, 'success', false);
        $isSuccess = (bool)$isSuccess;
        /** @var string|int|null $issuedOn */
        $issuedOn = Arr::get($response, 'issued_on');
        /** @var string|int|null $expiresOn */
        $expiresOn = Arr::get($response, 'expires_on');

        if ($isSuccess && !empty($issuedOn) && !empty($expiresOn)) {
            $isPositive = true;
            $issuedOn = Carbon::createFromTimestamp($issuedOn);
            $expiresOn = Carbon::createFromTimestamp($expiresOn);
        } else {
            $isPositive = false;
            $issuedOn = null;
            $expiresOn = null;
        }

        return new SllCertificateCheckerTestResultDto(
            $host,
            $port,
            $issuedOn,
            $expiresOn,
            $isPositive,
        );
    }
}
