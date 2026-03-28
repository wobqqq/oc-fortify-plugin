<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Dto;

final readonly class WidgetItemButtonDto
{
    public function __construct(
        public string $name,
        public string $action,
        public ?string $icon = null,
    ) {
    }
}
