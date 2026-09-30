<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class SensitiveFileCheckerClient
{
    private const CONCURRENCY = 7;

    public function __construct(private readonly ?Client $client = null)
    {
    }

    /**
     * A redirect is not followed: a sensitive path that redirects to the home page
     * answers 200 there and would read as exposed.
     *
     * @param array<int, string> $urls
     *
     * @return array<string, int|string>
     */
    public function request(array $urls): array
    {
        $urls = array_values($urls);

        $client = $this->client ?? new Client();

        $requests = static function (string ...$urls) {
            foreach ($urls as $url) {
                yield new Request('GET', $url);
            }
        };

        $responses = [];

        $pool = new Pool($client, $requests(...$urls), [
            'concurrency' => self::CONCURRENCY,
            'options' => [
                'timeout' => 10,
                'connect_timeout' => 5,
                'http_errors' => false,
                'allow_redirects' => false,
                'headers' => ['User-Agent' => 'Fortify sensitive files checker'],
            ],
            'fulfilled' => static function (ResponseInterface $response, int $index) use (&$responses, $urls): void {
                $responses[$urls[$index]] = $response->getStatusCode();
            },
            'rejected' => static function (mixed $reason, int $index) use (&$responses, $urls): void {
                $responses[$urls[$index]] = 'error';
            },
        ]);

        $pool->promise()->wait();

        asort($responses);

        return $responses;
    }
}
