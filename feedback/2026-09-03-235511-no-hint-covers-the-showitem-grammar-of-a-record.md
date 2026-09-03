---
date: 2026-09-03T23:55:11+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
directory: /home/benji/projects/site-tierheim
---

# No hint covers the showitem grammar of a record type of your own

## Observation

Task: regroup the fields of a project extension's own table into palettes and tabs so the editing form reads better.

typo3_task_guide (changeType cleanup, the TCA file as the only path) and typo3_hint_lookup with "TCA palettes tabs showitem field grouping for a custom table" both returned tca-formengine and tca-core-palette as the best matches, at bestCoverage 0.52, plus two hints about something else (frontend-records, content-element-shape).

tca-core-palette is detailed and good, but it is the opposite direction: appending a field to a palette core owns, from an Override, with addFieldsToPalette(). It says nothing about writing the palettes section of your own table, where you own the whole string and no override rules apply.

Nothing in the returned set answered what the task turned on: the item grammar of showitem, the empty middle segment of a palette item, that a palette's label lives in the palettes section rather than in showitem, that a palette renders its items in one row and a line break starts the next, whether a palette may carry a wide field such as an RTE, and which label forms a project may write on v14.

I wrote the palettes and the showitem from memory and proved them with a functional test I had to invent: every item of the type names a column, no field twice, every editable column reaches the form. That test guards exactly the silent failure tca-core-palette describes for core palettes, which suggests the warning belongs to the own-table case just as much.

What would have helped: a hint id such as tca-own-showitem, matched by "palette", "tab", "showitem", "field grouping" and "record type", carrying the grammar of a showitem item, where a palette's label lives, the line-break item, and the fact that an item naming no column is skipped without logging - so a regrouping is where a field goes missing, and a test holding the type's items against the columns is cheap.
