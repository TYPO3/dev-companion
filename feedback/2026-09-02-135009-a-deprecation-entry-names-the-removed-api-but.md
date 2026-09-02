---
date: 2026-09-02T13:50:09+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_changelog_lookup
directory: /home/benji/projects/site-tierheim
---

# A deprecation entry names the removed API but not what replaces it

## Observation

Task: same session — the prescribed deprecation sweep before writing a v14.3 backend module.

I ran typo3_changelog_lookup(type="deprecation", version="14", limit=200) as base.md's order asks. It returned 75 entries, each with type, version, issue, title, removal, tags and file path. Two of them touched what I was about to write:

- 14.0 #107823 "ButtonBar, Menu, and MenuRegistry make* methods deprecated"
- 14.3 #109519 "BackendUtility item list label methods"

Both titles told me to stop; neither told me what to write instead. I then read the two .rst files out of vendor/typo3/cms-core/Documentation/Changelog/ to get the Migration sections — ComponentFactory::create*() and SchemaLabelResolver->getLabelForFieldValue(). Both of those I used heavily afterwards.

So the sweep cost one large answer plus two file reads, and the two file reads carried the part I acted on. The 73 entries I did not act on were the price of the two I did — which is what a sweep is for, and I am not complaining about the volume. The gap is that the answer stops at the title.

A second, smaller point about the same call: I never used the `tag` argument, because the sweep-the-major-whole form was what base.md prescribed and it worked. The per-tag form the checklist also describes ("one call per tag") would have been more calls for the same reading.

## Query

typo3_changelog_lookup(type="deprecation", version="14", limit=200). Then, outside the server: cat vendor/typo3/cms-core/Documentation/Changelog/14.0/Deprecation-107823-ButtonBarMakeMethods.rst and 14.3/Deprecation-109519-BackendUtilityItemListLabelMethods.rst

## Suggestion

Carry the Migration section, or its first code block, on a deprecation entry — at least when the call names a single entry (a query that matches one, or an issue number). On a 75-entry sweep that would be too much text, so: return the titles as now, and let a follow-up call with the issue number return that entry whole, including Migration. I would have made two such calls and read no files.

The information is already on disk in the same package the tool reads the titles from.
