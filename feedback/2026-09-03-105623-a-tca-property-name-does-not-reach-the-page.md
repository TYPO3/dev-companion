---
date: 2026-09-03T10:56:23+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_documentation_lookup
directory: /home/benji/projects/typo3-cms
---

# a TCA property name does not reach the page documenting it; three calls for one sentence

## Observation

Task: fix Forge #108801, where a reporter declares an inline relation only in TCA columnsOverrides. Before writing anything I had to know whether changing config.type via columnsOverrides is supported at all — that single fact decided whether the report was a defect or an invalid configuration.

It took three calls. The first passed the literal property name "columnsOverrides" twice and returned BeforeTcaOverridesEvent, "Field types (config > type)", "Restriction builder", "Restrict HTTP access" and two column-type pages — none of which mentions the property. The second, with shorter queries, surfaced the TCA Reference "Record types" page at rank 4. Reading that page whole gave the sentence I needed, in an Attention block:

  "It is not possible to override any properties in 'Proc.' scope: The DataHandler
  does not take 'columnsOverrides' into account. ... This especially means that
  columns config 'type' must not be set to a different value."

The tool behaves as documented — base.md says "The manual matches page titles and section paths, never the text of a page". So this is not a defect. But the consequence is sharp for the TCA Reference specifically: that manual is organised as a small number of large pages, each documenting dozens of named properties as sections, and columnsOverrides IS a section heading on Types/Index.html ("### columnsOverrides"). A search that matches section paths should have reached it from its own name and did not.

The cost was not only three round trips. A session with less patience stops after call 1, concludes the manual says nothing, and writes the patch on the reporter's premise. That is the failure this fact prevented — see my separate positive feedback.

Note the answer is also version-bound in a way that mattered: the 14.3 sentence is contradicted by core code on main since commit 4930c561853 (2026-01-18), which I found with git log -S, not here.

## Query

1) typo3_documentation_lookup(queries=["columnsOverrides types record type field configuration","TCA types columnsOverrides restrictions"], targetVersion="14") — nothing usable. 2) typo3_documentation_lookup(queries=["types columnsOverrides","record types showitem palettes"], targetVersion="14", limit=8) — found "Record types". 3) typo3_documentation_lookup(page="https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/Types/Index.html", targetVersion="14") — the answer.

## Suggestion

Index the TCA Reference's property sections as addressable units, so a query naming a TCA property ("columnsOverrides", "creationOptions", "previewRenderer", "label_alt_force") returns the page plus the anchor of that section, rather than requiring the reader to guess the page's title. The section headings are already in the page structure the search reads paths from.

Cheaper interim fix: when a query returns nothing whose title matches, say so explicitly and name what was searched — "no page title or section path carries 'columnsOverrides'; the manual is not searched by page text" — so a reader knows to reformulate rather than concluding the manual is silent. The empty-answer guidance that typo3_forge_lookup gives (returning the full category list) is the model.
