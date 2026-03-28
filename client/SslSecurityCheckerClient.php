<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Client;

use Arr;

final class SslSecurityCheckerClient
{
    /**
     * @return array<string, mixed>
     */
    public function request(string $host, int $port = 443): array
    {
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $client = @stream_socket_client(
            sprintf('ssl://%s:%s', $host, $port),
            $errorCode,
            $errorMessage,
            10,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$client) {
            return [
                'success' => false,
                'error' => $errorMessage,
            ];
        }

        $params = stream_context_get_params($client);
        /** @var \OpenSSLCertificate|null $cert */
        $cert = Arr::get($params, 'options.ssl.peer_certificate');

        if (!$cert) {
            fclose($client);

            return [
                'success' => false,
                'error' => 'Certificate not found',
            ];
        }

        $certData = openssl_x509_parse($cert);

        if ($certData === false) {
            fclose($client);

            return [
                'success' => false,
                'error' => 'Unable to parse certificate',
            ];
        }

        /** @var string|int|null $issuedOn */
        $issuedOn = Arr::get($certData, 'validFrom_time_t');
        $issuedOn = (int)$issuedOn;
        /** @var string|int|null $expiresOn */
        $expiresOn = Arr::get($certData, 'validTo_time_t');
        $expiresOn = (int)$expiresOn;

        fclose($client);

        return [
            'success' => true,
            'issued_on' => $issuedOn,
            'expires_on' => $expiresOn,
        ];
    }
}
