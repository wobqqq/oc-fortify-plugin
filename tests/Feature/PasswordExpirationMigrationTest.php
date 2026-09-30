<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @param array<string, mixed> $config
 */
function storeLegacySettings(string $item, array $config): int
{
    return DB::table('system_settings')->insertGetId([
        'item' => $item,
        'value' => json_encode(['config' => $config, 'tests' => ['sensitive_files_checker_urls' => []]]),
    ]);
}

/**
 * @return array<string, mixed>
 */
function storedSettings(int $id): array
{
    $value = DB::table('system_settings')->where('id', $id)->value('value');
    $settings = is_string($value) ? json_decode($value, true) : null;

    if (!is_array($settings)) {
        throw new UnexpectedValueException('The settings are not stored as JSON.');
    }

    /** @var array<string, mixed> $settings */
    return $settings;
}

function runPasswordExpirationMigration(string $direction): void
{
    $migration = require dirname(__DIR__, 2) . '/updates/convert_password_expiration_to_days.php';

    if (!is_object($migration) || !method_exists($migration, 'up') || !method_exists($migration, 'down')) {
        throw new UnexpectedValueException('The update script is not a migration.');
    }

    $direction === 'down' ? $migration->down() : $migration->up();
}

beforeEach(function (): void {
    Schema::create('system_settings', static function (Blueprint $table): void {
        $table->increments('id');
        $table->string('item')->nullable();
        $table->mediumText('value')->nullable();
        $table->integer('site_id')->nullable();
    });
});

it('keeps password expiration off for a site that had the switch on', function (): void {
    $switchedOn = storeLegacySettings('wobqqq_fortify_fortify', ['enabled' => 1, 'password_policy_expire_days' => true]);
    $switchedOff = storeLegacySettings('wobqqq_fortify_fortify', ['password_policy_expire_days' => false]);

    runPasswordExpirationMigration('up');

    expect(storedSettings($switchedOn))->toBe([
        'config' => ['enabled' => 1, 'password_policy_expire_days' => 0],
        'tests' => ['sensitive_files_checker_urls' => []],
    ])->and(storedSettings($switchedOff))->toHaveKey('config.password_policy_expire_days', 0);
});

it('leaves the other settings and the rows without the setting alone', function (): void {
    $other = storeLegacySettings('acme_blog_settings', ['password_policy_expire_days' => true]);
    $untouched = storeLegacySettings('wobqqq_fortify_fortify', ['enabled' => 1]);
    DB::table('system_settings')->insert(['item' => 'wobqqq_fortify_fortify', 'value' => 'not json']);

    runPasswordExpirationMigration('up');

    expect(storedSettings($other))->toHaveKey('config.password_policy_expire_days', true)
        ->and(storedSettings($untouched))->toHaveKey('config', ['enabled' => 1]);
});

it('rolls a number of days back to the switch', function (): void {
    $days = storeLegacySettings('wobqqq_fortify_fortify', ['password_policy_expire_days' => 90]);
    $off = storeLegacySettings('wobqqq_fortify_fortify', ['password_policy_expire_days' => 0]);

    runPasswordExpirationMigration('down');

    expect(storedSettings($days))->toHaveKey('config.password_policy_expire_days', true)
        ->and(storedSettings($off))->toHaveKey('config.password_policy_expire_days', false);
});

it('does nothing before October has created its settings table', function (): void {
    Schema::drop('system_settings');

    runPasswordExpirationMigration('up');

    expect(Schema::hasTable('system_settings'))->toBeFalse();
});
