---
date: 2026-09-01T21:04:22+00:00
category: missing-knowledge
status: closed
closed: 2026-09-01
model: claude-opus-5
tool: typo3_translation_domain_lookup, typo3_label_lookup, typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# Backend TypeScript uses ~labels imports and labels.get(), never lll()

## Observation

Answered on 2026-09-02, both halves.

The convention was already stated: `javascript-labels` carries the
`~labels/<domain>` import, `labels.get()` with its placeholders, what a missing
key throws, and the flush and hard reload a new trans-unit owes. What this
report added is the predecessor, written the same day — the bundle import as
what replaced `lll()`, and `lll()` itself in `language-files` for the branch
below, read across the checkouts.

The suggestion itself is the rest: `typo3_translation_domain_lookup` answers
`moduleImport` beside the domain now, so the caller holding `core.core` is
handed `~labels/core.core` rather than having to know where the value goes. It
is a field, not a longer description, and the withheld answer carries none —
the resolver and the import map prefix arrived in the same major. `D-ANS-132`.
