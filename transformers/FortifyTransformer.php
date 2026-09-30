<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Transformers;

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

        /** @var bool|int|string|null $passwordPolicyExpireDays */
        $passwordPolicyExpireDays = Fortify::get('config.password_policy_expire_days');
        $passwordPolicyExpireDays = is_numeric($passwordPolicyExpireDays) ? max(0, (int)$passwordPolicyExpireDays) : 0;

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
     * @param array<int, WidgetGroupItemDto> $list
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
     * @param array<int, WidgetItemButtonDto|WidgetItemLinkDto> $buttons
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
        /** @var array<int, array<string, mixed>>|null $urls */
        $urls = Fortify::get('tests.sensitive_files_checker_urls');
        $urls = self::column(is_array($urls) ? $urls : [], 'url', static fn (string $url): string => rtrim($url, '/'));

        /** @var array<int, array<string, mixed>>|null $paths */
        $paths = Fortify::get('tests.sensitive_files_checker_paths');
        $paths = self::column(is_array($paths) ? $paths : [], 'path', static fn (string $path): string => trim($path, '/'));

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

        foreach (self::portRows('tests.sensitive_tcp_ports_checker_ips', 'ip') as [$ip, $ports]) {
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

        foreach (self::portRows('tests.ssl_certificate_checker_hosts', 'host') as [$host, $ports]) {
            $dtoList[] = new SllCertificateCheckerDto($host, $ports);
        }

        return $dtoList;
    }

    /**
     * @param array<string, mixed> $response
     */
    public static function sslCertificateCheckerTestResultDto(
        string $host,
        int $port,
        array $response,
    ): SllCertificateCheckerTestResultDto {
        $isSuccess = ($response['success'] ?? false) === true;
        $issuedOn = $response['issued_on'] ?? null;
        $expiresOn = $response['expires_on'] ?? null;

        if ($isSuccess && is_int($issuedOn) && $issuedOn > 0 && is_int($expiresOn) && $expiresOn > 0) {
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

    /**
     * @return array<int, array{0: string, 1: array<int, int>}> the target and its ports, for the rows naming both
     */
    private static function portRows(string $setting, string $targetKey): array
    {
        $rows = Fortify::get($setting);
        $result = [];

        foreach (is_array($rows) ? $rows : [] as $row) {
            if (!is_array($row)) {
                continue;
            }

            $target = is_scalar($row[$targetKey] ?? null) ? trim((string)$row[$targetKey]) : '';
            $ports = is_scalar($row['ports'] ?? null) ? explode(',', (string)$row['ports']) : [];
            $ports = array_values(array_unique(array_filter(
                array_map(static fn (string $port): int => (int)trim($port), $ports),
                static fn (int $port): bool => $port > 0 && $port <= 65535,
            )));

            if ($target !== '' && $ports !== []) {
                $result[] = [$target, $ports];
            }
        }

        return $result;
    }

    /**
     * @param array<int|string, mixed> $rows
     * @param callable(string): string $normalize
     *
     * @return array<int, string>
     */
    private static function column(array $rows, string $key, callable $normalize): array
    {
        $values = [];

        foreach ($rows as $row) {
            $value = is_array($row) ? ($row[$key] ?? null) : null;
            $value = is_scalar($value) ? $normalize(trim((string)$value)) : '';

            if ($value !== '') {
                $values[] = $value;
            }
        }

        return array_values(array_unique($values));
    }
}
