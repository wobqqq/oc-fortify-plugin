<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Listeners;

use October\Rain\Events\PriorityDispatcher;
use Wobqqq\Fortify\Cache\ConfigDtoCache;
use Wobqqq\Fortify\Models\Fortify;

final readonly class FortifyListener
{
    public function __construct(
        private ConfigDtoCache $configDtoCache,
    ) {
    }

    /**
     * Model events, not bindEvent(): the settings instance may be created before a listener
     * extends the model, and it must still clear the cache when it is saved.
     */
    public function subscribe(PriorityDispatcher $event): void
    {
        $event->listen(
            ['eloquent.saved: ' . Fortify::class, 'eloquent.deleted: ' . Fortify::class],
            function (): void {
                $this->configDtoCache->clear();
            },
        );
    }
}
