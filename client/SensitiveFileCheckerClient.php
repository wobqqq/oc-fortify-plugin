<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;

final class SensitiveFileCheckerClient
{
    /**
     * @param array<int, string> $urls
     * @return array<string, int|string>
     */
    public function request(array $urls): array
    {
        $client = new Client([
            'timeout' => 10,
            'http_errors' => false,
            'verify' => false,
            'allow_redirects' => [
                'max' => 5,
                'strict' => false,
                'referer' => true,
                'track_redirects' => true,
            ],
        ]);

        $requests = function ($urls) use ($client) {
            foreach ($urls as $url) {
                yield function () use ($client, $url) {
                    return $client->getAsync($url);
                };
            }
        };

        $responses = [];

        $pool = new Pool($client, $requests($urls), [
            'concurrency' => 7,
            'fulfilled' => function ($response, $index) use (&$responses, $urls) {
                $responses[$urls[$index]] = $response->getStatusCode();
            },
            'rejected' => function ($reason, $index) use (&$responses, $urls) {
                $responses[$urls[$index]] = 'error';
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        asort($responses);

        return $responses;
    }
}
