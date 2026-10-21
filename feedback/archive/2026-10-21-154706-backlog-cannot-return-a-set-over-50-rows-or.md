---
date: 2026-10-21T15:47:06+00:00
category: tool-gap
status: closed
closed: 2026-10-21
model: claude-opus-5-5
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# backlog cannot return a set over 50 rows or filter it by words, so a topic triage leaves the tool

## Observation

Task: list all open Forge issues about Extbase and translation, sorted by age, stale only. The breakdown call worked. It reported 110 open issues in "Extbase" (87) and "Extbase + l10n" (23). The topic "translation" also lives inside the plain "Extbase" category. So I had to read all 110 rows and filter subject and description by words. backlog returns at most 50 rows and has no offset. backlog does not combine with query. query is a full-text search with at most 25 hits and no category filter. No single call or pair of calls returned the 110 rows. I went to the Forge JSON API with curl: two pages of issues.json with category_id=1490|1661. Then a local regex filter over subject and description. Then I removed false positives by hand, for example "Language" in an LLL path inside a code sample. The user asked why I did not use the MCP. I did not try updatedBefore. It could have made the set smaller, but the tool gives no count per update date, so I could not know that in advance. Also, rows from the tool carry the code names each report cites and their state. I lost that because I left the tool.

## Query

typo3_forge_lookup backlog="stale" category="extbase" breakdown=true → total 110, categoriesUsed ["Extbase","Extbase + l10n"]. Need: all 110 rows, filtered by "translat|locali|l10n|language|overlay|fallback" in subject+description.

## Suggestion

Let backlog take a words filter that matches subject and description inside the enumerated set (category plus words, AND). Or give backlog an offset or a cursor, so the caller can read a set of 110 in three pages. Also add an updated-year dimension to breakdown, so the caller can choose an updatedBefore value that makes the set fit in one page. A "stale" threshold in the answer (for example "untouched for 12 months") would also help: now the caller must define stale.
