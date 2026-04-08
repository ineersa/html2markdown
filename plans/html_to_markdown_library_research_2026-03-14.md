# HTML to Markdown Library Research (2026-03-14)

This file saves the deep-research subagent findings about widely used HTML->Markdown libraries (excluding Python `html2text` and `kreuzberg-dev/html-to-markdown`).

## Executive Summary (Top 5 Sources To Mine Tests From)

1. `JohannesKaufmann/html-to-markdown` (Go)
   - Best immediate fixture source: clean golden pairs (`*.in.html` -> `*.out.md`) and plugin-scoped coverage.
2. `mysticmind/reversemarkdown-net` (.NET)
   - Strong regression fixture format (`*.verified.*`) plus `commonmark.json` corpus.
3. `mixmark-io/turndown` (JS)
   - Very high adoption and broad HTML conversion behavior coverage.
4. `vsch/flexmark-java` (Java)
   - Large spec resources and deep edge-case coverage.
5. `xijo/reverse_markdown` (Ruby)
   - Mature ecosystem usage and practical real-world HTML assets.

## Candidate Details

| Library | Ecosystem | Popularity/Activity Signals | Test/Fixture Sources | Fixture Shape | License |
|---|---|---|---|---|---|
| `mixmark-io/turndown` | JavaScript/TypeScript | ~10,910 stars, active, npm ~11,895,820 downloads/month, latest v7.2.2 | `test/` | HTML case corpus in test HTML + assertions | MIT |
| `crosstype/node-html-markdown` | JavaScript/TypeScript | ~254 stars, npm ~1,692,106 downloads/month, latest v2.0.0 | `test/` | Unit/integration style fixtures | MIT |
| `thephpleague/html-to-markdown` | PHP | ~1,873 stars, Packagist total ~28,103,235, monthly ~1,028,613 | `tests/` | Unit + conversion expectations | MIT |
| `xijo/reverse_markdown` | Ruby | ~665 stars, RubyGems total ~93,986,433, latest 3.0.2 | `spec/assets` | Real-world input assets with expected outputs in specs | WTFPL |
| `JohannesKaufmann/html-to-markdown` | Go | ~3,488 stars, latest v2.5.0, pkg.go.dev known importers: 60 | `plugin/commonmark/testdata/GoldenFiles`, `plugin/table/testdata/GoldenFiles`, `plugin/strikethrough/testdata/GoldenFiles`, `cli/html2markdown/cmd/testdata/TestExecute` | Golden files (`*.in.html`, `*.out.md`) | MIT |
| `mysticmind/reversemarkdown-net` | .NET/C# | ~372 stars, NuGet total ~4,277,133, latest 5.2.0 | `src/ReverseMarkdown.Test/TestData` | Snapshot/approval (`*.verified.md`, `*.verified.txt`) + `commonmark.json` | MIT |
| `vsch/flexmark-java` | Java/Kotlin | ~2,594 stars, Maven artifact has many releases (188 versions seen) | `flexmark-html2md-converter/src/test/resources` | Spec-style resources (`*_spec.md`) + converter fixtures | BSD-2-Clause |

## Best Fixture Paths To Import First

- Go (`JohannesKaufmann/html-to-markdown`)
  - `plugin/commonmark/testdata/GoldenFiles`
  - `plugin/table/testdata/GoldenFiles`
  - `plugin/strikethrough/testdata/GoldenFiles`
- .NET (`mysticmind/reversemarkdown-net`)
  - `src/ReverseMarkdown.Test/TestData/commonmark.json`
  - Selected `*.verified.md` and `*.verified.txt`
- JS (`mixmark-io/turndown`)
  - `test/` (especially conversion-focused cases)
- Ruby (`xijo/reverse_markdown`)
  - `spec/assets`
- Java (`vsch/flexmark-java`)
  - `flexmark-html2md-converter/src/test/resources`

## Recommended Ranking For Import Work

1. Go golden pairs (highest ROI, lowest transform effort)
2. .NET `commonmark.json` + selected verified snapshots
3. Turndown conversion corpus
4. Ruby real-world assets
5. Flexmark Java long-tail specs

## Expected Mismatch Categories During Import

- Whitespace (blank lines, trailing spaces, line endings)
- List shape (indent levels, ordered index style, tight vs loose)
- Emphasis style (`*` vs `_`, strong marker differences)
- Link rendering (autolink vs explicit Markdown link)
- Escaping policy (special chars and punctuation)
- Table layout (alignment rows, padding, pipe escaping)

## License and Reuse Note (High Level)

- Most shortlisted sources are permissive (MIT/BSD-2-Clause).
- `reverse_markdown` is WTFPL.
- Keep attribution for imported fixtures in a dedicated third-party fixture note.
- Treat this as engineering guidance, not legal advice.

## Research Sources

### mixmark-io/turndown
- https://api.github.com/repos/mixmark-io/turndown
- https://api.github.com/repos/mixmark-io/turndown/releases/latest
- https://api.npmjs.org/downloads/point/last-month/turndown
- https://registry.npmjs.org/turndown/latest

### crosstype/node-html-markdown
- https://api.github.com/repos/crosstype/node-html-markdown
- https://api.github.com/repos/crosstype/node-html-markdown/releases/latest
- https://api.npmjs.org/downloads/point/last-month/node-html-markdown
- https://registry.npmjs.org/node-html-markdown/latest

### thephpleague/html-to-markdown
- https://api.github.com/repos/thephpleague/html-to-markdown
- https://packagist.org/packages/league/html-to-markdown/stats.json
- https://repo.packagist.org/p2/league/html-to-markdown.json

### xijo/reverse_markdown
- https://api.github.com/repos/xijo/reverse_markdown
- https://rubygems.org/api/v1/gems/reverse_markdown.json

### JohannesKaufmann/html-to-markdown
- https://api.github.com/repos/JohannesKaufmann/html-to-markdown
- https://api.github.com/repos/JohannesKaufmann/html-to-markdown/releases/latest
- https://pkg.go.dev/github.com/JohannesKaufmann/html-to-markdown/v2?tab=importedby
- https://api.github.com/repos/JohannesKaufmann/html-to-markdown/contents/plugin/commonmark/testdata/GoldenFiles
- https://api.github.com/repos/JohannesKaufmann/html-to-markdown/contents/plugin/table/testdata/GoldenFiles
- https://api.github.com/repos/JohannesKaufmann/html-to-markdown/contents/plugin/strikethrough/testdata/GoldenFiles
- https://api.github.com/repos/JohannesKaufmann/html-to-markdown/contents/cli/html2markdown/cmd/testdata/TestExecute

### mysticmind/reversemarkdown-net
- https://api.github.com/repos/mysticmind/reversemarkdown-net
- https://api.github.com/repos/mysticmind/reversemarkdown-net/releases/latest
- https://azuresearch-usnc.nuget.org/query?q=packageid:ReverseMarkdown&prerelease=false
- https://api.nuget.org/v3/registration5-semver1/reversemarkdown/5.2.0.json
- https://api.github.com/repos/mysticmind/reversemarkdown-net/contents/src/ReverseMarkdown.Test/TestData

### vsch/flexmark-java
- https://api.github.com/repos/vsch/flexmark-java
- https://search.maven.org/solrsearch/select?q=g:%22com.vladsch.flexmark%22%20AND%20a:%22flexmark-all%22&rows=20&wt=json
- https://api.github.com/repos/vsch/flexmark-java/contents/flexmark-html2md-converter/src/test/resources
