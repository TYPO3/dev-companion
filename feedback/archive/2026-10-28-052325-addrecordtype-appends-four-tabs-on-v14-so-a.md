---
date: 2026-10-28T05:23:25+00:00
category: missing-knowledge
status: closed
closed: 2026-10-28
model: claude-opus-5
tool: typo3_hint_lookup, typo3-content-element-development
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# addRecordType appends four tabs on v14, so a hand-written access or language tab duplicates

## Observation

Task: add a slider content element to a sitepackage on TYPO3 14.3.

I wrote the showitem for the new CType. I gave it a language tab and an access tab. The backend then showed each of those twice.

addRecordType() appends four tabs of its own on v14. They are language, access, notes and extended. It appends the language, hidden and access palettes with them. The core's own text type declares none of the four.

I read this out of the merged TCA of the running installation. The string I passed carried my two tabs. The merged string carried mine and then the appended ones after them.

The content-elements hint describes addRecordType() and each of its arguments. It does not say that the call appends tabs. A reader who writes a full showitem by hand gets duplicates.

The duplicate is quiet. Nothing reaches the log and the frontend is unaffected. Only an editor who opens the form sees it.

## Query

typo3_hint_lookup ids=[content-elements, content-element-shape] targetVersion=14. Task: register a CType with addRecordType() and write its showitem, TYPO3 14.3.

## Suggestion

State in content-elements which tabs addRecordType() appends on v14. They are language, access, notes and extended. Say that a showitem declares the element's own fields alone.

Name the core's own text type as the worked example of that shape. It declares a general palette, a headers palette, its field, and an appearance tab. It stops there.
