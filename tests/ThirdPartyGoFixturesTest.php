<?php

declare(strict_types=1);

namespace Tests;

use Ineersa\Html2text\Config;
use Ineersa\Html2text\HTML2Markdown;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ThirdPartyGoFixturesTest extends TestCase
{
    private const array ALLOWED_MISMATCH_BUCKETS = [
        'whitespace',
        'list_shape',
        'emphasis_style',
        'autolink_policy',
        'escaping',
        'table_format',
        'entity_handling',
        'parser_bug',
        'unclassified',
    ];

    /**
     * @var array<string, mixed>|null
     */
    private static ?array $divergenceMetadata = null;

    #[DataProvider('goFixtureCases')]
    public function testGoFixtures(string $fixtureId, ?string $htmlFile, ?string $expectedFile): void
    {
        if (null === $htmlFile || null === $expectedFile) {
            $this->markTestSkipped('No Go third-party fixtures imported yet.');
        }

        $converter = new HTML2Markdown(new Config());

        $html = self::cleanupEol((string) file_get_contents($htmlFile));
        $expected = self::getBaseline($expectedFile);
        $actual = self::normalizeForComparison($converter->convert($html));
        $expected = self::normalizeForComparison($expected);

        if ($expected === $actual) {
            return;
        }

        $fixturePath = preg_replace('/\.html$/', '', self::relativePath($htmlFile, self::goFixturesRoot()));
        if (null === $fixturePath) {
            $this->fail(\sprintf('Unable to derive fixture path for %s', $htmlFile));
        }

        $divergence = self::resolveDivergence($fixtureId, $fixturePath);
        if (null === $divergence) {
            $this->fail(
                \sprintf(
                    'Fixture mismatch for %s requires divergence metadata with exactly one bucket.',
                    $fixtureId,
                ),
            );
        }

        if ($divergence['styleOnly']) {
            return;
        }

        $this->assertSame(
            $expected,
            $actual,
            \sprintf(
                'Fixture mismatch for %s (bucket: %s, html: %s, expected: %s)',
                $fixtureId,
                $divergence['bucket'],
                $htmlFile,
                $expectedFile,
            )
        );
    }

    public function testGoFixtureRootDirectoryExists(): void
    {
        $this->assertDirectoryExists(self::goFixturesRoot());
    }

    /**
     * @return array<string, array{0: string, 1: string|null, 2: string|null}>
     */
    public static function goFixtureCases(): array
    {
        $cases = [];
        $root = self::goFixturesRoot();
        foreach (self::collectHtmlFiles($root) as $htmlFile) {
            $expectedFile = preg_replace('/\.html$/', '.md', $htmlFile);
            if (null === $expectedFile) {
                continue;
            }

            $relative = self::relativePath($htmlFile, $root);
            $sourceLibrary = self::sourceLibrary($relative);
            $fixturePath = preg_replace('/\.html$/', '', $relative);
            if (null === $fixturePath) {
                continue;
            }
            $fixtureId = \sprintf('go/%s/%s', $sourceLibrary, $fixturePath);

            $cases[$fixtureId] = [$fixtureId, $htmlFile, $expectedFile];
        }

        if ([] === $cases) {
            return ['go/scaffold/no-fixtures-yet' => ['go/scaffold/no-fixtures-yet', null, null]];
        }

        return $cases;
    }

    /**
     * @return list<string>
     */
    private static function collectHtmlFiles(string $root): array
    {
        if (!is_dir($root)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            if ('html' !== strtolower($file->getExtension())) {
                continue;
            }
            $files[] = $file->getPathname();
        }

        sort($files);

        return $files;
    }

    private static function cleanupEol(string $input): string
    {
        return str_replace(["\r\n", "\r"], "\n", $input);
    }

    private static function getBaseline(string $expectedFile): string
    {
        if (!is_file($expectedFile)) {
            self::fail(\sprintf('Missing expected markdown fixture for %s', $expectedFile));
        }

        $content = (string) file_get_contents($expectedFile);

        return self::cleanupEol($content);
    }

    private static function normalizeForComparison(string $input): string
    {
        $normalized = self::cleanupEol($input);
        $normalized = (string) preg_replace('/[ \t]+$/m', '', $normalized);
        $normalized = (string) preg_replace("/\n{3,}/", "\n\n", $normalized);

        return rtrim($normalized);
    }

    /**
     * @return array{bucket: string, styleOnly: bool}|null
     */
    private static function resolveDivergence(string $fixtureId, string $fixturePath): ?array
    {
        $entry = self::loadDivergenceMetadata()[$fixtureId] ?? self::loadDivergenceMetadata()[$fixturePath] ?? null;
        if (null === $entry) {
            return null;
        }

        if (!\is_array($entry)) {
            self::fail(\sprintf('Divergence metadata entry for %s must be an object.', $fixtureId));
        }

        if (!isset($entry['bucket']) || !\is_string($entry['bucket'])) {
            self::fail(\sprintf('Divergence metadata entry for %s must include string field "bucket".', $fixtureId));
        }

        if (!\in_array($entry['bucket'], self::ALLOWED_MISMATCH_BUCKETS, true)) {
            self::fail(
                \sprintf(
                    'Divergence metadata entry for %s has unsupported bucket "%s".',
                    $fixtureId,
                    $entry['bucket'],
                )
            );
        }

        $styleOnly = $entry['style_only'] ?? $entry['styleOnly'] ?? false;
        if (!\is_bool($styleOnly)) {
            self::fail(\sprintf('Divergence metadata entry for %s has non-boolean style_only/styleOnly flag.', $fixtureId));
        }

        return [
            'bucket' => $entry['bucket'],
            'styleOnly' => $styleOnly,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function loadDivergenceMetadata(): array
    {
        if (null !== self::$divergenceMetadata) {
            return self::$divergenceMetadata;
        }

        $metadataPath = __DIR__.'/files/thirdPartyFixtures/divergence_buckets.json';
        if (!is_file($metadataPath)) {
            self::fail(\sprintf('Missing divergence metadata file: %s', $metadataPath));
        }

        $content = (string) file_get_contents($metadataPath);
        $data = json_decode($content, true);
        if (!\is_array($data)) {
            self::fail('Divergence metadata file must decode to a JSON object.');
        }

        foreach ($data as $fixtureKey => $entry) {
            if (!\is_string($fixtureKey) || '' === trim($fixtureKey)) {
                self::fail('Divergence metadata keys must be non-empty strings.');
            }
            if (!\is_array($entry)) {
                self::fail(\sprintf('Divergence metadata entry for key "%s" must be an object.', (string) $fixtureKey));
            }
            if (isset($entry['buckets'])) {
                self::fail(
                    \sprintf(
                        'Divergence metadata entry for key "%s" must use one "bucket" field (exactly one bucket).',
                        (string) $fixtureKey,
                    )
                );
            }
        }

        self::$divergenceMetadata = $data;

        return self::$divergenceMetadata;
    }

    private static function goFixturesRoot(): string
    {
        return __DIR__.'/files/thirdPartyFixtures/go';
    }

    private static function relativePath(string $path, string $root): string
    {
        $prefix = rtrim($root, '/').'/';

        if (str_starts_with($path, $prefix)) {
            return substr($path, \strlen($prefix));
        }

        return basename($path);
    }

    private static function sourceLibrary(string $relativePath): string
    {
        $segments = explode('/', $relativePath);

        return '' !== $segments[0] ? $segments[0] : 'unknown';
    }
}
