<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Dto;

use Wobqqq\Fortify\Enums\SessionSameSite;

final readonly class ConfigDto
{
    public function __construct(
        public bool $enabled,
        public ?SessionSameSite $sessionSameSite,
        public bool $sessionSecure,
        public bool $sessionHttpOnly,
        public bool $sessionEncrypt,
        public int $sessionLifetime,
        public bool $passwordPolicyAllowReset,
        public bool $passwordPolicyRequireUppercase,
        public bool $passwordPolicyRequireLowercase,
        public bool $passwordPolicyRequireNumber,
        public bool $passwordPolicyRequireNonAlpha,
        public bool $passwordPolicyExpireDays,
        public int $passwordPolicyMinLength,
        public bool $backendForceSecure,
        public bool $backendForceSingleSession,
    ) {
    }
}
