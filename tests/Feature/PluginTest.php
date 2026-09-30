<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Symfony\Component\Yaml\Yaml;
use Wobqqq\Fortify\Models\Fortify;
use Wobqqq\Fortify\Plugin;
use Wobqqq\Fortify\Widgets\Fortify as FortifyWidget;

it('registers the settings page and the dashboard widget behind the Fortify permission', function (): void {
    $plugin = new Plugin(app());

    expect($plugin->registerSettings())->toMatchArray(['fortify' => [
        'label' => 'wobqqq.fortify::lang.menu.fortify',
        'description' => 'wobqqq.fortify::lang.messages.fortify_description',
        'icon' => 'icon-shield',
        'class' => Fortify::class,
        'permissions' => ['app-fortify'],
        'order' => 1,
        'category' => 'system::lang.system.categories.system',
    ]])->and($plugin->registerReportWidgets())->toMatchArray([FortifyWidget::class => [
        'label' => 'wobqqq.fortify::lang.widgets.fortify',
        'context' => 'dashboard',
        'permissions' => ['app-fortify'],
    ]]);
});

it('declares the permission it guards the settings with', function (): void {
    expect(Yaml::parseFile(dirname(__DIR__, 2) . '/plugin.yaml'))->toHaveKey('permissions.app-fortify');
});

it('lists every update script in the version history', function (): void {
    $updates = dirname(__DIR__, 2) . '/updates';
    /** @var array<string, array<int, string>> $history */
    $history = Yaml::parseFile($updates . '/version.yaml');
    $listed = array_values(array_filter(array_merge(...array_values($history)), static fn (string $entry): bool => str_ends_with($entry, '.php')));
    $scripts = glob($updates . '/*.php');

    expect($listed)->toEqualCanonicalizing(array_map(basename(...), $scripts === false ? [] : $scripts));
});

it('shows the pages a blocked visitor sees', function (string $view, string $title): void {
    expect(View::make($view)->render())->toContain($title)->toContain('<!DOCTYPE html>');
})->with([
    ['wobqqq.fortify::denied', 'Access denied'],
    ['wobqqq.fortify::bad-request', 'Something went wrong'],
]);
