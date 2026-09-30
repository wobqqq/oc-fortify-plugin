<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Dto;

use Wobqqq\Fortify\Enums\WidgetItemColor;

final readonly class WidgetGroupItemDto
{
    public function __construct(
        public string          $name,
        /** @var array<int, WidgetItemButtonDto|WidgetItemLinkDto> $buttons */
        public array           $buttons = [],
        public WidgetItemColor $color = WidgetItemColor::DEFAULT,
        public ?string         $icon = null,
    ) {
    }
}
