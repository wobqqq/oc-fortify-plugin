<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Instances;

use October\Rain\Support\Traits\Singleton;
use Wobqqq\Fortify\Dto\SensitiveTcpPortCheckerDto;
use Wobqqq\Fortify\Transformers\FortifyTransformer;

final class SensitiveTcpPortCheckerDtoListInstance
{
    use Singleton;

    /** @var array<int, SensitiveTcpPortCheckerDto>|null */
    private ?array $sensitiveTcpPortCheckerDtoList = null;

    /**
     * @return array<int, SensitiveTcpPortCheckerDto>
     */
    public function get(): array
    {
        if (is_array($this->sensitiveTcpPortCheckerDtoList)) {
            return $this->sensitiveTcpPortCheckerDtoList;
        }

        return $this->sensitiveTcpPortCheckerDtoList = FortifyTransformer::sensitiveTcpPortCheckerDtoList();
    }
}
