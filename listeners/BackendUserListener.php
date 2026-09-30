<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Listeners;

use Backend\Models\User;
use Wobqqq\Fortify\Cache\BackendUserCache;
use Wobqqq\Fortify\Services\SensitiveAdministratorLoginCheckerService;

final readonly class BackendUserListener
{
    public function __construct(private BackendUserCache $backendUserCache)
    {
    }

    public function subscribe(): void
    {
        User::extend(function (User $user): void {
            $clear = fn () => $this->backendUserCache->clear(SensitiveAdministratorLoginCheckerService::LOGINS);

            $user->bindEvent('model.afterSave', $clear);
            $user->bindEvent('model.afterDelete', $clear);
        });
    }
}
