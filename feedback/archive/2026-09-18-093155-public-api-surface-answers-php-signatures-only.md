---
date: 2026-09-18T09:31:55+00:00
category: missing-knowledge
status: closed
closed: 2026-09-19
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# public-api-surface answers PHP signatures only; a changed protected TypeScript member has no rule

## Observation

Task: change the type of the protected property unfilteredNodes on the Tree class in Build/Sources/TypeScript/backend/tree/tree.ts from string to TreeNodeInterface[], for a bugfix that targets main and 14.3.
The patch-development skill sends every patch to typo3_hint_lookup id public-api-surface to settle the target branch. That hint speaks of PHP autoload fatals, final classes and changelog types. None of it applies to a TypeScript protected member. I settled it myself: the core ships no .d.ts declarations (Gerrit change 90853 is still WIP), so no consumer can type against the member, and no core file outside tree.ts reads it. The answer I needed was whether a changed protected member of a backend TypeScript class owes an Important entry or nothing, and whether it may reach a release line. I decided nothing is owed and wrote that into the review report as an open point for the reviewer.

## Query

typo3_hint_lookup id="public-api-surface"

## Suggestion

Add a statement to public-api-surface or backend-typescript that says what the core files for a changed public or protected member of a shipped TypeScript class, with one precedent entry if there is one, and that says the core ships no type declarations so a TypeScript type change has no compile-time consumer.
