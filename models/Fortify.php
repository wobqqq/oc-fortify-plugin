<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Models;

use Config;
use Event;
use October\Rain\Database\Traits\Validation;
use System\Models\SettingModel;
use Wobqqq\Fortify\Enums\FortifyEvent;
use Wobqqq\Fortify\Enums\SessionSameSite;
use Wobqqq\Fortify\Enums\View;
use Wobqqq\Fortify\Services\SensitiveFileCheckerService;
use Wobqqq\Fortify\Services\SensitiveSslCertificateCheckerService;
use Wobqqq\Fortify\Services\SensitiveTcpPortCheckerService;

/**
 * Wobqqq\Fortify\Models\Fortify
 *
 * @property int $id
 * @property string|null $item
 * @property array|null $value
 * @property int|null $site_id
 * @property int|null $site_root_id
 * @method static \October\Rain\Database\Builder|Fortify addWhereExistsQuery($query, $boolean = 'and', $not = false)
 * @method static \October\Rain\Database\Collection<int, static> all($columns = ['*'])
 * @method static \October\Rain\Database\Collection<int, static> get($columns = ['*'])
 * @method static \October\Rain\Database\Builder|Fortify lists($column, $key = null)
 * @method static \October\Rain\Database\Builder|Fortify newModelQuery()
 * @method static \October\Rain\Database\Builder|Fortify newQuery()
 * @method static \October\Rain\Database\Builder|Fortify orSearchWhere($term, $columns = [], $mode = 'all')
 * @method static \October\Rain\Database\Builder|Fortify orSearchWhereRelation($term, $relation, $columns = [], $mode = 'all')
 * @method static \October\Rain\Database\Builder|Fortify paginateAtPage($perPage, $currentPage)
 * @method static \October\Rain\Database\Builder|Fortify paginateCustom($perPage, $pageName)
 * @method static \October\Rain\Database\Builder|Fortify query()
 * @method static \October\Rain\Database\Builder|Fortify searchWhere($term, $columns = [], $mode = 'all')
 * @method static \October\Rain\Database\Builder|Fortify searchWhereRelation($term, $relation, $columns = [], $mode = 'all')
 * @method static \October\Rain\Database\Builder|Fortify simplePaginateAtPage($perPage, $currentPage)
 * @method static \October\Rain\Database\Builder|Fortify simplePaginateCustom($perPage, $pageName)
 * @method static \October\Rain\Database\Builder|Fortify whereId($value)
 * @method static \October\Rain\Database\Builder|Fortify whereItem($value)
 * @method static \October\Rain\Database\Builder|Fortify whereSiteId($value)
 * @method static \October\Rain\Database\Builder|Fortify whereSiteRootId($value)
 * @method static \October\Rain\Database\Builder|Fortify whereValue($value)
 * @mixin \Eloquent
 */
class Fortify extends SettingModel
{
    use Validation;

    public string $settingsCode = 'wobqqq_fortify_fortify';

    public string $settingsFields = 'fields.yaml';

    /** @var array<string, string> */
    public array $attributeNames = [
        'tests.sensitive_files_checker_urls.*.url' => 'wobqqq.fortify::lang.fields.url',
        'tests.sensitive_tcp_ports_checker_ips.*.ip' => 'wobqqq.fortify::lang.fields.ip',
        'tests.sensitive_tcp_ports_checker_ips.*.ports' => 'wobqqq.fortify::lang.fields.ports',
        'tests.ssl_certificate_checker_hosts.*.host' => 'wobqqq.fortify::lang.fields.host',
        'tests.ssl_certificate_checker_hosts.*.ports' => 'wobqqq.fortify::lang.fields.ports',
    ];

    /** @var array<string, mixed> */
    public array $rules = [
        'config.password_policy_min_length' => 'required|integer|min:4|max:128',
        'config.session_lifetime' => 'required|integer|min:1|max:1000',
        'tests.sensitive_files_checker_urls.*.url' => 'nullable|max:50|url',
        'tests.sensitive_files_checker_urls' => 'nullable|array|max:100',
        'tests.sensitive_files_checker_paths.*.path' => 'nullable|max:150|string',
        'tests.sensitive_files_checker_paths' => 'nullable|array|max:100',
        'tests.sensitive_tcp_ports_checker_ips.*.ip' => 'nullable|regex:/^[0-9a-fA-F\.:]+(\/\d{1,3})?$/|max:50',
        'tests.sensitive_tcp_ports_checker_ips.*.ports' => 'nullable|string|max:100|regex:/^\d+(,\d+)*$/',
        'tests.sensitive_tcp_ports_checker_ips' => 'nullable|array|max:5',
        'tests.ssl_certificate_checker_hosts.*.host' => [
            'nullable',
            'max:100',
            'regex:/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}(:\d{1,5})?$/i',
        ],
        'tests.ssl_certificate_checker_hosts.*.ports' => 'nullable|string|max:100|regex:/^\d+(,\d+)*$/',
        'tests.ssl_certificate_checker_hosts' => 'nullable|array|max:5',
    ];

    /**
     * @return array<string, string>
     */
    public function getSessionSameSiteOptions(): array
    {
        return [
            SessionSameSite::LAX->value => SessionSameSite::LAX->value,
            SessionSameSite::STRICT->value => SessionSameSite::STRICT->value,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getViewOptions(): array
    {
        $views = [
            View::DENIED->value => View::DENIED->value,
            View::BAD_REQUEST->value => View::BAD_REQUEST->value,
        ];

        /** @phpstan-ignore-next-line */
        Event::fire(FortifyEvent::VIEW_DENIED->value, [&$views]);

        return $views;
    }

    public function initSettingsData(): void
    {
        $config = [
            'enabled' => false,
            'session_same_site' => Config::get('session.same_site'),
            'session_lifetime' => Config::get('session.lifetime'),
            'session_secure' => Config::get('session.secure'),
            'session_http_only' => Config::get('session.http_only'),
            'session_encrypt' => Config::get('session.encrypt'),
            'password_policy_allow_reset' => Config::get('backend.password_policy.allow_reset'),
            'password_policy_min_length' => Config::get('backend.password_policy.min_length'),
            'password_policy_require_lowercase' => Config::get('backend.password_policy.require_lowercase'),
            'password_policy_require_number' => Config::get('backend.password_policy.require_number'),
            'password_policy_require_nonalpha' => Config::get('backend.password_policy.require_nonalpha'),
            'password_policy_expire_days' => Config::get('backend.password_policy.expire_days'),
            'password_policy_require_uppercase' => Config::get('backend.password_policy.require_uppercase'),
            'backend_force_secure' => Config::get('backend.force_secure'),
            'backend_force_single_session' => Config::get('backend.force_single_session'),
        ];
        /** @noinspection PhpUndefinedFieldInspection */
        /** @phpstan-ignore-next-line */
        $this->config = $config;

        /** @var SensitiveFileCheckerService $sensitiveFileCheckerService */
        $sensitiveFileCheckerService = app(SensitiveFileCheckerService::class);
        /** @var SensitiveTcpPortCheckerService $sensitiveTcpPortCheckerService */
        $sensitiveTcpPortCheckerService = app(SensitiveTcpPortCheckerService::class);
        /** @var SensitiveSslCertificateCheckerService $sensitiveSslCertificateCheckerService */
        $sensitiveSslCertificateCheckerService = app(SensitiveSslCertificateCheckerService::class);
        $tests = [
            'sensitive_files_checker_urls' => $sensitiveFileCheckerService->generateDefaultUrlsSettingsData(),
            'sensitive_files_checker_paths' => $sensitiveFileCheckerService->generateDefaultPathsSettingsData(),
            'sensitive_tcp_ports_checker_ips' => $sensitiveTcpPortCheckerService->generateDefaultSettingsData(),
            'ssl_certificate_checker_hosts' => $sensitiveSslCertificateCheckerService->generateDefaultSettingsData(),
        ];
        /** @noinspection PhpUndefinedFieldInspection */
        /** @phpstan-ignore-next-line */
        $this->tests = $tests;

        /** @phpstan-ignore-next-line */
        Event::fire(FortifyEvent::MODEL_FORTIFY_INIT_SETTINGS_DATA->value, [&$this]);
    }
}
