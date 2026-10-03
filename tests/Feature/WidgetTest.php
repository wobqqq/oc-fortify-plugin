<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use System\Classes\PluginManager;
use System\Classes\UpdateManager;
use Wobqqq\Fortify\Contracts\TlsCertificateProbe;
use Wobqqq\Fortify\Dto\WidgetGroupItemDto;
use Wobqqq\Fortify\Dto\WidgetItemLinkDto;
use Wobqqq\Fortify\Enums\ButtonAction;
use Wobqqq\Fortify\Enums\FortifyEvent;
use Wobqqq\Fortify\Enums\WidgetItemColor;
use Wobqqq\Fortify\Models\Fortify as FortifySettings;
use Wobqqq\Fortify\Services\WidgetService;
use Wobqqq\Fortify\Transformers\FortifyTransformer;
use Wobqqq\Fortify\Widgets\Fortify;

/**
 * @param array<string, string> $input
 */
function widgetAction(array $input): string
{
    app()->instance('request', Request::create('/admin', 'POST', $input));

    return widgetHtml((new Fortify())->onFortifyAction());
}

function widgetHtml(mixed $rendered): string
{
    return is_string($rendered) ? $rendered : throw new UnexpectedValueException('The widget did not render HTML.');
}

function widgetGroupItem(string $group, string $name): WidgetGroupItemDto
{
    foreach (app(WidgetService::class)->getWidgetGroups() as $widgetGroup) {
        if ($widgetGroup->name !== $group) {
            continue;
        }

        foreach ($widgetGroup->list as $item) {
            $label = trans($item->name);

            if ($item->name === $name || (is_string($label) && str_contains($label, $name))) {
                return $item;
            }
        }
    }

    throw new UnexpectedValueException(sprintf('The widget has no "%s" item in "%s".', $name, $group));
}

it('draws the info, modules and config groups', function (): void {
    $html = widgetHtml((new Fortify())->render());

    expect($html)->toContain('Info')->toContain('Modules')->toContain('Config')
        ->and(substr_count($html, 'data-handler="onFortifyAction"'))->toBe(3);
});

it('flags debug mode, a non-production environment and a guessable backend URI', function (): void {
    config(['app.debug' => true, 'app.env' => 'local', 'backend.uri' => '/admin']);

    expect(widgetGroupItem('wobqqq.fortify::lang.fields.info', 'wobqqq.fortify::lang.fields.debug_mode_is_disabled')->color)->toBe(WidgetItemColor::DANGER)
        ->and(widgetGroupItem('wobqqq.fortify::lang.fields.info', 'wobqqq.fortify::lang.fields.app_production_env_is_enabled')->color)->toBe(WidgetItemColor::DANGER)
        ->and(widgetGroupItem('wobqqq.fortify::lang.fields.info', '/admin')->color)->toBe(WidgetItemColor::DANGER);

    config(['app.debug' => false, 'app.env' => 'production', 'backend.uri' => '/manage-9f2c']);

    expect(widgetGroupItem('wobqqq.fortify::lang.fields.info', 'wobqqq.fortify::lang.fields.debug_mode_is_disabled')->color)->toBe(WidgetItemColor::SUCCESS)
        ->and(widgetGroupItem('wobqqq.fortify::lang.fields.info', '/manage-9f2c')->color)->toBe(WidgetItemColor::SUCCESS);
});

it('escapes the backend URI it prints', function (): void {
    config(['backend.uri' => '/<script>alert(1)</script>']);

    $html = widgetHtml((new Fortify())->render());

    expect($html)->not->toContain('<script>alert(1)</script>')
        ->and($html)->toContain('&lt;script&gt;');
});

it('flags pending updates', function (): void {
    UpdateManager::instance()->pending = 3;

    expect(widgetGroupItem('wobqqq.fortify::lang.fields.info', 'wobqqq.fortify::lang.fields.status_updates_pending')->color)
        ->toBe(WidgetItemColor::WARNING);
});

it('links every missing module to the marketplace and a disabled one to the plugin list', function (): void {
    PluginManager::instance()->plugins = ['wobqqq.fortifycsp' => true];

    $csp = widgetGroupItem('wobqqq.fortify::lang.fields.modules', 'wobqqq.fortify::lang.fields.csp');
    $ipBlocker = widgetGroupItem('wobqqq.fortify::lang.fields.modules', 'IP Blocker');

    expect($csp->buttons[0]->name)->toBe('wobqqq.fortify::lang.buttons.enable_plugin')
        ->and($ipBlocker->buttons[0])->toBeInstanceOf(WidgetItemLinkDto::class)
        ->and($ipBlocker->buttons[0])->toHaveProperty('link', 'https://octobercms.com/plugin/wobqqq-fortifyipblocker');
});

it('lets an installed module draw its own item', function (): void {
    PluginManager::instance()->plugins = ['wobqqq.fortifycsp' => false];

    Event::listen(FortifyEvent::SERVICES_WIDGET_GROUP_ITEM_CSP->value, function (WidgetGroupItemDto &$item): void {
        $item = FortifyTransformer::widgetGroupItemDto('CSP is on', [], WidgetItemColor::SUCCESS, 'icon-lock');
    });

    expect(widgetGroupItem('wobqqq.fortify::lang.fields.modules', 'CSP is on')->color)->toBe(WidgetItemColor::SUCCESS);
});

it('reads the config the application runs with while Fortify is disabled', function (): void {
    config(['backend.password_policy.min_length' => 16, 'backend.password_policy.expire_days' => 90]);

    expect(widgetGroupItem('wobqqq.fortify::lang.fields.config', 'wobqqq.fortify::lang.fields.password_policy_min_length')->color)->toBe(WidgetItemColor::SUCCESS)
        ->and(widgetGroupItem('wobqqq.fortify::lang.fields.config', 'wobqqq.fortify::lang.fields.password_policy_expire_days')->color)->toBe(WidgetItemColor::SUCCESS);

    FortifySettings::set('config', ['enabled' => true, 'password_policy_min_length' => 8, 'password_policy_expire_after_days' => 0]);
    Wobqqq\Fortify\Instances\ConfigDtoInstance::forgetInstance();

    expect(widgetGroupItem('wobqqq.fortify::lang.fields.config', 'wobqqq.fortify::lang.fields.password_policy_min_length')->color)->toBe(WidgetItemColor::DANGER)
        ->and(widgetGroupItem('wobqqq.fortify::lang.fields.config', 'wobqqq.fortify::lang.fields.password_policy_expire_days')->color)->toBe(WidgetItemColor::WARNING);
});

it('refuses the widget actions to an administrator without the Fortify permission', function (): void {
    signInAs('some-other-permission');

    expect(widgetAction(['action' => ButtonAction::SENSITIVE_FILES_CHECKER_OPEN_MODAL->value]))
        ->toContain('Sorry, this page is not accessible to you.');
});

it('only runs the actions the widget offers', function (string $method): void {
    signInAs('app-fortify');

    expect(widgetAction(['action' => $method]))->toContain('Action does not exist.');
})->with(['render', 'onFortifyAction', 'makePartial', 'sensitiveFilesCheckerRunTest', '']);

it('opens each checker modal', function (ButtonAction $action, string $title): void {
    signInAs('app-fortify');

    expect(widgetAction(['action' => $action->value]))->toContain($title);
})->with([
    [ButtonAction::SENSITIVE_FILES_CHECKER_OPEN_MODAL, 'Sensitive files checker'],
    [ButtonAction::SENSITIVE_TCP_PORTS_CHECKER_OPEN_MODAL, 'Sensitive TCP ports checker'],
    [ButtonAction::SSL_CERTIFICATE_CHECKER_OPEN_MODAL, 'SSL certificate checker'],
]);

it('escapes the checked targets in the modals', function (): void {
    signInAs('app-fortify');
    FortifySettings::set('tests', [
        'sensitive_files_checker_urls' => [['url' => "https://example.com/'});alert(1);//"]],
        'sensitive_files_checker_paths' => [['path' => '.env']],
    ]);

    $html = widgetAction(['action' => ButtonAction::SENSITIVE_FILES_CHECKER_OPEN_MODAL->value]);

    expect($html)->not->toContain("'});alert(1);//")
        ->and($html)->toContain('data-request-data="{&quot;_dash_definition&quot;:&quot;system&quot;');
});

it('runs a check and escapes the error it answers with', function (): void {
    signInAs('app-fortify');

    expect(widgetAction(['action' => ButtonAction::SSL_CERTIFICATE_CHECKER_RUN_TEST->value, 'host' => '']))
        ->toContain('The host cannot be empty.');

    FortifySettings::set('tests', ['sensitive_tcp_ports_checker_ips' => [['ip' => '127.0.0.1', 'ports' => '1']]]);

    expect(widgetAction(['action' => ButtonAction::SENSITIVE_TCP_PORTS_CHECKER_RUN_TEST->value, 'ip' => '127.0.0.1']))
        ->toContain('Sensitive TCP ports checker report');
});

it('runs the sensitive files and certificate checks', function (): void {
    signInAs('app-fortify');

    expect(widgetAction(['action' => ButtonAction::SENSITIVE_FILES_CHECKER_RUN_TEST->value, 'url' => 'https://unlisted.example']))
        ->toContain('No sensitive files are publicly accessible via HTTP.')
        ->and(widgetAction(['action' => ButtonAction::SSL_CERTIFICATE_CHECKER_RUN_TEST->value, 'host' => 'unlisted.example']))
        ->toContain('SSL certificate checker report');
});

it('logs an unexpected failure and answers without its details', function (): void {
    signInAs('app-fortify');
    FortifySettings::set('tests', ['ssl_certificate_checker_hosts' => [['host' => 'example.com', 'ports' => '443']]]);
    app()->instance(TlsCertificateProbe::class, new class () implements TlsCertificateProbe {
        #[Override]
        public function request(string $host, int $port = 443): array
        {
            throw new RuntimeException('socket secret detail');
        }
    });
    $log = Log::spy();

    $html = widgetAction(['action' => ButtonAction::SSL_CERTIFICATE_CHECKER_RUN_TEST->value, 'host' => 'example.com']);

    expect($html)->toContain('Please check your input and try again.')
        ->and($html)->not->toContain('socket secret detail');
    $log->shouldHaveReceived('error')->once();
});
