---
id: D-ANS-195
title: A changelog lookup takes the word the other tools take
date: 2026-11-06
status: open
coveredBy:
  - ChangelogLookupTest::targetVersionFiltersAsVersionDoes
---

# D-ANS-195 — A changelog lookup takes the word the other tools take

**`typo3_changelog_lookup` takes `targetVersion` beside `version`, and `version`
answers where a call passes both.**

## Evidence

- `feedback/archive/2026-10-31-061403-twelve-tools-say-targetversion-and-one-says.md`:
  a run read `targetVersion` from `typo3_task_guide` and the lookup refused it.
  `src/Tool/ChangelogLookup.php` still declared `version` alone on 2026-11-06.

## Decided

- **An added word rather than a rename.** A client installed months ago holds
  the old schema, and `AGENTS.md` says to add a field rather than rename one.
- The `extension` parameter keeps its name, and its description now says it.

## Assumed

- That the word costs less than the retry it removes.

## Wrong if

- A run still passes `extensionKey` with the new description in front of it.
  Then the parameter name is the defect, and a rename is the next step.
