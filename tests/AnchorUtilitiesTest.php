<?php

declare(strict_types=1);

namespace Tests;

use Ineersa\Html2text\Utilities\AnchorUtilities;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AnchorUtilitiesTest extends TestCase
{
    /**
     * @param list<int>                             $expectedStart
     * @param list<int>                             $expectedClose
     * @param list<array{text: string, depth: int}> $expectedText
     */
    #[DataProvider('provideComputeSamples')]
    public function testCompute(string $html, array $expectedStart, array $expectedClose, array $expectedText): void
    {
        $result = AnchorUtilities::compute($html);

        $this->assertSame([$expectedStart, $expectedClose, $expectedText], $result);
    }

    /**
     * @return array<string, array{0: string, 1: list<int>, 2: list<int>, 3: list<array{text: string, depth: int}>}>
     */
    public static function provideComputeSamples(): array
    {
        return [
            'plain text' => [
                'Just some plain text.',
                [],
                [],
                [
                    ['text' => 'Just some plain text.', 'depth' => 0],
                ],
            ],
            'single anchor' => [
                'Text <a href="#">link</a> text.',
                [1],
                [1],
                [
                    ['text' => 'Text ', 'depth' => 0],
                    ['text' => 'link', 'depth' => 1],
                    ['text' => ' text.', 'depth' => 0],
                ],
            ],
            'nested anchors' => [
                '<div><a href="1">link1 <a href="2">link2</a> link1 again</a></div>',
                [1, 2],
                [2, 1],
                [
                    ['text' => 'link1 ', 'depth' => 1],
                    ['text' => 'link2', 'depth' => 2],
                    ['text' => ' link1 again', 'depth' => 1],
                ],
            ],
            'sibling anchors' => [
                '<a href="1">first</a> and <a href="2">second</a>',
                [1, 1],
                [1, 1],
                [
                    ['text' => 'first', 'depth' => 1],
                    ['text' => ' and ', 'depth' => 0],
                    ['text' => 'second', 'depth' => 1],
                ],
            ],
            'anchor with attributes and uppercase' => [
                '<A HREF="test" CLASS="link">caps</A>',
                [1],
                [1],
                [
                    ['text' => 'caps', 'depth' => 1],
                ],
            ],
            'self-closing anchor' => [
                'Before <a href="#" /> After',
                [1],
                [1],
                [
                    ['text' => 'Before ', 'depth' => 0],
                    ['text' => ' After', 'depth' => 0],
                ],
            ],
            'unclosed anchor' => [
                'Text <a href="#">link',
                [1],
                [],
                [
                    ['text' => 'Text ', 'depth' => 0],
                    ['text' => 'link', 'depth' => 1],
                ],
            ],
            'extra closing anchor' => [
                'Text </a> link',
                [],
                [],
                [
                    ['text' => 'Text ', 'depth' => 0],
                    ['text' => ' link', 'depth' => 0],
                ],
            ],
            'mixed tags' => [
                '<html><body><h1>Title</h1><p>A <a href="#">link</a> here.</p></body></html>',
                [1],
                [1],
                [
                    ['text' => 'Title', 'depth' => 0],
                    ['text' => 'A ', 'depth' => 0],
                    ['text' => 'link', 'depth' => 1],
                    ['text' => ' here.', 'depth' => 0],
                ],
            ],
            'anchor tag starting with spaces' => [
                '< a href="x">space</ a >',
                [1],
                [1],
                [
                    ['text' => 'space', 'depth' => 1],
                ],
            ],
            'non-anchor tag closing with />' => [
                '<br />text',
                [],
                [],
                [
                    ['text' => 'text', 'depth' => 0],
                ],
            ],
            'empty token simulation' => [
                '<a></a>',
                [1],
                [1],
                [],
            ],
            'tag-like text but not tags' => [
                'a < b and c > d',
                [],
                [],
                [
                    ['text' => 'a ', 'depth' => 0],
                    ['text' => ' d', 'depth' => 0],
                ],
            ],
        ];
    }
}
