#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__.'/../../vendor/autoload.php';

if (!isset($_SERVER['argv']) || !is_array($_SERVER['argv'])) {
    fwrite(\STDERR, "This script must be run from the CLI.\n");
    exit(1);
}

$arguments = array_slice($_SERVER['argv'], 1);

$source = $arguments[0];
if (!is_file($source)) {
    fwrite(\STDERR, sprintf("File not found: %s\n", $source));
    exit(1);
}
$html = (string) file_get_contents($source);

$config = new Ineersa\Html2text\Config();
$html2Markdown = new Ineersa\Html2text\HTML2Markdown($config);
$markdown = $html2Markdown($html);

if (!str_ends_with($markdown, "\n")) {
    $markdown .= \PHP_EOL;
}

$testFile = __DIR__.'/../markdown_test';
file_put_contents($testFile, $markdown);

$sourceMd = str_replace('.html', '.md', $source);

$diff = shell_exec(sprintf('diff --unified --color=always -p "%s" "%s"', $sourceMd, $testFile));
if ($diff) {
    fwrite(\STDOUT, $diff);
}
