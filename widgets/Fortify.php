<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Widgets;

use Backend\Classes\ReportWidgetBase;
use Backend\Facades\BackendAuth;
use Input;
use Log;
use October\Rain\Exception\ApplicationException;
use October\Rain\Exception\ValidationException;
use Throwable;
use Wobqqq\Fortify\Enums\ButtonAction;
use Wobqqq\Fortify\Enums\Permission;
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

    public function render(): mixed
    {
        /** @var WidgetService $widgetService */
        $widgetService = app(WidgetService::class);

        return $this->makePartial('fortify', ['groups' => $widgetService->getWidgetGroups()]);
    }

    public function onFortifyAction(): mixed
    {
        try {
            if (!BackendAuth::userHasAccess(Permission::FORTIFY->value)) {
                /** @var string $message */
                $message = trans('wobqqq.fortify::lang.errors.access_denied_message');

                return $this->errorPartial($message);
            }

            /** @var string|null $action */
            $action = Input::get('action');

            return match (ButtonAction::tryFrom((string)$action)) {
                ButtonAction::SENSITIVE_FILES_CHECKER_OPEN_MODAL => $this->sensitiveFilesCheckerOpenModal(),
                ButtonAction::SENSITIVE_FILES_CHECKER_RUN_TEST => $this->sensitiveFilesCheckerRunTest(),
                ButtonAction::SENSITIVE_TCP_PORTS_CHECKER_OPEN_MODAL => $this->sensitiveTcpPortsCheckerOpenModal(),
                ButtonAction::SENSITIVE_TCP_PORTS_CHECKER_RUN_TEST => $this->sensitiveTcpPortsCheckerRunTest(),
                ButtonAction::SSL_CERTIFICATE_CHECKER_OPEN_MODAL => $this->sslCertificateCheckerOpenModal(),
                ButtonAction::SSL_CERTIFICATE_CHECKER_RUN_TEST => $this->sslCertificateCheckerRunTest(),
                null => $this->errorPartial('Action does not exist.'),
            };
        } catch (ValidationException|ApplicationException $e) {
            return $this->errorPartial($e->getMessage());
        } catch (Throwable $e) {
            Log::error($e);

            /** @var string $message */
            $message = trans('wobqqq.fortify::lang.errors.bad_request_message');

            return $this->errorPartial($message);
        }
    }

    private function sensitiveFilesCheckerOpenModal(): mixed
    {
        return $this->makePartial(
            'actions/sensitive_file_checker_modal',
            ['sensitiveFileCheckerDto' => SensitiveFileCheckerDtoInstance::instance()->get()],
        );
    }

    private function sensitiveFilesCheckerRunTest(): mixed
    {
        $url = $this->requiredInput('url');

        /** @var SensitiveFileCheckerService $sensitiveFileCheckerService */
        $sensitiveFileCheckerService = app(SensitiveFileCheckerService::class);
        $report = $sensitiveFileCheckerService->check($url);

        return $this->makePartial(
            'actions/sensitive_file_checker_run_test_result',
            [
                'urls' => $report->results,
                'numberOfPublicUrls' => $report->failures,
            ],
        );
    }

    private function sensitiveTcpPortsCheckerOpenModal(): mixed
    {
        return $this->makePartial(
            'actions/sensitive_tcp_port_checker_modal',
            ['sensitiveTcpPortCheckerDtoList' => SensitiveTcpPortCheckerDtoListInstance::instance()->get()],
        );
    }

    private function sensitiveTcpPortsCheckerRunTest(): mixed
    {
        $ip = $this->requiredInput('ip');

        /** @var SensitiveTcpPortCheckerService $sensitiveTcpPortCheckerService */
        $sensitiveTcpPortCheckerService = app(SensitiveTcpPortCheckerService::class);
        $report = $sensitiveTcpPortCheckerService->check($ip);

        return $this->makePartial(
            'actions/sensitive_tcp_port_checker_run_test_result',
            [
                'ports' => $report->results,
                'numberOfPublicPorts' => $report->failures,
            ],
        );
    }

    private function sslCertificateCheckerOpenModal(): mixed
    {
        return $this->makePartial(
            'actions/ssl_certificate_checker_modal',
            [
                'sslCertificateCheckerDtoList' => SslCertificateCheckerDtoListInstance::instance()->get(),
                'isOpenssl' => extension_loaded('openssl'),
            ],
        );
    }

    private function sslCertificateCheckerRunTest(): mixed
    {
        $host = $this->requiredInput('host');

        /** @var SensitiveSslCertificateCheckerService $sensitiveSslCertificateCheckerService */
        $sensitiveSslCertificateCheckerService = app(SensitiveSslCertificateCheckerService::class);
        $report = $sensitiveSslCertificateCheckerService->check($host);

        return $this->makePartial(
            'actions/ssl_certificate_checker_run_test_result',
            [
                'ports' => $report->results,
                'numberOfHostsWithoutSsl' => $report->failures,
            ],
        );
    }

    private function requiredInput(string $name): string
    {
        $value = Input::get($name);

        if (!is_string($value) || trim($value) === '') {
            throw new ValidationException([$name => sprintf('The %s cannot be empty.', $name)]);
        }

        return trim($value);
    }

    private function errorPartial(string $error): mixed
    {
        return $this->makePartial('actions/error', ['error' => $error]);
    }
}
