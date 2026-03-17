# Ralph Progress Log

This file tracks progress across iterations. Agents update this file
after each iteration and it's included in prompts for context.

## Codebase Patterns (Study These First)

*Add reusable patterns discovered during development here.*

- Third-party fixture governance is centralized under `tests/files/thirdPartyFixtures/` with attribution in markdown and machine-readable mismatch metadata in JSON.
- New fixture suites can remain stable before imports by combining a root-directory smoke test with a data-provider-driven pair matcher (`.html` -> `.md`).
- Third-party parity assertions should normalize both expected and actual output through a shared pipeline (LF endings, trailing-whitespace trim, bounded blank-line collapse) before comparing and bucket unresolved mismatches via JSON metadata.
- For third-party imports, keep a per-file source map (`local fixture path -> upstream path`) so fixture audits stay deterministic even after local renames from upstream suffix conventions (for example, `.in.html`/`.out.md` to `.html`/`.md`).
- First-pass parity reporting is easiest to keep deterministic by reusing the same normalization and divergence metadata rules as the fixture suite, then splitting report output into style-only expected diffs versus likely converter bugs.

---

## 2026-03-16 - US-003
- What was implemented
  - Added deterministic comparison normalization to the Go third-party fixture suite: force LF line endings, trim trailing whitespace per line, and collapse repeated blank lines to a bounded max before final assertion.
  - Added mismatch divergence metadata loading/validation from `tests/files/thirdPartyFixtures/divergence_buckets.json` with strict single-bucket enforcement and allowed bucket validation (`whitespace`, `list_shape`, `emphasis_style`, `autolink_policy`, `escaping`, `table_format`, `entity_handling`, `parser_bug`, `unclassified`).
  - Updated mismatch handling so every non-equal fixture must resolve to exactly one bucket entry; mismatches tagged as `style_only`/`styleOnly` are tracked and allowed to pass without forcing converter behavior changes.
- Files changed
  - `tests/ThirdPartyGoFixturesTest.php`
  - `.ralph-tui/progress.md`
- **Learnings:**
  - Patterns discovered
    - Enforcing bucket metadata only on actual mismatches keeps imported fixture suites strict while avoiding premature bookkeeping for cases that already pass.
  - Gotchas encountered
    - Cross-platform fixture content can contain both CRLF and lone CR line endings, so deterministic normalization must replace both forms rather than only CRLF.
---

## 2026-03-16 - US-002
- What was implemented
  - Added a dedicated PHPUnit suite entry named `Third Party Fixtures Suite` in `phpunit.dist.xml` that targets a new Go-only scaffold test file.
  - Created `tests/ThirdPartyGoFixturesTest.php` to discover Go fixture inputs under `tests/files/thirdPartyFixtures/go/`, resolve expected outputs by deterministic pair mapping (`.html` -> `.md`), and run conversion assertions.
  - Added fixture identifiers in data-provider keys and assertion messages as `go/<sourceLibrary>/<fixturePath>` so failure output clearly communicates both source scope and fixture id.
  - Added a root-directory smoke test so the suite remains executable even before fixture import is populated.
- Files changed
  - `phpunit.dist.xml`
  - `tests/ThirdPartyGoFixturesTest.php`
  - `.ralph-tui/progress.md`
- **Learnings:**
  - Patterns discovered
    - Recursive fixture discovery via SPL iterators is safer than glob for nested imports and keeps provider ordering deterministic when sorted.
  - Gotchas encountered
    - `HTML2Markdown` requires an explicit `Config` instance; test scaffolds cannot instantiate the converter without passing one.
---

## 2026-03-16 - US-001
- What was implemented
  - Created the Go-first third-party fixture root directory at `tests/files/thirdPartyFixtures/go/`.
  - Added attribution and import tracking scaffold at `tests/files/thirdPartyFixtures/THIRD_PARTY_FIXTURES.md` with required fields (upstream URL, commit SHA placeholder, source paths, license, import date, transformations).
  - Added divergence metadata support file `tests/files/thirdPartyFixtures/divergence_buckets.json` as a JSON map container keyed by fixture id/path.
  - Documented that non-Go directories (`dotnet`, `js`, `ruby`, `java`) are intentionally deferred to phase 2.
- Files changed
  - `tests/files/thirdPartyFixtures/go/.gitkeep`
  - `tests/files/thirdPartyFixtures/THIRD_PARTY_FIXTURES.md`
  - `tests/files/thirdPartyFixtures/divergence_buckets.json`
  - `.ralph-tui/progress.md`
- **Learnings:**
  - Patterns discovered
    - Keeping human-readable attribution and machine-readable divergence metadata side by side under one root makes fixture imports auditable and reproducible.
  - Gotchas encountered
    - Empty directories are not tracked by git, so a placeholder file is required to persist the new Go root directory.
---

## 2026-03-16 - US-004
- What was implemented
  - Imported all GoldenFile fixture pairs from `JohannesKaufmann/html-to-markdown` under `plugin/commonmark/testdata/GoldenFiles`, `plugin/table/testdata/GoldenFiles`, and `plugin/strikethrough/testdata/GoldenFiles`.
  - Added deterministic local fixture layout under `tests/files/thirdPartyFixtures/go/johanneskaufmann-html-to-markdown/<group>/` and normalized upstream naming from `*.in.html` / `*.out.md` to local `*.html` / `*.md` pairs.
  - Added full local-to-upstream mapping metadata in `tests/files/thirdPartyFixtures/go/upstream_path_map.json` covering every imported fixture file.
  - Updated attribution in `tests/files/thirdPartyFixtures/THIRD_PARTY_FIXTURES.md` with the resolved upstream `main` SHA and import date, plus a reference to the new upstream path map metadata file.
  - Seeded divergence metadata keys for all imported Go fixtures in `tests/files/thirdPartyFixtures/divergence_buckets.json` so imported mismatches are explicitly tracked during this phase.
- Files changed
  - `tests/files/thirdPartyFixtures/go/johanneskaufmann-html-to-markdown/commonmark/*.html`
  - `tests/files/thirdPartyFixtures/go/johanneskaufmann-html-to-markdown/commonmark/*.md`
  - `tests/files/thirdPartyFixtures/go/johanneskaufmann-html-to-markdown/table/*.html`
  - `tests/files/thirdPartyFixtures/go/johanneskaufmann-html-to-markdown/table/*.md`
  - `tests/files/thirdPartyFixtures/go/johanneskaufmann-html-to-markdown/strikethrough/*.html`
  - `tests/files/thirdPartyFixtures/go/johanneskaufmann-html-to-markdown/strikethrough/*.md`
  - `tests/files/thirdPartyFixtures/go/upstream_path_map.json`
  - `tests/files/thirdPartyFixtures/THIRD_PARTY_FIXTURES.md`
  - `tests/files/thirdPartyFixtures/divergence_buckets.json`
  - `.ralph-tui/progress.md`
- **Learnings:**
  - Patterns discovered
    - Converting upstream suffix-based pairs (`name.in.html` + `name.out.md`) into local stem-based pairs (`name.html` + `name.md`) keeps test resolution simple while preserving traceability through a dedicated source map.
  - Gotchas encountered
    - The current third-party fixture id formatting duplicates the source library segment in provider output (`go/<library>/<library>/...`), so divergence metadata keys are most stable when keyed by fixture path.
---

## 2026-03-16 - US-005
- What was implemented
  - Executed `vendor/bin/phpunit --testsuite "Third Party Fixtures Suite"` after Go fixture import and captured the initial mismatch baseline.
  - Generated and saved first categorized mismatch report at `plans/go_fixture_import_mismatch_report.md` including bucket summary counts, grouped per-fixture mismatch listing, parser-bug candidate section, and explicit style-only vs likely converter bug split.
  - Added a forward reference to the report in third-party attribution metadata for future parity updates.
- Files changed
  - `plans/go_fixture_import_mismatch_report.md`
  - `tests/files/thirdPartyFixtures/THIRD_PARTY_FIXTURES.md`
  - `.ralph-tui/progress.md`
- **Learnings:**
  - Patterns discovered
    - Reusing suite normalization and divergence metadata semantics in report generation keeps parity triage aligned with test outcomes and avoids drift.
  - Gotchas encountered
    - Current style-only mismatch handling causes PHPUnit risky tests (no assertions) for each diverged fixture, so report generation must treat risky output as expected phase-1 signal rather than execution failure.
---
