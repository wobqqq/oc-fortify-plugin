<?php

declare(strict_types=1);

use Backend\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Wobqqq\Fortify\Cache\BackendUserCache;
use Wobqqq\Fortify\Cache\ConfigDtoCache;
use Wobqqq\Fortify\Models\Fortify;

it('caches the config until the settings are saved', function (): void {
    Fortify::set('config', ['enabled' => false]);
    $cache = app(ConfigDtoCache::class);

    expect($cache->get()->enabled)->toBeFalse();

    Fortify::set('config', ['enabled' => true]);

    expect($cache->get()->enabled)->toBeTrue();
});

it('rebuilds a cached config the previous plugin version wrote in another shape', function (): void {
    Fortify::set('config', ['enabled' => true, 'password_policy_expire_days' => 30]);
    $cache = app(ConfigDtoCache::class);

    Cache::shouldReceive('remember')->once()->andThrow(new TypeError('Cannot assign string to property'));
    Cache::shouldReceive('forget')->once();

    expect($cache->get()->passwordPolicyExpireDays)->toBe(30);
});

it('counts the superusers and the administrators who have not signed in for three months', function (): void {
    User::query()->insert([
        ['login' => 'owner', 'is_superuser' => true, 'last_login' => Carbon::now()],
        ['login' => 'admin', 'is_superuser' => true, 'last_login' => Carbon::now()->subMonths(4)],
        ['login' => 'editor', 'is_superuser' => false, 'last_login' => Carbon::now()->subYear()],
    ]);

    $cache = app(BackendUserCache::class);

    expect($cache->countSuperusers())->toBe(2)
        ->and($cache->countOutdatedAdmins())->toBe(2)
        ->and($cache->getLoginsByLogins(['admin', 'root']))->toBe(['admin']);
});

it('forgets the administrator counts when an administrator changes', function (): void {
    $cache = app(BackendUserCache::class);

    expect($cache->countSuperusers())->toBe(0);

    $user = new User(['login' => 'root', 'is_superuser' => true]);
    $user->save();

    expect($cache->countSuperusers())->toBe(1)
        ->and($cache->getLoginsByLogins(['root']))->toBe(['root']);

    $user->delete();

    expect($cache->countSuperusers())->toBe(0);
});

it('keys the login lookup by the logins it was asked for', function (): void {
    User::query()->insert([['login' => 'admin', 'is_superuser' => false, 'last_login' => null]]);
    $cache = app(BackendUserCache::class);

    expect($cache->getLoginsByLogins(['root']))->toBe([])
        ->and($cache->getLoginsByLogins(['admin']))->toBe(['admin']);
});
