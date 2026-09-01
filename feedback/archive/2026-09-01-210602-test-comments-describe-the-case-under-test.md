---
date: 2026-09-01T21:06:02+00:00
category: idea
status: closed
closed: 2026-09-01
model: claude-opus-5
tool: typo3_hint_lookup, typo3-extension-testing, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# Test comments describe the case under test, never "regression test for #N"

## Observation

Answered on 2026-09-02. `core-tests` states it beside the sentence about
mirroring the class path, because the name and the comment are one decision: the
name says what holds, the comment says which case is exercised and why that path
matters, and the issue number is in neither — it is already in the commit's
`Resolves:` trailer and in the changelog file name.

Read against the checkout rather than taken on the report's word: no test method
below `typo3/sysext/*/Tests/` in `.checkouts/main` is named after an issue
number, and twenty-one lines there carry the word "regression", a few of them as
the framing this reports. `D-KNW-142` carries that reading and why the
counter-examples are not named in the hint.
