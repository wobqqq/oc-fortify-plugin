<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Instances;

use October\Rain\Support\Traits\Singleton;
use Wobqqq\Fortify\Cache\ConfigDtoCache;
use Wobqqq\Fortify\Dto\ConfigDto;

final class ConfigDtoInstance
{
    use Singleton;

    private ?ConfigDto $configDto = null;

    public function get(): ConfigDto
    {
        if ($this->configDto instanceof ConfigDto) {
            return $this->configDto;
        }

        /** @var ConfigDtoCache $fortifyConfigDtoCache */
        $fortifyConfigDtoCache = app(ConfigDtoCache::class);

        return $this->configDto = $fortifyConfigDtoCache->get();
    }
}
