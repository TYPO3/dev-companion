---
id: D-SKL-103
title: 'A review brief carries the surface list it is written from'
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::aBriefNamesTheHintsItLeftBehind
  - HintsTest::anExtensionReviewBriefCarriesTheSurfacesOfTheChecklist
---

# D-SKL-103 — A review brief carries the surface list it is written from

**`typo3_task_guide` with `changeType="audit"` returns the surfaces of a project
or an extension review as `surfaces`, each with the hint ids that answer it.**

The brief also says that the list goes into the reply before the first read, in
its text and as `surfacesOwed` beside the list. Step 3 of
`typo3-extension-health` names that answer as the list to write.

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
  names are the ones `references/checklist.md` writes under *Audit surfaces*, in
  its order. `HintsTest::anExtensionReviewBriefCarriesTheSurfacesOfTheChecklist`
  holds the two to each other, and holds every id to a hint that answers.
- **Weighed against `D-SKL-074`.** That entry moved an obligation to where the
  session is when it acts, and its first **Wrong if** fired twice. A sentence at
  the end of the order did not reach the report. Here the place a session is at
  is the brief, and the brief is an answer this server corrects with a release.
  A skill in somebody's project stays as it was installed.
- **Weighed against `SKILL-11`.** The list arrives whole from a source the
  request cannot narrow. The session marks what the request left out **not
  requested** on it, which step 4 already asks.
- **A core review gets no list.** `typo3-core-patch-review` closes on surfaces
  of its own, and a brief for a change carries none.
- **The list is a floor, and the brief says so.** A subsystem the checkout ships
  that no entry names gets a row of its own. `SURFACES_OWED` and step 3 carry
  that sentence.
- **Shipped content is an entry of its own.** It stands on every list, because
  the brief does not read the checkout. A package without `Initialisation/`
  marks it not applicable, at the cost of one line.
- **Rejected: a third wording of step 3.** Two wordings did not produce the
  list, and `D-SKL-049` weighs a further one against corrections nobody has met.

## Assumed

- That a session writes down a list an answer hands it, where it did not write
  down a list a skill told it to derive.
- That the hint ids per surface are the right first question for a surface with
  no files. They are this server's routing, read off the hint titles, and no run
  has asked them yet.
- That a session makes the task-list call it is told to make, as it made the
  hint lookups.

## Wrong if

- The next `REVIEW-01` or `REVIEW-02` run gets the brief with `surfaces` and
  still writes the list only at the end. The lever was then not the source of
  the list, and the remaining one is a gate that refuses the first read.
- A session copies the list and reads every surface, including the ones a
  focused request left out. Then the list drags the reading along, which is
  `D-SKL-002`'s first **Assumed** failing.
- A run asks the ids and finds a surface's hints off its subject. The routing is
  then a guess, and the field goes back to names alone.
- A run again leaves a subsystem the checkout ships unread because no entry
  names it. The sentence about the floor then did not reach the reading.
- The next run still opens no task list before the first read. Then a session
  follows a call to this server alone, and the list has to come back from one.
- A judge reads a task list as hidden from the reader and marks the surface
  criterion missed on it. Then the criterion wants text, and the question goes
  to the maintainer.

## Since then

The second `REVIEW-02` run of 2026-10-28 wrote the seven surfaces into its reply
before the first read, so the first **Wrong if** did not fire. The run took the
list as its whole work list instead. It never read `Initialisation/`, although
`typo3_extension_describe` listed `Initialisation/data.xml`. The run before it
had made that import its top finding. The **Decided** bullets on the floor and
on shipped content answer that run.

The instruction reached no Claude Code session on 2026-10-28. It stood in the
text half alone, and Claude Code hands the model the data half (`D-EVI-011`).
The second `REVIEW-01` run got `surfaces` and never the sentence. So that run
tested the list without the instruction, and the first **Wrong if** has not
fired yet. `surfacesOwed` now carries the sentence in the data, as `R-ANS-002`
asks.

## Since then

The first **Wrong if** fired on the third `REVIEW-01` and `REVIEW-02` runs of
2026-10-28. Both got `surfacesOwed` and wrote no list before the first read.
Both asked `typo3_hint_lookup` per surface, the half that names a call. A gate
that refuses the first read is out of reach, because the read is the client's
own tool. So the list became a call. `SURFACES_OWED` and step 3 now ask for it
in the client's task or todo list, one item per surface, and in the reply only
where the client has no such tool. The criterion asks that the review
**establishes** its surfaces before the read, and a task list in the trace does
that. **Assumed** and **Wrong if** carry the task list's own bullets.

## Since then

The fourth `REVIEW-01` run of 2026-10-28 made one task item per surface before
the first read, so the task-list bullet of **Wrong if** did not fire. It then
asked `typo3_hint_lookup` for two of eight surfaces and none of the seven ids
`omittedHintsOwed` named. The fourth `REVIEW-03` run fetched five of six omitted
ids in one batch and left `backend-lit-task`. Both made the first step of an
owed sentence and dropped the step after it. So each `surfaces` entry and each
`omittedHints` entry now carries its call as `lookup`, and a task item made from
an entry names that call. This entry is wrong on that change too if the next
`REVIEW-01` run writes items with the lookup and still judges a surface without
it.
