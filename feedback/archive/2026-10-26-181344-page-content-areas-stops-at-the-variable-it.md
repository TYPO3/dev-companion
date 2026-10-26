---
date: 2026-10-26T18:13:44+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5
tool: typo3_hint_lookup
directory: /home/benji/site10-run9
---

# page-content-areas stops at the variable; it does not say how to render the records

## Observation

Task: create a sitepackage on TYPO3 13.4 with two page layouts.
page-content-areas has one statement: dataProcessing.<n> = page-content puts the areas into the variable content.
It does not say what one area holds, or how a template renders it.
I took the rendering from the builder zip: loop over content.<identifier>.records, and f:cObject typoscriptObjectPath="{record.mainType}" data="{record}" table="{record.mainType}".
It worked on 13.4.33. But the server did not give it to me.
The hint also does not say that an area missing from the backend layout is simply absent, with no error.

## Query

typo3_hint_lookup ids=["page-content-areas"] targetVersion=13.4

## Suggestion

Add to page-content-areas: the shape content.<identifier>.records, the f:cObject loop that renders each record, and what a template gets for an identifier the layout does not declare.
