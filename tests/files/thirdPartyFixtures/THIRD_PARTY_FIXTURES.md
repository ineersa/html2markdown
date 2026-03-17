# Third-Party Fixtures Attribution

This directory tracks imported third-party fixture datasets used for parity benchmarking.

## Scope

- Active in phase 1: `go/`
- Intentionally deferred to phase 2: `dotnet/`, `js/`, `ruby/`, `java/`

## Go GoldenFiles Import Record

- Upstream repository URL: `https://github.com/JohannesKaufmann/html-to-markdown`
- Resolved commit SHA: `3006818b20a61b0a36eb86321aef57d3d017c27e`
- Source paths:
  - `plugin/commonmark/testdata/GoldenFiles`
  - `plugin/table/testdata/GoldenFiles`
  - `plugin/strikethrough/testdata/GoldenFiles`
- License: `MIT`
- Import date: `2026-03-16`
- Transformations:
  - Preserve fixture content semantics during copy.
  - Keep deterministic local fixture naming.
  - Preserve local `.html` to `.md` pair consistency.
  - Record local-to-upstream path mapping in metadata.

## Metadata Files

- Divergence bucket map: `divergence_buckets.json`
  - JSON object keyed by local fixture id/path.
  - Each mismatch entry must map to exactly one bucket in phase 1.
- Go upstream path map: `go/upstream_path_map.json`
  - JSON object keyed by local file path under `tests/files/thirdPartyFixtures/`.
  - Every imported local fixture file (`.html` and `.md`) maps to its upstream source path.
- Phase 1 Go mismatch report: `plans/go_fixture_import_mismatch_report.md`
  - Initial categorized Go-first mismatch snapshot for import baseline and parity prioritization.
