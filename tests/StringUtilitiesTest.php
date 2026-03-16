<?php

declare(strict_types=1);

namespace Tests;

use Ineersa\Html2text\Utilities\StringUtilities;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StringUtilitiesTest extends TestCase
{
    #[DataProvider('provideWordwrapSamples')]
    public function testWordwrap(
        string $string,
        int $width,
        string $break,
        bool $cut,
        string $expected,
    ): void {
        $this->assertSame($expected, StringUtilities::wordwrap($string, $width, $break, $cut));
    }

    /**
     * @return array<string, array{0: string, 1: int, 2: string, 3: bool, 4: string}>
     */
    public static function provideWordwrapSamples(): array
    {
        return [
            'width <= 0 returns original string' => [
                'hello world', 0, "\n", false, 'hello world',
            ],
            'empty string returns empty' => [
                '', 75, "\n", false, '',
            ],
            'string shorter than width is not wrapped' => [
                'hello world', 75, "\n", false, 'hello world',
            ],
            'standard space wrap' => [
                'hello world how are you', 10, "\n", false, "hello\nworld how\nare you",
            ],
            'custom break string' => [
                'hello world how are you', 10, '<br>', false, 'hello<br>world how<br>are you',
            ],
            'cut is false does not split long words' => [
                'supercalifragilisticexpialidocious word', 10, "\n", false, "supercalifragilisticexpialidocious\nword",
            ],
            'cut is true splits long words' => [
                'supercalifragilisticexpialidocious word', 10, "\n", true, "supercalif\nragilistic\nexpialidoc\nious word",
            ],
            'hyphen aware break' => [
                'well-known phenomenon', 12, "\n", false, "well-known\nphenomenon",
            ],
            'hyphen not broken if cut is true' => [
                'well-known phenomenon', 6, "\n", true, "well-k\nnown\nphenom\nenon",
            ],
            'hyphen without previous or next char' => [
                '-well known-', 10, "\n", false, "-well\nknown-",
            ],
            'multiple hyphens' => [
                'a-b-c-d-e-f', 4, "\n", false, "a-b-\nc-d-\ne-f",
            ],
        ];
    }
}
