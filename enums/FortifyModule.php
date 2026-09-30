<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Enums;

enum FortifyModule: string
{
    case ADMIN_IP_ACCESS = 'Wobqqq.FortifyAdminIpAccess';
    case IP_BLOCKER = 'Wobqqq.FortifyIpBlocker';
    case SMART_IP_BLOCKER = 'Wobqqq.FortifySmartIpBlocker';
    case CSP = 'Wobqqq.FortifyCsp';
    case INPUT_SANITIZER = 'Wobqqq.FortifyInputSanitizer';

    public function pluginCode(): string
    {
        return $this->value;
    }

    public function marketplaceUrl(): string
    {
        return sprintf('https://octobercms.com/plugin/%s', str_replace('.', '-', strtolower($this->value)));
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMIN_IP_ACCESS => 'wobqqq.fortify::lang.fields.admin_ip_access',
            self::IP_BLOCKER => 'wobqqq.fortify::lang.fields.ip_blocker',
            self::SMART_IP_BLOCKER => 'wobqqq.fortify::lang.fields.smart_ip_blocker',
            self::CSP => 'wobqqq.fortify::lang.fields.csp',
            self::INPUT_SANITIZER => 'wobqqq.fortify::lang.fields.input_sanitizer',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ADMIN_IP_ACCESS, self::IP_BLOCKER, self::SMART_IP_BLOCKER => 'icon-ban',
            self::CSP => 'icon-lock',
            self::INPUT_SANITIZER => 'icon-crosshairs',
        };
    }

    public function widgetEvent(): FortifyEvent
    {
        return match ($this) {
            self::ADMIN_IP_ACCESS => FortifyEvent::SERVICES_WIDGET_GROUP_ITEM_ADMIN_IP_ACCESS,
            self::IP_BLOCKER => FortifyEvent::SERVICES_WIDGET_GROUP_ITEM_IP_BLOCKER,
            self::SMART_IP_BLOCKER => FortifyEvent::SERVICES_WIDGET_GROUP_ITEM_SMART_IP_BLOCKER,
            self::CSP => FortifyEvent::SERVICES_WIDGET_GROUP_ITEM_CSP,
            self::INPUT_SANITIZER => FortifyEvent::SERVICES_WIDGET_GROUP_ITEM_INPUT_SANITIZER,
        };
    }
}
