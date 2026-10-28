---
date: 2026-10-28T08:59:31+00:00
category: missing-knowledge
status: closed
closed: 2026-10-28
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# what EXT:form accepts and then ignores: validators on Hidden, on readonly, and a partial's attrib...

## Observation

Task: add a contact form with spam protection to a sitepackage, TYPO3 14.3.10.

Trimmed: the four places where EXT:form accepts configuration and does not act on it are answered in `form-framework` — `D-KNW-177`. What remains are two notes from the same session. fluidAdditionalAttributes renders the string 'true' as the attribute name, so aria-hidden: 'true' becomes aria-hidden="aria-hidden"; the same value written as an inline Fluid array in a partial renders correctly. And a change to form YAML needs the caches flushed and then one further request: the first request after the flush answered from the previous definition, and I twice read that as the code not working.

## Query

typo3_hint_lookup ids=[form-framework] targetVersion=14. Observed on typo3/cms-form 14.3.10 with a form definition shipped from Resources/Private/Forms/.

## Suggestion

Say how to write a fluidAdditionalAttributes value that must render as the string "true". Say that the first request after a cache flush can still answer from the previous form definition.
