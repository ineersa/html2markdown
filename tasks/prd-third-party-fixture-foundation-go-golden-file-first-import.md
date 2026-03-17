# PRD: Third-Party Fixture Foundation + Go Golden File First Import

## Overview
Build the initial third-party fixture testing foundation and complete the first import from Go GoldenFiles so this PHP library can be benchmarked against mature HTML-to-Markdown implementations. This phase is strictly Go-first and does not import or scaffold active non-Go fixture suites yet.

## Goals
- Establish a stable third-party fixture architecture for Go fixture imports.
- Import all selected Go GoldenFile fixture pairs from upstream.
- Compare outputs with deterministic normalization and required mismatch bucketing.
- Preserve converter stability by tracking style-only differences as metadata.
- Save a first-pass Go mismatch report as a markdown artifact in `plans/`.

## Quality Gates

These commands must pass for every user story:
- `composer run cs-fix`
- `composer run tests`
- `vendor/bin/phpunit --testsuite "Rust Fixtures Suite"`
- `vendor/bin/phpunit --testsuite "PHP Fixtures Suite"`
- `vendor/bin/phpunit --testsuite "Utils Suite"`
- `vendor/bin/phpunit --testsuite "Third Party Fixtures Suite"`

## User Stories

### US-001: Create Go-first third-party fixture foundation layout
**Description:** As a maintainer, I want a predictable third-party fixture directory and metadata layout so imports are reproducible and auditable.

**Acceptance Criteria:**
- [ ] Add Go root directory under `tests/files/thirdPartyFixtures/go/`.
- [ ] Add `tests/files/thirdPartyFixtures/THIRD_PARTY_FIXTURES.md` with attribution fields: upstream repo URL, resolved commit SHA, source paths, license, import date, transformations.
- [ ] Add metadata support file for divergence bucketing (JSON map keyed by fixture id/path).
- [ ] Document that non-Go library directories (`dotnet`, `js`, `ruby`, `java`) are intentionally deferred to phase 2.

### US-002: Initialize Go fixture suite scaffolding
**Description:** As a maintainer, I want Go-focused third-party fixture suite scaffolding so failures are isolated and actionable in this phase.

**Acceptance Criteria:**
- [ ] Add PHPUnit suite/test scaffolding for third-party Go fixtures.
- [ ] Go suite resolves `.html` input fixtures to matching `.md` expected files.
- [ ] Test naming/output makes source library and fixture id clear in failures.
- [ ] Non-Go suites are not added in this phase and their absence does not break existing test suite execution.

### US-003: Implement deterministic normalization + required mismatch bucketing
**Description:** As a maintainer, I want deterministic comparison and categorized diffs so parity work is actionable.

**Acceptance Criteria:**
- [ ] Normalize line endings to LF before assertion.
- [ ] Trim trailing whitespace before assertion.
- [ ] Apply bounded repeated-blank-line normalization policy.
- [ ] Support mismatch buckets: `whitespace`, `list_shape`, `emphasis_style`, `autolink_policy`, `escaping`, `table_format`, `entity_handling`, `parser_bug`, and temporary `unclassified`.
- [ ] Every mismatch is assigned exactly one bucket.
- [ ] Style-only mismatches can be marked in metadata without immediate converter behavior changes.

### US-004: Import all Go GoldenFiles from upstream main snapshot
**Description:** As a maintainer, I want all selected Go GoldenFiles imported so we can benchmark against a high-value corpus immediately.

**Acceptance Criteria:**
- [ ] Import all fixture pairs from:
  - `plugin/commonmark/testdata/GoldenFiles`
  - `plugin/table/testdata/GoldenFiles`
  - `plugin/strikethrough/testdata/GoldenFiles`
- [ ] Record resolved upstream commit SHA used for import in `tests/files/thirdPartyFixtures/THIRD_PARTY_FIXTURES.md`.
- [ ] Use deterministic local naming for imported fixtures and keep `.html`/`.md` pair consistency.
- [ ] Every imported `.html` has a matching `.md` (no orphans).
- [ ] Preserve upstream fixture filename/path mapping in metadata for every imported fixture (local file -> upstream original path).

### US-005: Generate and save first Go mismatch report
**Description:** As a maintainer, I want an initial categorized report saved in-repo so follow-up parity work is prioritized and traceable.

**Acceptance Criteria:**
- [ ] Execute third-party Go fixture suite after import.
- [ ] Generate mismatch report grouped by defined buckets.
- [ ] Report includes bucket summary counts.
- [ ] Report includes per-fixture mismatch listing.
- [ ] Report includes a dedicated parser-bug candidate section.
- [ ] Distinguish style-only expected diffs from likely parser/conversion bugs.
- [ ] Save report as markdown under `plans/` (e.g., `plans/go_fixture_import_mismatch_report.md`).
- [ ] Report path is referenced in attribution or phase notes for future updates.

## Functional Requirements
- FR-1: Store third-party Go fixtures under `tests/files/thirdPartyFixtures/go/`.
- FR-2: Enforce pair-based fixture execution (`.html` input + `.md` expected output).
- FR-3: Keep this phase Go-only; defer non-Go directory/suite creation to phase 2.
- FR-4: Apply explicit normalization rules before assertion.
- FR-5: Support per-fixture divergence metadata using approved mismatch buckets, including temporary `unclassified`.
- FR-6: Require exactly one bucket assignment per mismatch.
- FR-7: Import Go fixtures from upstream `main` snapshot and record resolved commit SHA in attribution.
- FR-8: Maintain attribution and licensing records for imported fixture groups.
- FR-9: Preserve fixture-level mapping from local filenames to upstream source paths.
- FR-10: Fail clearly on missing pairs or malformed metadata entries.
- FR-11: Persist first Go mismatch report in `plans/` as markdown with required structure.
- FR-12: Do not regress existing PHP, Rust, and Utils suites.

## Non-Goals (Out of Scope)
- Importing .NET, JS, Ruby, or Java fixture content.
- Creating non-Go fixture directories or active non-Go suite scaffolding in this phase.
- Changing converter core logic to force immediate parity.
- Automating upstream download/sync tooling.
- Importing additional Go datasets outside the three GoldenFiles groups.
- Large refactors unrelated to fixture foundation and Go first import.

## Technical Considerations
- Reuse existing fixture test patterns from current suites for consistency.
- Keep normalization explicit and minimal to avoid masking real behavior differences.
- Record commit SHA as the required source pin for this phase.
- Keep mismatch report markdown human-friendly but structured for later automation.

## Success Metrics
- Go-first third-party fixture foundation and attribution files are present and runnable.
- All Go GoldenFile pairs from the three selected groups are imported.
- Each mismatch is bucketed with exactly one category (temporary `unclassified` allowed).
- Required local->upstream mapping exists for all imported fixtures.
- Quality gates pass.
- Initial categorized Go mismatch report is committed under `plans/`.

## Open Questions
- None for this phase.