<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Instances;

use October\Rain\Support\Traits\Singleton;
use Wobqqq\Fortify\Dto\SllCertificateCheckerDto;
use Wobqqq\Fortify\Transformers\FortifyTransformer;

final class SslCertificateCheckerDtoListInstance
{
    use Singleton;

    /** @var array<int, SllCertificateCheckerDto>|null */
    private ?array $sslCertificateCheckerDtoList = null;

    /**
     * @return array<int, SllCertificateCheckerDto>
     */
    public function get(): array
    {
        if (is_array($this->sslCertificateCheckerDtoList)) {
            return $this->sslCertificateCheckerDtoList;
        }

        return $this->sslCertificateCheckerDtoList = FortifyTransformer::sslCertificateCheckerDtoList();
    }
}
