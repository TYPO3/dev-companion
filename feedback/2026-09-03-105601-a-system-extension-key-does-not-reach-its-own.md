---
date: 2026-09-03T10:56:01+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# a system extension key does not reach its own Forge category, costing a round trip

## Observation

Task: find an open impexp bug on forge.typo3.org and fix it.

My first call passed category="impexp" — the system extension key, which is also the word the user used and the word the directory typo3/sysext/impexp/ is named after. It came back status "empty", total 0, categoriesUsed [], with the full list of 55 category names. The area is spelled "Import/Export (T3D)". My second call with that literal string answered 19 issues.

The behaviour is documented ("A word naming none or several is answered with every area the project has") and it did self-correct, so this is not a bug — the recovery worked and cost exactly one round trip. But the mapping that failed is the most predictable one in the whole tool: every core issue category corresponds to one or more system extensions, and the extension key is what a developer standing in a checkout actually holds. I was in typo3/sysext/impexp/ with its files open; "impexp" was the only name I had.

The same trap is latent for other areas whose category name shares no word with the extension key: ext:impexp -> "Import/Export (T3D)", ext:belog/beuser -> "Backend User Interface", ext:lowlevel -> "Miscellaneous"(?), ext:form -> "Form Framework", ext:indexed_search -> "Indexed Search". Only some of those are reachable by the key.

The empty answer returning the full category list is the right recovery and should be kept — it is what let me fix it in one step rather than guessing.

## Query

typo3_forge_lookup(backlog="stale", tracker="Bug", category="impexp", limit=30) — returned status "empty", total 0; then typo3_forge_lookup(backlog="stale", tracker="Bug", category="Import/Export (T3D)", limit=40) — returned 19 issues

## Suggestion

Match the category argument against system extension keys as well as against category names, so category="impexp" resolves to "Import/Export (T3D)" and the answer says which mapping it used (the same way categoriesUsed already reports the tracker's own spelling). Where the key maps to several areas, list them the way an ambiguous word is already handled. Failing that, have the empty answer name the likely intent: "no category matches 'impexp'; the system extension impexp is filed under 'Import/Export (T3D)'" would have saved the round trip and is one static mapping table.
