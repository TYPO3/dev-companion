---
date: 2026-10-26T13:43:03+00:00
category: tool-gap
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/site10-run5
---

# hint_lookup takes one id per call; the sitepackage skill needs five or six in a row

## Observation

Task: create a sitepackage on TYPO3 14.3.5.
The skill and the task_guide checklist name hint ids to fetch by id. The id parameter takes one string.
I made six parallel calls. Each answer repeated the same envelope: targetVersion, domains, availableHintsWithheld.
No call returned nothing. No call needed a retry with different arguments.

## Query

typo3_hint_lookup id=sitepackage-fluid-styled-content | sitepackage-layout | sitepackage-typoscript-reference | page-content-areas | installation-setup | frontend-page-rendering, targetVersion=14.3

## Suggestion

Accept a list of ids in one call, for example ids=[...]. Or let task_guide take an option that inlines every hint its checklist names, so that omittedHints is empty.
