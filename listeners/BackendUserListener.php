<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Listeners;

use Backend\Models\User;
use Wobqqq\Fortify\Cache\BackendUserCache;

final readonly class BackendUserListener
{
    public function __construct(private BackendUserCache $backendUserCache)
    {
    }

    public function subscribe(): void
    {
        User::extend(function (User $user) {
            $user->bindEvent('model.afterSave', function () {
                $this->backendUserCache->clearCountSuperusers();
                $this->backendUserCache->countOutdatedAdmins();
                $this->backendUserCache->clearCountOutdatedAdmins();
                $this->backendUserCache->clearGetLoginsByLogins();
            });

            $user->bindEvent('model.afterDelete', function () {
                $this->backendUserCache->clearCountSuperusers();
                $this->backendUserCache->countOutdatedAdmins();
                $this->backendUserCache->clearCountOutdatedAdmins();
                $this->backendUserCache->clearGetLoginsByLogins();
            });
        });
    }
}
