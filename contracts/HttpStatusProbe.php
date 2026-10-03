<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Contracts;

interface HttpStatusProbe
{
    /**
     * The status code of each URL, or "error" when it did not answer. Redirects are not followed.
     *
     * @param array<int, string> $urls
     *
     * @return array<string, int|string>
     */
    public function request(array $urls): array;
}
