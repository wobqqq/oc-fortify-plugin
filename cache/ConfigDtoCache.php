<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Cache;

use Illuminate\Support\Facades\Cache;
use Wobqqq\Fortify\Dto\ConfigDto;
use Wobqqq\Fortify\Transformers\FortifyTransformer;

final class ConfigDtoCache extends BasicCache
{
    public function get(): ConfigDto
    {
        $cacheKey = $this->cacheKey();

        /** @var ConfigDto $configDto */
        $configDto = Cache::remember($cacheKey, self::TTL, function () {
            return FortifyTransformer::configDto();
        });

        return $configDto;
    }

    public function clear(): void
    {
        Cache::forget($this->cacheKey());
    }
}
