<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Console;

use Illuminate\Console\Command;
use Wobqqq\Fortify\Services\ConfigService;

final class ConfigDisableCommand extends Command
{
    /** @var string */
    protected $name = 'wobqqq.fortify:config:disable';

    /** @var string */
    protected $description = 'Disable Config.';

    public function handle(ConfigService $configService): void
    {
        $configService->disable();

        $this->info('Config disabled.');
    }
}
