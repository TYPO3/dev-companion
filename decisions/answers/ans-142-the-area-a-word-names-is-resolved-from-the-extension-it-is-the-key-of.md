---
id: D-ANS-142
title: The area a word names is resolved from the extension it is the key of
date: 2026-09-04
status: open
coveredBy:
  - ForgeTest::aSystemExtensionKeyReachesTheAreaItsIssuesAreFiledUnder
---

# D-ANS-142 — The area a word names is resolved from the extension it is the key of

**A `category` word that names no area of the tracker is looked up as a system
extension key, because the key is what a caller standing in a checkout holds.**

Half the keys reach no area by their own spelling, and the recovery costs the
caller a call each time.

## Evidence

- `feedback/2026-09-03-105601`. `typo3_forge_lookup(category="impexp")` answered
  `empty` with all 55 area names, and the second call with `Import/Export (T3D)`
  answered 19 issues. The session was standing in `typo3/sysext/impexp/` and had
  no other name for the subject.
- Measured on 2026-09-04 over the 38 entries of
  `knowledge/catalog/system-extension/` and the 55 areas the recorded answer in
  `documentation/server/tools/typo3_forge_lookup.rst` lists: the key resolves to
  exactly one area for 19 of them through `Forge::named()`. The other 19 include
  `impexp`, `beuser`, `belog`, `lowlevel`, `redirects` and `filelist`.
- The composer title of the entry — the part before the dash in its
  `description` — resolves 23, and it is a different 23. Taken only where the
  key itself reached nothing it adds six: `beuser`, `extensionmanager`,
  `filemetadata`, `impexp`, `sys_note` and `tstemplate`. Five of those read
  correctly and `sys_note` does not: it lands on
  `System/Bootstrap/Configuration`, which is a plausible area and not that
  extension's.
- The empty answer's own recovery works and the feedback asks for it to be kept.
  What it does not do is save the call.

## Decided

- Step 1a: the mapping from a system extension to the area the core files its
  issues under is knowledge nothing here holds.
- `low`. One session reported it, the recovery worked, and it cost one call.
- The title derivation is not the shape to build. One in six of the entries it
  adds resolves to the wrong area, and an answer that reads a real area's issues
  under the caller's word is worse than the empty one they already recover from.
- What the shape is instead: the keys that reach no area are a curated mapping,
  and what keeps it true is a command reading it against the areas
  `Forge::categories()` fetches — the arrangement `manuals:check` and
  `components:check` already have. A mapped area the tracker renames is then
  reported rather than silently unmatched.

## Assumed

- That the 55 areas the recorded answer lists are the areas today. The list is
  fetched live by the tool and was recorded on the day of that run.

## Wrong if

- A second session reports the same round trip against an area the curated
  mapping would not have covered. Then the mapping is not what is missing and
  the matcher is.
- The core renames its areas often enough that the check is the maintenance
  rather than the guard.

## Since then

Built the same day. `knowledge/catalog/system-extension/entries.json` carries a
`forgeCategory` on `impexp`, `redirects` and `extensionmanager`, `Forge` reads
it where the caller's word matched no area, and `categoriesUsed` names the area
it read — which is what the answer already promised to say.
`bin/cli forge-categories:check` holds all three against the areas the project
publishes.

Three rows and not sixteen, because the derivation above is what says which
extensions have an area of their own. The other thirteen file into
`Backend User Interface` and `Code Cleanup`, and mapping a key onto an area that
broad would answer a narrow question with somebody else's issues.
