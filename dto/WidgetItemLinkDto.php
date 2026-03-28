<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Dto;

final readonly class WidgetItemLinkDto
{
    public function __construct(
        public string $name,
        public string $link,
        public ?string $icon = null,
        public ?string $target = null,
    ) {
    }
}
