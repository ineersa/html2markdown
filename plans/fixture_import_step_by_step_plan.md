# Fixture Import Plan (Step by Step)

Goal: import best third-party HTML->Markdown fixtures into this repo without destabilizing existing behavior.

## Scope

- Keep existing suites green (`PHP Fixtures Suite`, `Rust Fixtures Suite`, `Utils Suite`).
- Add third-party fixtures in phases, with source attribution and predictable normalization.
- Track intentional style differences separately from true conversion bugs.

## Proposed Target Layout

Use dedicated directories under `tests/files`:

- `tests/files/thirdPartyFixtures/go/`
- `tests/files/thirdPartyFixtures/dotnet/`
- `tests/files/thirdPartyFixtures/js/`
- `tests/files/thirdPartyFixtures/ruby/`
- `tests/files/thirdPartyFixtures/java/`
- `tests/files/thirdPartyFixtures/THIRD_PARTY_FIXTURES.md` (source + license + commit SHA)

Use normalized pair naming:

- `<source>_<group>_<index>_<slug>.html`
- `<source>_<group>_<index>_<slug>.md`

## Phase 0: Foundation

1. Add third-party fixture root directories and attribution file.
2. Add a dedicated PHPUnit suite file, for example `tests/ThirdPartyFixturesTest.php`.
3. Reuse the Rust suite style: load only `tests/files/thirdPartyFixtures/**/*.html` and assert against matching `.md`.
4. Add metadata support file (JSON map) for known divergence buckets.

Done criteria:
- New suite can run with zero fixtures and pass.

## Phase 1: Go Golden Files (First Import)

Source priority:
- `JohannesKaufmann/html-to-markdown/plugin/commonmark/testdata/GoldenFiles`
- `JohannesKaufmann/html-to-markdown/plugin/table/testdata/GoldenFiles`
- `JohannesKaufmann/html-to-markdown/plugin/strikethrough/testdata/GoldenFiles`

Steps:
1. Copy `*.in.html` as `.html` and matching `*.out.md` as `.md`.
2. Prefix with `go_` and group names (`commonmark`, `table`, `strikethrough`).
3. Run only third-party suite and record failures by category.
4. Mark expected style-only diffs in metadata instead of immediately changing core behavior.

Done criteria:
- At least 50 high-value Go fixture pairs imported.
- Failure report grouped by category is generated.

## Phase 2: .NET CommonMark + Verified Regressions

Source priority:
- `reversemarkdown-net/src/ReverseMarkdown.Test/TestData/commonmark.json`
- Selected `*.verified.md`/`*.verified.txt` cases with real bug value.

Steps:
1. Convert JSON/snapshot fixtures into HTML/MD pairs in `dotnet/`.
2. Skip or tag cases that rely on framework-specific formatting assumptions.
3. Run suite and categorize mismatches.

Done criteria:
- CommonMark subset imported and runnable.
- High-noise snapshot cases clearly tagged.

## Phase 3: Turndown (JS) Conversion Corpus

Source priority:
- `mixmark-io/turndown/test/`

Steps:
1. Extract pure conversion cases first (avoid plugin/rule override tests initially).
2. Convert into pair fixtures under `js/`.
3. Compare output and tag differences in link, list, and escaping behavior.

Done criteria:
- Core Turndown conversion subset imported.
- No regression in existing PHP and Rust suites.

## Phase 4: Ruby Real-World Assets

Source priority:
- `xijo/reverse_markdown/spec/assets`

Steps:
1. Import representative assets (start with short-medium documents).
2. Build expected Markdown from upstream tests where possible.
3. Keep large documents in a separate optional suite if runtime grows.

Done criteria:
- Real-world HTML shapes covered (nested sections, docs-like content, media-heavy blocks).

## Phase 5: Flexmark Long-Tail Specs

Source priority:
- `flexmark-java/flexmark-html2md-converter/src/test/resources`

Steps:
1. Select focused subsets (lists, code blocks, links, tables) before full import.
2. Convert spec formats into pair fixtures.
3. Add a slow suite label if fixture volume becomes large.

Done criteria:
- At least one curated subset imported for each major feature area.

## Normalization Rules (Apply Before Assertion)

1. Normalize line endings to LF.
2. Trim trailing whitespace.
3. Normalize repeated blank lines (bounded policy).
4. Keep entity decoding policy explicit (do not silently over-normalize).
5. Keep Markdown style toggles configurable per suite.

## Mismatch Buckets To Track

- `whitespace`
- `list_shape`
- `emphasis_style`
- `autolink_policy`
- `escaping`
- `table_format`
- `entity_handling`
- `parser_bug`

## Quality Gates Per Phase

Run on every phase:

1. `composer run cs-fix`
2. `composer run tests`
3. `vendor/bin/phpunit --testsuite "Rust Fixtures Suite"`
4. `vendor/bin/phpunit --testsuite "PHP Fixtures Suite"`
5. `vendor/bin/phpunit --testsuite "Utils Suite"`
6. `vendor/bin/phpunit --testsuite "Third Party Fixtures Suite"` (new)

## Attribution Checklist

For each imported fixture group, add to `THIRD_PARTY_FIXTURES.md`:

1. Upstream repository URL
2. Upstream commit SHA or release tag
3. Source file path(s)
4. License
5. Import date
6. Any transformations applied

## Suggested Milestones

- Milestone A: Foundation + Phase 1 complete
- Milestone B: Phase 2 + Phase 3 complete
- Milestone C: Phase 4 + curated Phase 5 complete
- Milestone D: Stabilization pass (reduce expected diffs and convert to true pass cases)
