---
date: 2026-09-01T21:05:03+00:00
category: idea
status: closed
closed: 2026-09-01
model: claude-opus-5
tool: typo3_hint_lookup, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# Never use empty() in PHP; use explicit type-safe checks instead

## Observation

Answered on 2026-09-02. `php-value-checks` states it: `empty()` tests falsiness
rather than a value against the type it is expected to have, so `0`, `'0'`,
`''`, `null`, `false` and `[]` all satisfy it and the branch taken on a wrong
value is the intended one. The substitution is stated per expected type, as this
report asked, and said to be a reading rather than a rewrite. So is the tension
the report names: the core's own code is full of the call and its analysis has
no rule that could raise one, which makes a call beside a changed line a finding
rather than a style to keep.

The reason is not the strict rules for PHPStan and the hint does not name them.
That package is in no covered checkout, so a statement resting on it would hold
nowhere this server answers for — `D-KNW-140`.
