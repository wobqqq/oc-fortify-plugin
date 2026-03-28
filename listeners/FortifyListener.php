<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Listeners;

use October\Rain\Events\Dispatcher;
use Wobqqq\Fortify\Cache\ConfigDtoCache;
use Wobqqq\Fortify\Models\Fortify;

final readonly class FortifyListener
{
    public function __construct(
        private ConfigDtoCache $configDtoCache,
    ) {
    }

    public function subscribe(Dispatcher $event): void
    {
        Fortify::extend(function (Fortify $fortify) {
            $fortify->bindEvent('model.afterSave', function () {
                $this->configDtoCache->clear();
            });

            $fortify->bindEvent('model.afterDelete', function () {
                $this->configDtoCache->clear();
            });
        });
    }
}
