---
date: 2026-09-01T21:03:45+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup, typo3_documentation_lookup
directory: /home/benji/projects/typo3-cms
---

# Fluid resolves get/is/has accessors before properties, so hasItems() shadows an items property

## Observation

Trimmed on 2026-09-01. What this reported is in the corpus and has been since
2026-08-14: `fluid-object-access` states the resolution order, that a method is
reachable only under the name of the property it looks like, the shadowing that
makes `{obj.items}` a boolean, and the naming rule for a DTO a template reads —
`D-KNW-075` is the reading behind it, taken across the four checkouts, and it
binds what `<f:for>` does with a boolean per major, which this report asserts
unbound. The searchable half the suggestion asked for is there too: the
exception string, `f:for each` and `shadows the property` are in its `appliesTo`.

What is left is that none of it fires where the class is written. Measured on
2026-09-01: the exception message with a `.html` path reaches the hint first,
and with a `Classes/` path reaches nothing, because the query carries no Fluid
word and `Domains` places it in `php`. The card serving this carries the two
levers.
