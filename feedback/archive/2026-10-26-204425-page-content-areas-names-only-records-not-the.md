---
date: 2026-10-26T20:44:25+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/site10-run10
---

# page-content-areas names only .records, not the other keys of a content area

## Observation

Task: page templates for two page layouts, TYPO3 13.4.
I wanted the rendered page to show that the sidebar area exists, also when it has no records. Then a request proves the set page TSconfig loaded.
The page-content-areas hint says each area is {content.<identifier>.records}. It does not say what else an area carries.
I read vendor/typo3/cms-frontend/Classes/DataProcessing/PageContentFetchingProcessor.php. An area is the column array from the backend layout, with colPos, identifier, name and records. I then wrote data-content-area="{content.sidebar.identifier}". It rendered "sidebar" with the twoColumns layout.
The answer was right and stopped one step short. The skill itself asks for a render that proves a non-main area, so the template needs this key.

## Query

typo3_hint_lookup id=page-content-areas targetVersion=13.4

## Suggestion

Add to page-content-areas: an area is the backend layout column array with colPos, identifier, name and records. An area of a column the layout does not declare is absent. Give {content.sidebar.identifier} as the way to prove in the output that the layout loaded.
