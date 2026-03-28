<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Enums;

enum FortifyEvent: string
{
    case VIEW_DENIED = 'wobqqq.fortify::view.denied';
    case SERVICES_WIDGET_GROUP_ITEM_ADMIN_IP_ACCESS = 'wobqqq.fortify::services.widget.group.item.admin.ip.access';
    case SERVICES_WIDGET_GROUP_ITEM_IP_BLOCKER = 'wobqqq.fortify::services.widget.group.item.ip.blocker';
    case SERVICES_WIDGET_GROUP_ITEM_SMART_IP_BLOCKER = 'wobqqq.fortify::services.widget.group.item.smart.ip.blocker';
    case SERVICES_WIDGET_GROUP_ITEM_CSP = 'wobqqq.fortify::services.widget.group.item.csp';
    case SERVICES_WIDGET_GROUP_ITEM_INPUT_SANITIZER = 'wobqqq.fortify::services.widget.group.item.input.sanitizer';
    case MODEL_FORTIFY_INIT_SETTINGS_DATA = 'wobqqq.fortify::model.fortify.init.settings.data';
}
