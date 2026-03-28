<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Dto;

final readonly class WidgetGroupDto
{
    public function __construct(
        public string $name,
        /** @var array<int, mixed> $list */
        public array $list = [],
        public ?string $icon = null,
    ) {
    }
}
