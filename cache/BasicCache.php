<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Cache;

use Closure;
use Illuminate\Support\Facades\Cache;
use Throwable;

class BasicCache
{
    protected const TTL = 3600;

    /**
     * Part of every key: a new plugin version whose cached objects change shape bumps it,
     * so an update never reads what the previous version wrote.
     */
    protected const VERSION = 2;

    /**
     * @param array<mixed, mixed> ...$args
     */
    protected function cacheKey(...$args): string
    {
        $args[] = static::class;
        $args[] = static::VERSION;
        $args = array_map(
            static fn (mixed $value): string => is_scalar($value) ? (string)$value : serialize($value),
            $args,
        );

        return md5(implode('-', $args));
    }

    /**
     * @template TValue
     *
     * @param Closure(): TValue $callback
     *
     * @return TValue
     */
    protected function remember(string $key, Closure $callback): mixed
    {
        try {
            return Cache::remember($key, self::TTL, $callback);
        } catch (Throwable) {
            Cache::forget($key);

            return $callback();
        }
    }
}
