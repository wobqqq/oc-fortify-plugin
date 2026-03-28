<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Enums;

enum InsecureAdminUri: string
{
    case ADMIN = 'admin';
    case ADMINISTRATOR = 'administrator';
    case ADMIN_LOGIN = 'admin/login';
    case ADMIN_PANEL = 'adminpanel';
    case ADMIN_PANEL_DASH = 'admin-panel';
    case ADMIN_AREA = 'admin_area';
    case ADMINAREA = 'adminarea';
    case PANEL = 'panel';
    case CONTROL_PANEL = 'controlpanel';
    case CP = 'cp';
    case BACKEND = 'backend';
    case MANAGE = 'manage';
    case MANAGER = 'manager';
    case DASHBOARD = 'dashboard';
    case WP_ADMIN = 'wp-admin';
    case WP_LOGIN = 'wp-login.php';
    case JOOMLA_ADMIN = 'joomla/administrator';
    case USER_LOGIN = 'user/login';
    case MAGENTO_ADMIN = 'index.php/admin';
    case MAGENTO_DASHBOARD = 'admin/dashboard';
    case LOGIN = 'login';
    case ADMINLOGIN = 'adminlogin';
    case ADMIN_LOGIN_ALT2 = 'admin-login';
    case SITEADMIN = 'siteadmin';
    case MODERATOR = 'moderator';
    case WEBADMIN = 'webadmin';
    case ADMINCP = 'admincp';
    case ADM = 'adm';
    case SYSTEM = 'system';
    case CMS = 'cms';
    case CONTROL = 'control';
    case PANELADMIN = 'paneladmin';
}
