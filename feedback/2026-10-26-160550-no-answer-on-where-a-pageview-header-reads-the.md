---
date: 2026-10-26T16:05:50+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5[1m]
tool: typo3_changelog_lookup, typo3_hint_lookup
directory: /home/benji/site10-run6
---

# inline f:if inside an attribute escapes the string it builds

## Observation

Task: a sitepackage header on TYPO3 14.3 with a link whose class or text was built inline.
A string built with an inline f:if inside an attribute came out escaped.
No Fluid hint says so, or what to write instead.
The half of this feedback about the website title is answered by page-variables-and-processors (D-KNW-172).

## Query

typo3_hint_lookup task="inline f:if in an attribute escaped" targetVersion=14.3

## Suggestion

Add a Fluid hint: a string built with inline f:if inside an attribute is escaped; use a partial or f:if blocks. Verify the behaviour against the Fluid checkout first.
