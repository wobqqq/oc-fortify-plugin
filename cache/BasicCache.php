<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Cache;

class BasicCache
{
    protected const TTL = 3600;

    /**
     * @param array<mixed, mixed> ...$args
     * @return string
     */
    protected function cacheKey(...$args): string
    {
        $args[] = static::class;
        $args = array_map(function ($value) {
            if (is_array($value)) {
                return serialize($value);
            }

            return (string)$value;
        }, $args);

        $key = implode('-', $args);
        $key = md5($key);

        return $key;
    }
}
