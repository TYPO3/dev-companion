---
id: D-SKL-110
title: A core patch review reads the prose of the patch as a surface
date: 2026-11-24
status: open
coveredBy: []
---

# D-SKL-110 — A core patch review reads the prose of the patch as a surface

**The core patch review has a Prose surface. It reads the comments and the
commit message as one text against `any/writing/the-prose-a-patch-carries`,
which now carries the STE sentence rules and no word list.**

Two feedback from one review of core change 97935 on 2026-11-24 reported the
gap. The user asked for a check against ASD-STE100, and then for uniformity and
readability. The review found neither gap on its own.

## Evidence

- The page already had "Use the Words the Codebase Uses" and "One Fact, One
  Place". `TaskIntents::load()` adds it to a brief only where the task writes
  files, so a review never received it. That is step 2 of the ladder, delivery.
- The checklist and the report skeleton had no surface for prose. A review
  disposes of a surface it does not list by silence.
- Patch set 1 of 97935 named `isSiteReachable()` beside
  `SourceHostProvider::getAccessibleHosts()` in the core checkout. Its commit
  message said "web mount page" where its docblock said "web mount".
- `D-DOC-070` records that the STE dictionary cannot ship here, for its licence.

## Decided

- The maintainer chose the STE sentence rules without a word list. The page
  gains the word limits, the simple tenses, the noun cluster limit and the kept
  articles. It says that the core does not require STE.
- The page gains two rules across the patch: one term for one thing, taken from
  the code near the change, and the prose read as one text.
- The checklist gains **Prose**, the report skeleton its row, and the skill
  names the `typo3_rule_lookup` call at the step that asks the owners.
- A sentence finding ranks as **Worth a change**, because the core does not ask
  for STE. A word judged from memory of the dictionary says so in its
  **Evidence** line.
- The delivery stays in the skill. A brief that carried the page to every review
  would change `TaskIntents` for one workflow.

## Assumed

- That the page applies to every caller who writes patch prose. It stands below
  `any/`, so an extension author receives the STE rules too.

## Wrong if

- A review marks **Prose** assessed and a user still finds two terms for one
  thing in the same patch. Then the surface text does not make the comparison
  happen, and the step needs a shape of its own.
- A core reviewer objects to a comment that keeps to the page. Then the STE
  rules cost more than they give in the core, and the page binds them.
