<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Widgets;

use Backend\Classes\ReportWidgetBase;
use Exception;
use Input;
use Wobqqq\Fortify\Instances\SensitiveFileCheckerDtoInstance;
use Wobqqq\Fortify\Instances\SensitiveTcpPortCheckerDtoListInstance;
use Wobqqq\Fortify\Instances\SslCertificateCheckerDtoListInstance;
use Wobqqq\Fortify\Services\SensitiveFileCheckerService;
use Wobqqq\Fortify\Services\SensitiveSslCertificateCheckerService;
use Wobqqq\Fortify\Services\SensitiveTcpPortCheckerService;
use Wobqqq\Fortify\Services\WidgetService;

class Fortify extends ReportWidgetBase
{
    /** @var string */
    protected $defaultAlias = 'fortify';

    /**
     * @return array<string, mixed>
     */
    public function defineProperties(): array
    {
        return [
            'title' => [
                'title' => 'wobqqq.fortify::lang.menu.fortify',
                'default' => 'wobqqq.fortify::lang.menu.fortify',
                'type' => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error',
            ],
        ];
    }

    /**
     * @return mixed
     * @throws \SystemException
     */
    public function render(): mixed
    {
        /** @var WidgetService $widgetService */
        $widgetService = app(WidgetService::class);
        $groups = $widgetService->getWidgetGroups();

        /** @var mixed $render */
        $render =  $this->makePartial('fortify', ['groups' => $groups]);

        return $render;
    }

    /**
     * @throws \SystemException
     */
    public function onFortifyAction(): mixed
    {
        try {
            /** @var string|null $action */
            $action = Input::get('action');

            if (empty($action)) {
                throw new Exception('Action cannot be empty.');
            }

            if (!method_exists($this, $action)) {
                throw new Exception('Action does not exist.');
            }

            return $this->$action();
        } catch (\Throwable $e) {
            return $this->makePartial('actions/error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * @return mixed
     * @throws \SystemException
     */
    private function sensitiveFilesCheckerOpenModalService(): mixed
    {
        try {
            $fortifySensitiveFileCheckerDto = SensitiveFileCheckerDtoInstance::instance()->get();

            return $this->makePartial(
                'actions/sensitive_file_checker_modal',
                ['sensitiveFileCheckerDto' => $fortifySensitiveFileCheckerDto],
            );
        } catch (\Throwable $e) {
            return $this->makePartial('actions/error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * @return mixed
     * @throws \SystemException
     */
    private function sensitiveFilesCheckerRunTestService(): mixed
    {
        try {
            /** @var string|null $url */
            $url = Input::get('url');

            if (empty($url)) {
                throw new Exception('URL cannot be empty.');
            }

            /** @var SensitiveFileCheckerService $sensitiveFileCheckerService */
            $sensitiveFileCheckerService = app(SensitiveFileCheckerService::class);
            [$urls, $numberOfPublicUrls] = $sensitiveFileCheckerService->check($url);

            return $this->makePartial(
                'actions/sensitive_file_checker_run_test_result',
                [
                    'urls' => $urls,
                    'numberOfPublicUrls' => $numberOfPublicUrls,
                ],
            );
        } catch (\Throwable $e) {
            return $this->makePartial('actions/error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * @return mixed
     * @throws \SystemException
     */
    private function sensitiveTcpPortsCheckerOpenModalService(): mixed
    {
        try {
            $fortifySensitiveTcpPortCheckerDtoList = SensitiveTcpPortCheckerDtoListInstance::instance()->get();

            return $this->makePartial(
                'actions/sensitive_tcp_port_checker_modal',
                ['sensitiveTcpPortCheckerDtoList' => $fortifySensitiveTcpPortCheckerDtoList],
            );
        } catch (\Throwable $e) {
            return $this->makePartial('actions/error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * @return mixed
     * @throws \SystemException
     */
    private function sensitiveTcpPortsCheckerRunTestService(): mixed
    {
        try {
            /** @var string|null $ip */
            $ip = Input::get('ip');

            if (empty($ip)) {
                throw new Exception('IP cannot be empty.');
            }

            /** @var SensitiveTcpPortCheckerService $sensitiveTcpPortCheckerService */
            $sensitiveTcpPortCheckerService = app(SensitiveTcpPortCheckerService::class);
            [$ports, $numberOfPublicPorts] = $sensitiveTcpPortCheckerService->check($ip);

            return $this->makePartial(
                'actions/sensitive_tcp_port_checker_run_test_result',
                [
                    'ports' => $ports,
                    'numberOfPublicPorts' => $numberOfPublicPorts,
                ],
            );
        } catch (\Throwable $e) {
            return $this->makePartial('actions/error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * @return mixed
     * @throws \SystemException
     */
    private function sslCertificateCheckerOpenModalService(): mixed
    {
        try {
            $sslCertificateCheckerDtoList = SslCertificateCheckerDtoListInstance::instance()->get();

            return $this->makePartial(
                'actions/ssl_certificate_checker_modal',
                [
                    'sslCertificateCheckerDtoList' => $sslCertificateCheckerDtoList,
                    'isOpenssl' => extension_loaded('openssl'),
                ],
            );
        } catch (\Throwable $e) {
            return $this->makePartial('actions/error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * @return mixed
     * @throws \SystemException
     */
    private function sslCertificateCheckerRunTestService(): mixed
    {
        try {
            /** @var string|null $host */
            $host = Input::get('host');

            if (empty($host)) {
                throw new Exception('Host cannot be empty.');
            }

            /** @var SensitiveSslCertificateCheckerService $sensitiveSslCertificateCheckerService */
            $sensitiveSslCertificateCheckerService = app(SensitiveSslCertificateCheckerService::class);
            [$ports, $numberOfHostsWithoutSsl] = $sensitiveSslCertificateCheckerService->check($host);
            return $this->makePartial(
                'actions/ssl_certificate_checker_run_test_result',
                [
                    'ports' => $ports,
                    'numberOfHostsWithoutSsl' => $numberOfHostsWithoutSsl,
                ],
            );
        } catch (\Throwable $e) {
            return $this->makePartial('actions/error', ['error' => $e->getMessage()]);
        }
    }
}
