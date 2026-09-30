<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Queries;

use Backend\Models\User;
use Illuminate\Support\Carbon;

final class BackendUserQuery
{
    public function countSuperusers(): int
    {
        $query = User::query();
        $query->where('is_superuser', true);

        return $query->count();
    }

    public function countOutdatedAdmins(): int
    {
        $query = User::query();
        $query->whereDate('last_login', '<=', (Carbon::now()->subMonths(3)));

        return $query->count();
    }

    /**
     * @param array<int, string> $logins
     *
     * @return array<int, string>
     */
    public function getLoginsByLogins(array $logins): array
    {
        $query = User::query();
        $query->select(['login']);
        $query->whereIn('login', $logins);
        /** @var array<int, string> $logins */
        $logins = $query->pluck('login')->all();

        return $logins;
    }
}
