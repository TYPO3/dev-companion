---
id: R-DIS-011
title: 'The entrypoint installs its own client configuration'
status: held
heldBy:
  - InstallerTest
---

# R-DIS-011 — The entrypoint installs its own client configuration

**The entrypoint can install its own stdio configuration into the caller's
`.mcp.json` on an explicit `install` command.**

It preserves every unrelated entry, is idempotent for its own command, and
refuses to replace a `typo3-dev-companion` entry that points somewhere else. The
request path stays read-only; no ordinary lookup writes client configuration.

## From

The two manual absolute-path JSON snippets between the discovery of the package
and the first call to it (2026-07-30).
