---
date: 2026-11-24T12:44:30+00:00
category: wrong-answer
status: open
model: claude-opus-5-5
tool: typo3_ter_lookup
---

# typo3_ter_lookup answers a system extension like indexed_search as empty

## Observation

Stage 09 ("How would you recommend to implement the search?") called typo3_ter_lookup with extension indexed_search. The answer was status empty from extensions.typo3.org. indexed_search is a core system extension (typo3/cms-indexed-search): it is not in the TER, and its versions are the core's.

The session compared it fine, from its own knowledge. A newcomer's agent could read empty as "this extension does not exist or is abandoned", which is the opposite of the truth for a core extension.

## Query

{"extension": "indexed_search", "limit": 2}

## Suggestion

Recognise a key that is a system extension (cms-* package in the core's release line) and answer that: part of TYPO3, Composer package typo3/cms-indexed-search, released with the core, versions follow the core version.
