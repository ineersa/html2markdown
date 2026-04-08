<?php

declare(strict_types=1);

namespace Tests;

use Ineersa\Html2text\Utilities\UrlUtilities;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UrlUtilitiesTest extends TestCase
{
    #[DataProvider('provideUrlJoinSamples')]
    public function testUrlJoin(string $base, string $link, string $expected): void
    {
        $this->assertSame($expected, UrlUtilities::urlJoin($base, $link));
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function provideUrlJoinSamples(): array
    {
        return [
            'empty link' => ['http://example.com/base', '', 'http://example.com/base'],
            'empty base' => ['', 'http://example.com/link', 'http://example.com/link'],
            'absolute link with scheme' => ['http://example.com/base', 'https://other.com/link', 'https://other.com/link'],

            // Fragment handling
            'fragment only' => ['http://example.com/path/file', '#section', 'http://example.com/path/file#section'],
            'fragment replacing existing fragment' => ['http://example.com/path/file#old', '#new', 'http://example.com/path/file#new'],

            // Query handling
            'query only' => ['http://example.com/path/file', '?q=1', 'http://example.com/path/file?q=1'],
            'query replacing existing query' => ['http://example.com/path/file?old=1', '?new=2', 'http://example.com/path/file?new=2'],

            // Protocol-relative links
            'protocol relative link' => ['http://example.com/path', '//other.com/file', 'http://other.com/file'],

            // Absolute path link
            'absolute path' => ['http://example.com/path/file', '/new/path', 'http://example.com/new/path'],
            'absolute path with query and fragment' => ['http://example.com/path/file', '/new/path?q=1#sec', 'http://example.com/new/path?q=1#sec'],

            // Relative path link
            'relative path without trailing slash on base' => ['http://example.com/dir/file', 'newfile', 'http://example.com/dir/newfile'],
            'relative path with trailing slash on base' => ['http://example.com/dir/', 'newfile', 'http://example.com/dir/newfile'],
            'relative path on root base' => ['http://example.com', 'newfile', 'http://example.com/newfile'],

            // Path normalization
            'dot directory' => ['http://example.com/dir/', './newfile', 'http://example.com/dir/newfile'],
            'dot dot directory' => ['http://example.com/dir/subdir/', '../newfile', 'http://example.com/dir/newfile'],
            'multiple dot dot directories' => ['http://example.com/dir/sub1/sub2/', '../../newfile', 'http://example.com/dir/newfile'],
            'dot dot beyond root' => ['http://example.com/dir/', '../../newfile', 'http://example.com/newfile'],

            // Query preservation and merging
            'link with query and fragment' => ['http://example.com/path', 'file?q=1#sec', 'http://example.com/file?q=1#sec'],
            'link with no query but base has query' => ['http://example.com/path?baseq=1', 'file', 'http://example.com/file?baseq=1'],
            'link with query overriding base query' => ['http://example.com/path?baseq=1', 'file?newq=2', 'http://example.com/file?newq=2'],

            // Specific parsing edge cases
            'base with port' => ['http://example.com:8080/path', 'file', 'http://example.com:8080/file'],
            'base with user info' => ['http://user:pass@example.com/path', 'file', 'http://user:pass@example.com/file'],
            'base with user only' => ['http://user@example.com/path', 'file', 'http://user@example.com/file'],
        ];
    }

    public function testInvalidBaseUrlThrowsLogicException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Invalid base URL');

        UrlUtilities::urlJoin('///', 'file');
    }

    public function testInvalidBaseUrlWithoutSchemeOrHostThrowsLogicException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Invalid base URL');

        UrlUtilities::urlJoin('just-a-path', 'file');
    }

    #[DataProvider('provideNormalizePathSamples')]
    public function testNormalizePath(string $path, string $expected): void
    {
        $this->assertSame($expected, UrlUtilities::normalizePath($path));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function provideNormalizePathSamples(): array
    {
        return [
            'normal path' => ['/a/b/c', '/a/b/c'],
            'trailing slash' => ['/a/b/c/', '/a/b/c/'],
            'no leading slash' => ['a/b/c', 'a/b/c'],
            'with dot' => ['/a/./b/c', '/a/b/c'],
            'with dot dot' => ['/a/b/../c', '/a/c'],
            'multiple dot dot' => ['/a/b/c/../../d', '/a/d'],
            'dot dot at root' => ['/../a', '/a'],
            'complex' => ['/a/./b/../c/d/../e/', '/a/c/e/'],
            'empty path' => ['', ''],
            'only dots' => ['/./', '/'],
            'double slashes' => ['/a//b', '/a/b'],
        ];
    }
}
