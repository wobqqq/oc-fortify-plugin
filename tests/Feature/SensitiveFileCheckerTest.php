<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Wobqqq\Fortify\Client\SensitiveFileCheckerClient;
use Wobqqq\Fortify\Models\Fortify;
use Wobqqq\Fortify\Services\SensitiveFileCheckerService;

/**
 * @param array<string, Response|Throwable> $answers the answer for each requested URL
 *
 * @return ArrayObject<int, string> the URLs requested
 */
function fakeSensitiveFileResponses(array $answers): ArrayObject
{
    /** @var ArrayObject<int, string> $requested */
    $requested = new ArrayObject();

    $handler = static function (Psr\Http\Message\RequestInterface $request, array $options) use ($answers, $requested): GuzzleHttp\Promise\PromiseInterface {
        $url = (string)$request->getUri();
        $requested[] = $url;
        $answer = $answers[$url] ?? new Response(404);

        if ($answer instanceof Throwable) {
            return GuzzleHttp\Promise\Create::rejectionFor($answer);
        }

        return GuzzleHttp\Promise\Create::promiseFor($answer);
    };

    app()->instance(
        SensitiveFileCheckerClient::class,
        new SensitiveFileCheckerClient(new Client(['handler' => HandlerStack::create($handler), 'http_errors' => false])),
    );

    return $requested;
}

beforeEach(function (): void {
    Fortify::set('tests', [
        'sensitive_files_checker_urls' => [['url' => 'https://example.com']],
        'sensitive_files_checker_paths' => [['path' => '.env'], ['path' => 'composer.json'], ['path' => '.git/config']],
    ]);
});

it('reports the paths a site serves as exposed', function (): void {
    fakeSensitiveFileResponses([
        'https://example.com/.env' => new Response(200),
        'https://example.com/.git/config' => new ConnectException('timeout', new Request('GET', 'https://example.com/.git/config')),
    ]);

    [$results, $exposed] = app(SensitiveFileCheckerService::class)->check('https://example.com');

    $statuses = collect($results)->mapWithKeys(fn ($result): array => [$result->url => [$result->status, $result->isPositive]]);

    expect($exposed)->toBe(2)
        ->and($statuses['https://example.com/.env'])->toBe(['200', false])
        ->and($statuses['https://example.com/.git/config'])->toBe(['error', false])
        ->and($statuses['https://example.com/composer.json'])->toBe(['404', true]);
});

it('does not read a redirect to another page as an exposed file', function (): void {
    fakeSensitiveFileResponses([
        'https://example.com/.env' => new Response(301, ['Location' => 'https://example.com/']),
        'https://example.com/' => new Response(200),
    ]);

    [, $exposed] = app(SensitiveFileCheckerService::class)->check('https://example.com');

    expect($exposed)->toBe(0);
});

it('only scans the sites listed in the settings', function (): void {
    $requested = fakeSensitiveFileResponses([]);

    [$results, $exposed] = app(SensitiveFileCheckerService::class)->check('https://attacker.example');

    expect($results)->toBe([])
        ->and($exposed)->toBe(0)
        ->and($requested->getArrayCopy())->toBe([]);
});

it('defaults to the application URL and the common sensitive paths', function (): void {
    $service = app(SensitiveFileCheckerService::class);

    expect($service->generateDefaultUrlsSettingsData())->toBe([['url' => 'https://fortify.test']])
        ->and($service->generateDefaultPathsSettingsData())->toContain(['path' => '.env'], ['path' => 'auth.json']);
});
