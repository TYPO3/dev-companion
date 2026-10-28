---
date: 2026-10-28T06:02:32+00:00
category: missing-knowledge
status: closed
closed: 2026-10-28
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# indexed_search renders its page browser twice and shows it for a single page of results

## Observation

Task: make the search work on a sitepackage site, TYPO3 14.3, with indexed_search.

The shipped Pagination partial guards on the existence of the pagination object. It does not guard on the number of pages. So a result set that fits one page still renders a page browser. It holds one link, labelled "Page 1", whose href is a hash.

The browser is rendered twice. One sits above the results with the count line. One sits below the results alone.

The guard that reads correctly is pagination.lastPageNumber greater than one. PaginationInterface carries that method beside getAllPageNumbers.

Two models built this page independently, one with this server and one without it. Both shipped the page browser for a single page. The user found it in the rendered page. Neither model looked.

## Query

Rendered /search with tx_indexedsearch_pi2[search][sword]=jambo on an installation holding 13 indexed pages, 2 results.

## Suggestion

Add what the shipped indexed_search templates render that a small site does not want. The page browser for one page of results, and that it is rendered twice. The advanced search link, behind which stand eight selects and a checkbox. The relevance percentage beside every hit.

Say which of those a stylesheet can reach and which need the template. That is the decision a caller makes first, and the corpus carries nothing on it today.
