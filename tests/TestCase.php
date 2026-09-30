<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Tests;

use Backend\Classes\AuthManager;
use Backend\Helpers\Backend;
use Backend\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use LogicException;
use October\Rain\Database\Model;
use October\Rain\Events\EventServiceProvider;
use October\Rain\Extension\Container as ExtensionContainer;
use Orchestra\Testbench\TestCase as BaseTestCase;
use ReflectionProperty;
use System\Classes\PluginManager;
use System\Classes\UpdateManager;
use System\Models\SettingModel;
use Wobqqq\Fortify\Plugin;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        SettingModel::resetStore();
        PluginManager::forgetInstance();
        UpdateManager::forgetInstance();
        $this->forgetPluginInstances();

        $this->createBackendUsersTable();

        $plugin = new Plugin($this->app());
        $plugin->register();
        $plugin->boot();
    }

    /**
     * October keeps model event listeners and extend() callbacks in statics, while every
     * test boots a new application and the plugin again.
     */
    protected function tearDown(): void
    {
        ExtensionContainer::clearExtensions();
        User::flushEventListeners();
        SettingModel::flushEventListeners();
        Model::flushEventListeners();

        parent::tearDown();
    }

    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [EventServiceProvider::class];
    }

    /**
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Backend' => \Backend\Facades\Backend::class,
            'BackendAuth' => \Backend\Facades\BackendAuth::class,
            'Event' => \October\Rain\Support\Facades\Event::class,
            'Input' => \October\Rain\Support\Facades\Input::class,
            'Str' => \October\Rain\Support\Str::class,
            'Url' => \Illuminate\Support\Facades\URL::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        /** @var \Illuminate\Translation\Translator $translator */
        $translator = $app->make('translator');
        $translator->addNamespace('wobqqq.fortify', __DIR__ . '/../lang');

        $config = $app->make(\Illuminate\Contracts\Config\Repository::class);

        $app->singleton('backend.helper', Backend::class);
        $app->singleton('backend.auth', AuthManager::class);

        $config->set('backend.password_policy', [
            'allow_reset' => true,
            'min_length' => 4,
            'require_uppercase' => false,
            'require_lowercase' => false,
            'require_number' => false,
            'require_nonalpha' => false,
            'expire_days' => false,
        ]);
        $config->set('backend.uri', '/admin');
        $config->set('backend.force_secure', false);
        $config->set('backend.force_single_session', false);
    }

    protected function app(): Application
    {
        if (!$this->app instanceof Application) {
            throw new LogicException('The application has not been created yet.');
        }

        return $this->app;
    }

    private function forgetPluginInstances(): void
    {
        foreach ([
            \Wobqqq\Fortify\Instances\ConfigDtoInstance::class,
            \Wobqqq\Fortify\Instances\SensitiveFileCheckerDtoInstance::class,
            \Wobqqq\Fortify\Instances\SensitiveTcpPortCheckerDtoListInstance::class,
            \Wobqqq\Fortify\Instances\SslCertificateCheckerDtoListInstance::class,
        ] as $instance) {
            $instance::forgetInstance();
        }

        (new ReflectionProperty(\Wobqqq\Fortify\Services\ConfigService::class, 'overrideConfig'))->setValue(null, false);
    }

    private function createBackendUsersTable(): void
    {
        Schema::create('backend_users', static function (Blueprint $table): void {
            $table->increments('id');
            $table->string('login')->unique();
            $table->boolean('is_superuser')->default(false);
            $table->timestamp('last_login')->nullable();
        });
    }
}
