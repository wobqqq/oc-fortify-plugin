<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Dto;

final readonly class SensitiveFileCheckerDto
{
    public function __construct(
        /** @var array<int, string> */
        public array $urls,
        /** @var array<int, string> */
        public array $paths,
    ) {
    }
}
