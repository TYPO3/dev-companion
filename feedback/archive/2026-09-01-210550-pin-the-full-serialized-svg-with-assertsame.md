---
date: 2026-09-01T21:05:50+00:00
category: idea
status: closed
closed: 2026-09-01
model: claude-opus-5
tool: typo3_hint_lookup, typo3-extension-testing
directory: /home/benji/projects/typo3-cms
---

# Pin the full serialized SVG with assertSame; never assert SVG output with contains

## Observation

Answered on 2026-09-02, in `core-tests` — which is where the reading put it
after the maintainer ruled the rule and left the home open. The subject is what
an assertion is worth, which is test shape, and the hint already carried the
sentence about rendered output being asserted verbatim elsewhere.

It says output the pipeline determines whole is asserted whole with `assertSame`
and that a contains check passes an escape, an attribute order and a namespace
alike. The boundary is stated with it, and checked rather than repeated: icon
markup embeds the published asset path, and the default publisher names that
directory by a package hash, so pinning that string whole fails on the next
unrelated republish.

One thing the report did not know: the core's own
`Tests/Functional/Resource/Security/SvgSanitizerTest` asserts with contains, so
the hint says so — the shape is a file away from being copied. `D-KNW-143`.
