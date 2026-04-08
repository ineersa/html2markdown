# Go Fixture Import Mismatch Report (Phase 1)

Date: 2026-03-16

## Scope

- Fixture suite: `Third Party Fixtures Suite` (Go only)
- Upstream dataset: `JohannesKaufmann/html-to-markdown` GoldenFiles snapshot
- Imported groups:
  - `plugin/commonmark/testdata/GoldenFiles`
  - `plugin/table/testdata/GoldenFiles`
  - `plugin/strikethrough/testdata/GoldenFiles`

## Execution

- Command run: `vendor/bin/phpunit --testsuite "Third Party Fixtures Suite"`
- Result: suite executed successfully with 15 tests total (14 fixture cases + 1 root-directory smoke test).
- Observation: all 14 fixture cases currently report as risky in PHPUnit because style-only mismatches are intentionally allowed in phase 1 and return before a hard assertion.

## Bucket Summary Counts

| Bucket | Count |
|---|---:|
| `whitespace` | 0 |
| `list_shape` | 0 |
| `emphasis_style` | 0 |
| `autolink_policy` | 0 |
| `escaping` | 0 |
| `table_format` | 0 |
| `entity_handling` | 0 |
| `parser_bug` | 0 |
| `unclassified` | 14 |

## Per-Fixture Mismatch Listing

### `unclassified` (14)

All current mismatches are tagged `style_only: true` in divergence metadata.

- `johanneskaufmann-html-to-markdown/commonmark/blockquote`
- `johanneskaufmann-html-to-markdown/commonmark/bold`
- `johanneskaufmann-html-to-markdown/commonmark/code`
- `johanneskaufmann-html-to-markdown/commonmark/heading`
- `johanneskaufmann-html-to-markdown/commonmark/image`
- `johanneskaufmann-html-to-markdown/commonmark/link`
- `johanneskaufmann-html-to-markdown/commonmark/list`
- `johanneskaufmann-html-to-markdown/commonmark/metadata`
- `johanneskaufmann-html-to-markdown/strikethrough/strikethrough`
- `johanneskaufmann-html-to-markdown/table/basics`
- `johanneskaufmann-html-to-markdown/table/col_row_span`
- `johanneskaufmann-html-to-markdown/table/contents`
- `johanneskaufmann-html-to-markdown/table/email`
- `johanneskaufmann-html-to-markdown/table/parents`

## Parser-Bug Candidate Section

- Current `parser_bug` candidates: none.
- No fixture is presently bucketed as `parser_bug`.

## Style-Only vs Likely Converter Bugs

- Style-only expected diffs: 14
  - All known mismatches in this first-pass import are metadata-marked style-only and intentionally non-blocking.
- Likely parser/conversion bugs: 0
  - No non-style mismatch remains in this report snapshot.

## Notes for Follow-Up

- Next parity pass should re-bucket each `unclassified` style-only fixture into the most specific bucket when policy is clear.
- If a mismatch is reclassified as non-style (`style_only: false`), it should fail the suite and be tracked as parity work.
