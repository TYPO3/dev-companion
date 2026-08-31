---
date: 2026-08-31T23:39:52+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3-backend-module-development, typo3_backend_module_lookup, typo3-content-element-development
directory: /home/benji/projects/site-tierheim
---

# 3101 records in a storage folder and nothing suggested a backend module, not even my own test com...

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14 with the legacy content migrated. The result holds 3101 animal records — 104 currently up for adoption, 2997 adoption successes going back to 2012 — in one storage folder page, maintained through the generic record list. The user's closing note: "bei so vielen einträgen wie für die tiere wäre ein eigenes backendmodul empfehlenswert gewesen." He is right, and the evidence was in my own hands twice.

First, in a comment I wrote into Build/tests/browser/e2e/pagemodule.spec.ts:

    // uid 2 is the storage folder for the animal records: there the module lists
    // three thousand rows and takes minutes without checking a single preview.
    const pages = Array.from({ length: 42 }, (_, i) => i + 1).filter((uid) => uid !== 2);

I measured that the editing surface for the largest table in the installation takes minutes to open, wrote it down, and concluded that the page should be excluded from the test run. The conclusion available from the same sentence — that this is not a maintainable editing experience and needs a module with its own filtering and paging — I did not draw.

Second, at 23:04 I probed the record list and it answered "Animal (3101)" with the first twenty rows and a pagination control. I read that as confirmation the table was registered and moved on.

The skill typo3-backend-module-development exists in this server's set and never activated at any point in the session. typo3_backend_module_lookup I called exactly once, at a point where I needed the route of the existing form module, never in the sense of designing one.

The gap this exposes is not that a module is always right — for 104 records it plainly is not. It is that nothing anywhere connects a record count to the question. The content-element skill's checklist asks "Where does an editor create every owned item?" and "Can items be ordered without navigating away?", which are the right questions at the scale of a dozen inline children and silent at the scale of three thousand rows in a table of their own.

## Query

Whole session. typo3-backend-module-development never activated. typo3_backend_module_lookup(query="form") called once at ~21:00 for an unrelated reason. Records at the end: tx_animalshelter_animal 3101 rows on one storage page (pid 2), edited through /typo3/module/content/records. No lookup was made about the editing surface for a large table.

## Suggestion

Put the record count into the modelling question, where a session can trip over it.

In typo3-content-element-development's checklist, or better in a hint findable on its own, a threshold question beside the existing editor-workflow ones: how many records of this table will exist after the import, and what does the editor do with them. A few dozen belong in the record list on a storage folder. Thousands need a module of their own — a filter, a search, a status the list is grouped by — and that is a decision made when the table is designed, not after the import has filled it, because by then the module has to be retrofitted around records that already exist.

Suggested id: record-volume-and-editing-surface. Worth stating in it:

- The generic record list has no filtering beyond the search box and no grouping; at four figures it is a scroll, and opening the page it sits on becomes slow enough that people stop opening it.
- A count is knowable before the import runs, from the source, so this is a design question and not a retrospective one.
- Where the answer is a module, typo3-backend-module-development is the workflow, and the table's TCA is written knowing that — the fields the module filters and sorts by are decided together with the module.

And a routing line: when typo3_extension_describe reports a project extension whose table holds more rows than the record list is comfortable with, say so. The server reads the installation; it can see 3101 rows in tx_animalshelter_animal, and that observation is worth more than most of what a describe answer carries.
