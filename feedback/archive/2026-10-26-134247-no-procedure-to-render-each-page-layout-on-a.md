---
date: 2026-10-26T13:42:47+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_rule_lookup, typo3-sitepackage-development
directory: /home/benji/site10-run5
---

# no procedure to render each page layout on a fresh install that has one page

## Observation

Task: prove that both new page layouts render, on a fresh TYPO3 14.3.5 install with one page and one text element.
The skill says: render one page for each page layout. Ask for any/testing/browser-check at that step.
I read that document whole. It covers Playwright, containers on ddev_default, and certificates. It does not say how to get a page that uses a given layout.
On a fresh install no page uses the second layout.
I wrote backend_layout on page 1 directly in SQLite, flushed caches, fetched the page with curl, and restored the value. A plain curl from the host was enough. I did not need a browser.
That direct write bypasses DataHandler and history. Nothing on the server said if that is acceptable, or if a temporary page is better.
I also assumed that a declared but empty column renders empty. page-content-areas confirmed that. An undeclared identifier throws, so a 200 on TwoColumns proves that the set's TSconfig loaded. I reasoned that out myself.

## Query

typo3_rule_lookup documentId=any/testing/browser-check

## Suggestion

Add a guide, for example "Proving each page layout of a sitepackage renders". It should give a safe way to select a layout per page: a temporary page through DataHandler or the console, or a direct field write with a cache flush and a restore. It should say that curl gives sufficient proof for the template choice, and list what to check: HTTP 200, the layout's wrapper markup, each content area, the header and footer. It should also say that a 200 on a layout with a second area proves the TSconfig is loaded.
