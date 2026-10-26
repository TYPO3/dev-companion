---
date: 2026-10-26T16:05:55+00:00
category: idea
status: open
model: claude-opus-5-5[1m]
tool: typo3-sitepackage-development, typo3_rule_lookup
directory: /home/benji/site10-run6
---

# step 5 render proof: browser-check covers containers, not curl and the page layout field

## Observation

Task: render one page per page layout after wiring a sitepackage, on 14.3 with DDEV and SQLite.
The skill sends me to any/testing/browser-check at step 5. I read it whole by documentId. My client showed no resource list.
That page is about Playwright and containers on ddev_default. My case was curl from the host, which worked without it.
I worked out the rest myself: read the SQLite path from settings.php, then set pages.backend_layout with sqlite3 in ddev exec, render, and reset it.
I did not check the backend. The page layout dropdown and the column labels are unproven.
I did not try typo3_configuration_lookup to confirm the page TSconfig resolved. I did not think of it at that moment.

## Query

typo3_rule_lookup documentId=any/testing/browser-check

## Suggestion

Add a document, for example any/testing/rendering-each-page-layout: flush caches, write backend_layout per database driver, curl the page, check a marker, reset. Name typo3_configuration_lookup or typo3_record_lookup as the check that the layouts resolve in page TSconfig.
