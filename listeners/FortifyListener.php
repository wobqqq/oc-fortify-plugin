<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Listeners;

use Wobqqq\Fortify\Cache\ConfigDtoCache;
use Wobqqq\Fortify\Models\Fortify;

final readonly class FortifyListener
{
    public function __construct(
        private ConfigDtoCache $configDtoCache,
    ) {
    }

    public function subscribe(): void
    {
        Fortify::extend(function (Fortify $fortify): void {
            $fortify->bindEvent('model.afterSave', function (): void {
                $this->configDtoCache->clear();
            });

            $fortify->bindEvent('model.afterDelete', function (): void {
                $this->configDtoCache->clear();
            });
        });
    }
}
