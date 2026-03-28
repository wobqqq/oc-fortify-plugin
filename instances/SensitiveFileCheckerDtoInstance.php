<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Instances;

use October\Rain\Support\Traits\Singleton;
use Wobqqq\Fortify\Dto\SensitiveFileCheckerDto;
use Wobqqq\Fortify\Transformers\FortifyTransformer;

final class SensitiveFileCheckerDtoInstance
{
    use Singleton;

    private ?SensitiveFileCheckerDto $sensitiveFileCheckerDto = null;

    public function get(): SensitiveFileCheckerDto
    {
        if ($this->sensitiveFileCheckerDto instanceof SensitiveFileCheckerDto) {
            return $this->sensitiveFileCheckerDto;
        }

        return $this->sensitiveFileCheckerDto = FortifyTransformer::sensitiveFileCheckerDto();
    }
}
