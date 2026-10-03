<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/Plugin.php',
        __DIR__ . '/cache',
        __DIR__ . '/client',
        __DIR__ . '/console',
        __DIR__ . '/contracts',
        __DIR__ . '/dto',
        __DIR__ . '/enums',
        __DIR__ . '/instances',
        __DIR__ . '/listeners',
        __DIR__ . '/models',
        __DIR__ . '/queries',
        __DIR__ . '/services',
        __DIR__ . '/transformers',
        __DIR__ . '/updates',
        __DIR__ . '/widgets',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        __DIR__ . '/tests/Stubs',
        '*/partials/*',
    ])
    ->withPhpSets(php82: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        earlyReturn: true,
    );
