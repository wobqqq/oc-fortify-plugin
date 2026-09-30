<?php

declare(strict_types=1);

arch('every file declares strict types')
    ->expect('Wobqqq\Fortify')
    ->toUseStrictTypes();

arch('no debugging calls are left behind')
    ->expect(['dd', 'dump', 'var_dump', 'print_r', 'ray', 'die', 'exit'])
    ->not->toBeUsed();

arch('data transfer objects are immutable')
    ->expect('Wobqqq\Fortify\Dto')
    ->toBeFinal()
    ->toBeReadonly();

arch('enums back every code the plugin shares')
    ->expect('Wobqqq\Fortify\Enums')
    ->toBeStringBackedEnums();

arch('services do not reach for the request directly')
    ->expect('Wobqqq\Fortify\Services')
    ->not->toUse(['Input', 'Request', Illuminate\Http\Request::class]);
