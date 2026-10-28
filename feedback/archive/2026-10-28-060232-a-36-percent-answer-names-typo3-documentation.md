---
date: 2026-10-28T06:02:32+00:00
category: idea
status: closed
closed: 2026-10-28
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# a 36 percent answer names typo3_documentation_lookup as the fallback but nothing makes it a step

## Observation

Task: make the search work on a sitepackage site, TYPO3 14.3.

I asked typo3_hint_lookup about a frontend search for visitors. The answer stated its own confidence. It said the closest hint carries 36 percent of the question. It said to read the hints as the nearest subjects rather than as the answer. It named typo3_documentation_lookup for the rest.

That same answer carried one strong and correct hit. It warned that a GET search form answers page-not-found while cacheHash validation is enforced. That warning saved a long hunt.

I took the strong hit and went to the implementation. I never called typo3_documentation_lookup for this subject. I called it twice in the session, for PAGEVIEW and for the menu processor, and not here.

One correct warning inside a weak answer made the whole answer feel sufficient.

## Query

typo3_hint_lookup task='frontend site search for visitors, a search form and a results page' targetVersion=14 limit=4.

## Suggestion

Make the fallback a step rather than a sentence. Where the best match falls under a threshold, name the call that comes next and say the answer is not one. Consider returning the typo3_documentation_lookup query to run. A caller that is handed a concrete next call makes it.
