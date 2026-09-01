---
date: 2026-08-31T23:33:27+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_documentation_lookup
directory: /home/benji/projects/site-tierheim
---

# No hint covers restricting the CType selector; six adjacent hints came back with no sign of the miss

## Observation

Task: relaunch a German animal shelter site on TYPO3 v14. At this point the user had told me, in his words, that excluding content element types "geht immer über pagetsconfig" — my sitepackage had a Configuration/TCA/Overrides/zzz_tt_content_cleanup.php that filtered $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] and unset entries from ['types'].

I called typo3_hint_lookup with task "restrict which content element types editors can select, remove CType items from the selector via page TSconfig instead of manipulating TCA", paths ["packages/tierheim_sitepackage/Configuration/TCA/Overrides/zzz_tt_content_cleanup.php", "packages/tierheim_sitepackage/Configuration/Sets/Tierheim/page.tsconfig"], targetVersion 14.

It returned six hints: content-elements, tsconfig, tca-formengine, site-sets, browser-tests, extbase-plugin-registration. Not one of them mentions TCEFORM, keepItems or removeItems. The tsconfig hint is about where page TSconfig files are auto-loaded from and how the merge works — the right subsystem, the wrong question. tca-formengine says "Prefer established TCA option names and existing FormEngine behavior", which reads as mild encouragement for the file I was about to delete.

Nothing in the answer signalled it had not found the subject. Six confident, well-formed, adjacent hints look identical to six hints that answer you.

What settled it was typo3_documentation_lookup, in two round trips: a search over ["TCEFORM removeItems CType", "page TSconfig restrict content element types", "keepItems TCEFORM tt_content"] returning excerpts with the TCEFORM page ranked second, then the same tool with page= that URL, which carried keepItems in full ("Change the list of items in TCA type=select fields. Using this property, all items except those defined here are removed", table level TCEFORM.tt_content.header_layout.keepItems, type "list of values").

So the knowledge exists in the manual and not in the hints, and the hint answer gave no way to tell. Three calls for one line of TSconfig.

One thing the manual did not settle and I still have not: whether page TSconfig accepts the multi-line `key ( ... )` form for a comma list. I wrote it that way, could not establish it, and reverted to a single line out of caution. That is an unverified retreat, not a decision.

## Query

typo3_hint_lookup(task="restrict which content element types editors can select, remove CType items from the selector via page TSconfig instead of manipulating TCA", paths=["packages/tierheim_sitepackage/Configuration/TCA/Overrides/zzz_tt_content_cleanup.php","packages/tierheim_sitepackage/Configuration/Sets/Tierheim/page.tsconfig"], targetVersion="14") -> six hints, none about TCEFORM/keepItems. Then typo3_documentation_lookup(queries=["TCEFORM removeItems CType","page TSconfig restrict content element types","keepItems TCEFORM tt_content"], targetVersion="14") -> TCEFORM page ranked 2nd. Then typo3_documentation_lookup(page="https://docs.typo3.org/m/typo3/reference-typoscript/14.3/en-us/PageTsconfig/TceForm.html", targetVersion="14.3") -> the answer.

## Suggestion

Trimmed on 2026-09-01. The subject is written up as
content-element-selector-restriction, which the probe for this query now
reaches: keepItems and removeItems as the site's decision, that they restrict
the form and not what is stored, and what unsetting the TCA items does instead —
FormEngine falls back to the types entry '0' or '1' and opens the record in
another type's form, and the page module is where "Invalid value" is drawn. What
is left open is the answer shape.

When a hint answer's top match is well below whatever score means "this is about
your question", say so in the answer. "These are the closest hints; none of them
states anything about <the subject terms>" is a different answer from six hints,
and it is the one that would have sent me to typo3_documentation_lookup in one
step instead of three.
