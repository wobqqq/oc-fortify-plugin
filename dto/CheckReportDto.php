<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Dto;

/**
 * @template TResult of object
 */
final readonly class CheckReportDto
{
    /**
     * @param list<TResult> $results
     */
    public function __construct(
        public array $results,
        public int $failures,
    ) {
    }
}
