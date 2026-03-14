<?php

declare(strict_types=1);

namespace Tests;

use Ineersa\Html2text\Config;
use Ineersa\Html2text\HTML2Markdown;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RustFixturesTest extends TestCase
{
    #[DataProvider('rustFixtureCases')]
    public function testRustFixtures(string $filename): void
    {
        $converter = new HTML2Markdown(self::createRustParityConfig());

        $expected = self::getBaseline($filename);
        $html = self::cleanupEol((string) file_get_contents($filename));
        $actual = $converter->convert($html);

        $this->assertSame(rtrim($expected), rtrim($actual));
    }

    /**
     * @return array<string,mixed>
     */
    public static function rustFixtureCases(): array
    {
        $cases = [];
        $files = glob(__DIR__.'/files/rustTestsFiles/*.html');
        sort($files);

        foreach ($files as $file) {
            $cases[basename($file, '.html')] = [$file];
        }

        return $cases;
    }

    private static function cleanupEol(string $input): string
    {
        $input = (string) preg_replace("/\r+/", "\r", $input);

        return str_replace("\r\n", "\n", $input);
    }

    private static function getBaseline(string $htmlFile): string
    {
        $expectedFile = preg_replace('/\.html$/', '.md', $htmlFile);
        $content = (string) file_get_contents($expectedFile);

        return rtrim(self::cleanupEol($content));
    }

    private static function createRustParityConfig(): Config
    {
        return new Config(
            baseUrl: '',
            bodyWidth: 0,
            skipInternalLinks: false,
            listIndentBaseLevel: 1,
            indentDefinitionDescriptions: false,
            blankLineAfterDefinitionDescription: true,
            appendFinalListNewline: false,
            appendRawNewlineAfterTopLevelList: true,
            ulItemMark: '-',
            emphasisMark: '*'
        );
    }
}
