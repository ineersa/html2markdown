# PHP Html2Text

[![CI](https://github.com/ineersa/html2markdown/actions/workflows/main.yml/badge.svg?branch=main)](https://github.com/ineersa/html2markdown/actions/workflows/main.yml)
[![codecov](https://codecov.io/gh/ineersa/html2markdown/branch/main/graph/badge.svg)](https://codecov.io/gh/ineersa/html2markdown)


`html2text` converts a page of HTML into clean, easy-to-read plain ASCII text. Better yet, that ASCII also happens to be valid Markdown (a text-to-HTML format).

It is a PHP port of [Alir3z4/html2text](https://github.com/Alir3z4/html2text) with few fixes and updates.

Functionality parity is checked via the test suite, which contains all the test cases from the original and more.
Most of the code was translated with AI with a lot of refactoring and fixes.


## How to install/requirements

Project is using new DOM extension for better HTML parser and requires `ext-libxml`. 
PHP version required - 8.4+ 

To install run composer command:
```bash
composer require ineersa/html2text
```

## Usage

Basic usage:

```php
$html = (string) file_get_contents($source);

$config = new Ineersa\Html2text\Config();
$html2Markdown = new Ineersa\Html2text\HTML2Markdown($config);
$markdown = $html2Markdown($html);
```

Config options are compatible with Python library:

```php
final readonly class Config
{
    public function __construct(
        /** Use Unicode characters instead of ASCII fallbacks. */
        public bool $unicodeSnob = false,
        /** Escape all special characters even if output is less readable. */
        public bool $escapeSnob = false,
        /** Append footnote links immediately after each paragraph. */
        public bool $linksEachParagraph = false,
        /** Wrap long lines at the configured column (0 disables wrapping). */
        public int $bodyWidth = 78,
        /** Skip internal anchors like href="#local-anchor". */
        public bool $skipInternalLinks = true,
        /** Render links using inline Markdown syntax. */
        public bool $inlineLinks = true,
        /** Surround links with angle brackets to prevent wraps. */
        public bool $protectLinks = false,
        /** Allow links to wrap across lines. */
        public bool $wrapLinks = true,
        /** Wrap list items at the configured body width. */
        public bool $wrapListItems = false,
        /** Wrap table output text. */
        public bool $wrapTables = false,
        /** Is Google Doc */
        public bool $googleDoc = false,
        /** Callback to apply at tag processing, $callback($this, $tag, $attrs, $start), should return true to break processing, false otherwise */
        public ?\Closure $tagCallback = null,
        /** Pixels Google uses to indent nested lists. */
        public int $googleListIndent = 36,
        /**
         * Values that indicate bold text in inline styles.
         *
         * @var string[]
         */
        public array $boldTextStyleValues = ['bold', '700', '800', '900'],
        /** Ignore anchor tags entirely. */
        public bool $ignoreAnchors = false,
        /** Ignore mailto links during conversion. */
        public bool $ignoreMailtoLinks = false,
        /** Drop all image tags from the output. */
        public bool $ignoreImages = false,
        /** Keep image tags rendered as raw HTML. */
        public bool $imagesAsHtml = false,
        /** Replace images with their alt text. */
        public bool $imagesToAlt = false,
        /** Include width/height attributes when preserving images. */
        public bool $imagesWithSize = false,
        /** Ignore text emphasis such as italics and bold. */
        public bool $ignoreEmphasis = false,
        /** Wrap inline code with custom markers. */
        public bool $markCode = false,
        /** Use backquotes instead of indentation for code blocks. */
        public bool $backquoteCodeStyle = false,
        /** Fallback alt text when an image omits it. */
        public string $defaultImageAlt = '',
        /** Pad tables to align cell widths. */
        public bool $padTables = false,
        /** Convert absolute links with identical href/text to <href> style. */
        public bool $useAutomaticLinks = true,
        /** Render tables as HTML instead of Markdown. */
        public bool $bypassTables = false,
        /** Ignore table tags but retain row content. */
        public bool $ignoreTables = false,
        /** Emit a single line break after block elements (requires width 0). */
        public bool $singleLineBreak = false,
        /** Use as the opening quotation mark for <q> tags. */
        public string $openQuote = '"',
        /** Use as the closing quotation mark for <q> tags. */
        public string $closeQuote = '"',
        /** Include <sup> and <sub> tags in the output. */
        public bool $includeSupSub = false,
        /** baseUrl to join with URLs if needed */
        public string $baseUrl = '',
        /** Number of list nesting levels skipped when applying visual indentation. */
        public int $listIndentBaseLevel = 0,
        /** Add indentation before definition list descriptions (<dd>). */
        public bool $indentDefinitionDescriptions = true,
        /** Add a blank line after closing a definition description (<dd>). */
        public bool $blankLineAfterDefinitionDescription = false,
        /** Append an extra newline after closing a top-level list. */
        public bool $appendFinalListNewline = true,
        /** Append one extra raw newline after a top-level list closes. */
        public bool $appendRawNewlineAfterTopLevelList = false,
        /** Emphasis marks */
        public string $ulItemMark = '*',
        public string $emphasisMark = '_',
        public string $strongMark = '**',
        /** hide strikethrough emphasis */
        public bool $hideStrikethrough = false,
    ) {
    }
}
```

New parity-oriented options added for finer output control:

- `listIndentBaseLevel`: removes indentation from the first N list levels (useful when aligning with other converters that keep top-level lists flush-left).
- `indentDefinitionDescriptions`: toggles the `    ` prefix for `<dd>` entries.
- `blankLineAfterDefinitionDescription`: inserts a paragraph break after each `</dd>`.
- `appendFinalListNewline`: appends a newline when closing the outermost list.
- `appendRawNewlineAfterTopLevelList`: appends one more raw newline after a top-level list close.

## Rust parity

This project can be validated against the Rust implementation using imported HTML->Markdown fixture pairs.

- `PHP Fixtures Suite` checks the PHP port's original fixture set.
- `Rust Fixtures Suite` checks parity fixtures imported from the Rust repository.
- You can run only parity tests with: `vendor/bin/phpunit --testsuite "Rust Fixtures Suite"`.

<details>
<summary>Rust parity configuration example</summary>

```php
<?php

use Ineersa\Html2text\Config;
use Ineersa\Html2text\HTML2Markdown;

$config = new Config(
    baseUrl: '',
    bodyWidth: 0,
    skipInternalLinks: false,
    ulItemMark: '-',
    emphasisMark: '*',
    listIndentBaseLevel: 1,
    indentDefinitionDescriptions: false,
    blankLineAfterDefinitionDescription: true,
    appendFinalListNewline: false,
    appendRawNewlineAfterTopLevelList: true,
);

$converter = new HTML2Markdown($config);
```

</details>

<details>
<summary>Suggested LLM-oriented configuration</summary>

For LLM ingestion, a practical default is to minimize reflow noise, keep inline context, and preserve structural cues:

```php
<?php

use Ineersa\Html2text\Config;
use Ineersa\Html2text\HTML2Markdown;

$config = new Config(
    bodyWidth: 0,
    unicodeSnob: true,
    inlineLinks: true,
    skipInternalLinks: true,
    wrapLinks: false,
    wrapListItems: false,
    wrapTables: false,
    padTables: false,
    useAutomaticLinks: true,
    backquoteCodeStyle: true,
    imagesToAlt: true,
    ulItemMark: '-',
    emphasisMark: '*',
);

$converter = new HTML2Markdown($config);
```

Why this profile works well for LLM workflows:

- `bodyWidth: 0` avoids hard-wrapped lines that can split sentence context.
- `inlineLinks: true` keeps reference targets close to anchor text.
- `skipInternalLinks: true` reduces table-of-contents and in-page anchor noise.
- `backquoteCodeStyle: true` keeps code blocks explicit and model-friendly.
- `imagesToAlt: true` preserves useful image semantics without raw HTML clutter.

</details>

## Development

You can find information about the repository in [AGENTS.md](./AGENTS.md)

Composer has scripts section with commands to run all required tools:
```json
"scripts": {
    "cs-fix": "vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php",
    "phpstan": "vendor/bin/phpstan analyse -c phpstan.dist.neon",
    "tests": "vendor/bin/phpunit --colors=always --testdox",
    "coverage": "XDEBUG_MODE=coverage vendor/bin/phpunit --colors=always --testdox --coverage-text --coverage-html coverage/ --coverage-clover coverage/clover.xml",
    "tests-xdebug": "php -d xdebug.mode=debug -d xdebug.client_host=127.0.0.1 -d xdebug.client_port=9003 -d xdebug.start_with_request=yes vendor/bin/phpunit --colors=always --testdox"
  }
```

## License

This project is licensed under the [GNU General Public License v3.0 or later](LICENSE).

It is a PHP port of [Alir3z4/html2text](https://github.com/Alir3z4/html2text),  
which is licensed under the GPL as well.  
All credit goes to the original authors for their work.
