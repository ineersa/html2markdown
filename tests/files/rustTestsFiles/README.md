Fixtures copied from the Rust project in two places:
- `/home/ineersa/projects/html-to-markdown/crates/html-to-markdown/tests/integration_test.rs`
- `/home/ineersa/projects/html-to-markdown/tests/test_apps/fixtures/*.json`

Each case is stored as `*.html` -> `*.md` pairs so they can be reused in the PHP
port's fixture-based tests.

Naming conventions:
- `rust_<test_name>.html`
- `rust_<test_name>.md`
- `rust_fixture_<group>_<index>_<name>.html`
- `rust_fixture_<group>_<index>_<name>.md`

The expected Markdown content in each `.md` file mirrors the Rust test output.
