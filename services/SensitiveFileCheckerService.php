<?php

declare(strict_types=1);

namespace Wobqqq\Fortify\Services;

use Config;
use Wobqqq\Fortify\Client\SensitiveFileCheckerClient;
use Wobqqq\Fortify\Dto\SensitiveFileCheckerTestResultDto;
use Wobqqq\Fortify\Instances\SensitiveFileCheckerDtoInstance;
use Wobqqq\Fortify\Transformers\FortifyTransformer;

final readonly class SensitiveFileCheckerService
{
    private const PATHS = [
        '.env',
        '.env.example',
        '.env.local',
        '.env.testing',
        '.env.production',
        'composer.json',
        'composer.lock',
        'auth.json',
        'auth.example.json',
        'phpunit.xml',
        'phpunit.xml.dist',
        'config/cms.php',
        'config/app.php',
        'config/backend.php',
        'config/editor.php',
        'config/cache.php',
        'config/logging.php',
        'config/hashing.php',
        'config/filesystems.php',
        'config/database.php',
        'config/media.php',
        'config/multisite.php',
        'config/services.php',
        'config/queue.php',
        'config/mail.php',
        'config/session.php',
        'config/system.php',
        'config/view.php',
        '.git/config',
        '.gitignore',
        '.gitattributes',
        '.gitmodules',
        '.htpasswd',
        'README.md',
        'README.rst',
        'Makefile',
        '.editorconfig',
        'docker-compose.yml',
        '.dockerignore',
        'package.json',
        'package-lock.json',
        'yarn.lock',
        '.npmrc',
        'storage/logs',
        'storage/app/uploads/protected',
        'modules',
        'plugins',
        'vendor',
        'config',
    ];

    public function __construct(private SensitiveFileCheckerClient $sensitiveFileCheckerClient)
    {
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function generateDefaultUrlsSettingsData(): array
    {
        /** @var string $appUrl */
        $appUrl = Config::get('app.url');
        $appUrl = rtrim($appUrl, '/');

        return [['url' => $appUrl]];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function generateDefaultPathsSettingsData(): array
    {
        return array_map(static fn (string $path): array => ['path' => $path], self::PATHS);
    }

    /**
     * @return array{0: array<int, SensitiveFileCheckerTestResultDto>, 1: int}
     */
    public function check(string $url): array
    {
        $fortifySensitiveFileCheckerDto = SensitiveFileCheckerDtoInstance::instance()->get();

        if (!in_array($url, $fortifySensitiveFileCheckerDto->urls, true)) {
            return [[], 0];
        }

        $urls = array_values(array_map(
            static fn (string $path): string => sprintf('%s/%s', $url, $path),
            $fortifySensitiveFileCheckerDto->paths,
        ));

        $responses = $this->sensitiveFileCheckerClient->request($urls);

        $urls = [];

        $numberOfPublicUrls = 0;

        foreach ($responses as $checkedUrl => $status) {
            $isPositive = $status !== 'error' && $status !== 200;
            $urls[] = FortifyTransformer::sensitiveFileCheckerTestResultDto(
                $checkedUrl,
                (string)$status,
                $isPositive,
            );

            if (!$isPositive) {
                $numberOfPublicUrls++;
            }
        }

        return [$urls, $numberOfPublicUrls];
    }
}
