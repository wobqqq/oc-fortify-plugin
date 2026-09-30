<?php

declare(strict_types=1);

namespace Wobqqq\Fortify;

use Event;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use Wobqqq\Fortify\Console\ConfigDisableCommand;
use Wobqqq\Fortify\Enums\Permission;
use Wobqqq\Fortify\Listeners\BackendUserListener;
use Wobqqq\Fortify\Listeners\FortifyListener;
use Wobqqq\Fortify\Models\Fortify;
use Wobqqq\Fortify\Services\ConfigService;

final class Plugin extends PluginBase
{
    public function register(): void
    {
        $this->registerConsoleCommand('wobqqq.fortify:config:disable', ConfigDisableCommand::class);
    }

    public function boot(): void
    {
        $this->registerEvents();
        $this->registerViews();
        $this->runService();
    }

    /**
     * @return array<string, mixed>
     */
    public function registerSettings(): array
    {
        return [
            'fortify' => [
                'label' => 'wobqqq.fortify::lang.menu.fortify',
                'description' => 'wobqqq.fortify::lang.messages.fortify_description',
                'icon' => 'icon-shield',
                'class' => Fortify::class,
                'permissions' => [Permission::FORTIFY->value],
                'order' => 1,
                'category' => SettingsManager::CATEGORY_SYSTEM,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function registerReportWidgets(): array
    {
        return [
            Widgets\Fortify::class => [
                'label' => 'wobqqq.fortify::lang.widgets.fortify',
                'context' => 'dashboard',
                'permissions' => [Permission::FORTIFY->value],
            ],
        ];
    }

    private function registerEvents(): void
    {
        Event::subscribe(FortifyListener::class);
        Event::subscribe(BackendUserListener::class);
    }

    private function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'wobqqq.fortify');
    }

    private function runService(): void
    {
        /** @var ConfigService $configService */
        $configService = app(ConfigService::class);
        $configService->overrideConfig();
    }
}
