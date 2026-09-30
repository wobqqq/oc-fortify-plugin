<?php

declare(strict_types=1);

use Backend\Classes\AuthManager;
use Wobqqq\Fortify\Tests\TestCase;

pest()->extend(TestCase::class)->in('Unit', 'Feature');

function signInAs(string ...$permissions): AuthManager
{
    /** @var AuthManager $auth */
    $auth = app('backend.auth');
    $auth->permissions = array_values($permissions);

    return $auth;
}
