---
id: D-SKL-103
title: 'A review brief carries the surface list it is written from'
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::anExtensionReviewBriefCarriesTheSurfacesOfTheChecklist
---

# D-SKL-103 — A review brief carries the surface list it is written from

**`typo3_task_guide` with `changeType="audit"` returns the surfaces of a project
or an extension review as `surfaces`, each with the hint ids that answer it.**

The brief also says in its text that the list goes into the reply before the
first read. Step 3 of `typo3-extension-health` names that answer as the list to
write.

## Evidence

- **Three runs missed step 3.** `REVIEW-01` of 2026-10-11, and `REVIEW-01` and
  `REVIEW-02` of 2026-10-28 (`scenarios/runs/`), wrote no surface list before
  the first read. Each list stood only as the closing **Surfaces** table. The
  2026-10-28 runs had the wording `D-SKL-002` added on 2026-10-11, "Write it
  into your reply".
- **The brief is the last answer before the reading.** Both 2026-10-28 runs
  called `typo3_task_guide` with `changeType` audit before their first checkout
  read: call 9 of 33 in `REVIEW-01`, call 3 in `REVIEW-02`. Neither answer named
  a surface.
- **The hint lookups came after the judgment.** `REVIEW-01` asked
  `typo3_hint_lookup` three times after twenty checkout reads, each query a
  finding already formed. `REVIEW-02` asked once, by two ids. Neither asked one
  query per surface, as the skill's *Ask before you judge* says.
- **A broad query per surface reaches little.** `bin/cli hints:probe` on one
  English query per checklist surface matched nothing for Package, Quality and
  Pinned versions, and one hint for Persistence. So the list carries hint ids
  rather than a query text.

## Decided

- **The answer carries the list, and the skill keeps its account.** The surface
  names are the ones `references/checklist.md` writes under *Audit surfaces*,
  in its order. `HintsTest::anExtensionReviewBriefCarriesTheSurfacesOfTheChecklist`
  holds the two to each other, and holds every id to a hint that answers.
- **Weighed against `D-SKL-074`.** That entry moved an obligation to where the
  session is when it acts, and its first **Wrong if** fired twice. A sentence at
  the end of the order did not reach the report. Here the place a session is at
  is the brief, and the brief is an answer this server corrects with a release.
  A skill in somebody's project stays as it was installed.
- **Weighed against `SKILL-11`.** The list arrives whole from a source the
  request cannot narrow. The session marks what the request left out **not
  requested** on it, which step 4 already asks.
- **A core review gets no list.** `typo3-core-patch-review` closes on surfaces of
  its own, and a brief for a change carries none.
- **Rejected: a third wording of step 3.** Two wordings did not produce the
  list, and `D-SKL-049` weighs a further one against corrections nobody has met.

## Assumed

- That a session writes down a list an answer hands it, where it did not write
  down a list a skill told it to derive.
- That the hint ids per surface are the right first question for a surface with
  no files. They are this server's routing, read off the hint titles, and no run
  has asked them yet.

## Wrong if

- The next `REVIEW-01` or `REVIEW-02` run gets the brief with `surfaces` and
  still writes the list only at the end. The lever was then not the source of
  the list, and the remaining one is a gate that refuses the first read.
- A session copies the list and reads every surface, including the ones a
  focused request left out. Then the list drags the reading along, which is
  `D-SKL-002`'s first **Assumed** failing.
- A run asks the ids and finds a surface's hints off its subject. The routing is
  then a guess, and the field goes back to names alone.
