---
date: 2026-09-01T21:04:22+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_translation_domain_lookup, typo3_label_lookup, typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# Backend TypeScript uses ~labels imports and labels.get(), never lll()

## Observation

Trimmed on 2026-09-02. The convention is stated: `javascript-labels` carries the
`~labels/<domain>` import, `labels.get()` with its ICU placeholders, what the
missing key throws, and the flush and hard reload a new trans-unit owes. What
this report added is the predecessor, and that is written now — the bundle
import as what replaced `lll()` on the majors that have it, and `lll()` from
`@typo3/core/lit-helper` with its silent empty string in `language-files` for
the branch below, read across the checkouts.

What is left is the suggestion itself: `typo3_translation_domain_lookup`
computes the domain and stops there, so the caller has to know the value belongs
in an import specifier. Naming the import form in that answer touches the tool,
which the judging run does not do.
