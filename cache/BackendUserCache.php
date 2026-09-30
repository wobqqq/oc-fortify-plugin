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
        return $this->remember(
            $this->cacheKey(['countSuperusers']),
            fn (): int => $this->backendUserQuery->countSuperusers(),
        );
    }

    public function countOutdatedAdmins(): int
    {
        return $this->remember(
            $this->cacheKey(['countOutdatedAdmins']),
            fn (): int => $this->backendUserQuery->countOutdatedAdmins(),
        );
    }

    /**
     * @param array<int, string> $logins
     *
     * @return array<int, string>
     */
    public function getLoginsByLogins(array $logins): array
    {
        return $this->remember(
            $this->cacheKey(['getLoginsByLogins'], $logins),
            fn (): array => $this->backendUserQuery->getLoginsByLogins($logins),
        );
    }

    /**
     * @param array<int, string> $logins
     */
    public function clear(array $logins): void
    {
        Cache::forget($this->cacheKey(['countSuperusers']));
        Cache::forget($this->cacheKey(['countOutdatedAdmins']));
        Cache::forget($this->cacheKey(['getLoginsByLogins'], $logins));
    }
}
