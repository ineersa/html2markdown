Fixtures copied from the Rust project in two places:
- `https://docs.rs/crate/html-to-markdown-rs/2.28.2/source/tests/integration_test.rs`
- `https://github.com/kreuzberg-dev/html-to-markdown/tree/main/tests/test_apps/fixtures`

Each case is stored as `*.html` -> `*.md` pairs so they can be reused in the PHP
port's fixture-based tests.

Naming conventions:
- `rust_<test_name>.html`
- `rust_<test_name>.md`
- `rust_fixture_<group>_<index>_<name>.html`
- `rust_fixture_<group>_<index>_<name>.md`

The expected Markdown content in each `.md` file mirrors the Rust test output.
