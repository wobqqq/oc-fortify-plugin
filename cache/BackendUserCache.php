<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Cache;

use Illuminate\Support\Facades\Cache;
use Wobqqq\Fortify\Queries\BackendUserQuery;

final class BackendUserCache extends BasicCache
{
    public function __construct(private readonly BackendUserQuery $backendUserQuery)
    {
    }

    public function countSuperusers(): int
    {
        $cacheKey = $this->cacheKey(['countSuperusers']);

        /** @var int $numberOfSuperusers */
        $numberOfSuperusers = Cache::remember($cacheKey, self::TTL, function () {
            return $this->backendUserQuery->countSuperusers();
        });

        return $numberOfSuperusers;
    }

    public function clearCountSuperusers(): void
    {
        Cache::forget($this->cacheKey(['countSuperusers']));
    }

    public function countOutdatedAdmins(): int
    {
        $cacheKey = $this->cacheKey(['countOutdatedAdmins']);

        /** @var int $numberOfOutdatedAdmins */
        $numberOfOutdatedAdmins = Cache::remember($cacheKey, self::TTL, function () {
            return $this->backendUserQuery->countOutdatedAdmins();
        });

        return $numberOfOutdatedAdmins;
    }

    public function clearCountOutdatedAdmins(): void
    {
        Cache::forget($this->cacheKey(['countOutdatedAdmins']));
    }

    /**
     * @param array<int, string> $logins
     * @return array<int, string>
     */
    public function getLoginsByLogins(array $logins): array
    {
        $cacheKey = $this->cacheKey(['getLoginsByLogins']);

        /** @var array<int, string> $logins */
        $logins = Cache::remember($cacheKey, self::TTL, function () use ($logins) {
            return $this->backendUserQuery->getLoginsByLogins($logins);
        });

        return $logins;
    }

    public function clearGetLoginsByLogins(): void
    {
        Cache::forget($this->cacheKey(['getLoginsByLogins']));
    }
}
