<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Enums;

enum ButtonAction: string
{
    case SENSITIVE_FILES_CHECKER_OPEN_MODAL = 'sensitiveFilesCheckerOpenModalService';
    case SENSITIVE_FILES_CHECKER_RUN_TEST = 'sensitiveFilesCheckerRunTestService';
    case SENSITIVE_TCP_PORTS_CHECKER_OPEN_MODAL = 'sensitiveTcpPortsCheckerOpenModalService';
    case SENSITIVE_TCP_PORTS_CHECKER_RUN_TEST = 'sensitiveTcpPortsCheckerRunTestService';
    case SSL_CERTIFICATE_CHECKER_OPEN_MODAL = 'sslCertificateCheckerOpenModalService';
    case SSL_CERTIFICATE_CHECKER_RUN_TEST = 'sslCertificateCheckerRunTestService';
}
