---
id: D-SKL-034
title: 'A step is skippable on what the session holds'
date: 2026-08-11
status: open
coveredBy:
  - SkillTest::theDeprecationSweepIsSkippedWhereNoTypo3ApiIsTouched
  - SkillTest::theWorkflowStepRunsInEverySession
---

# D-SKL-034 — A step is skippable on what the session holds

**Step 3 of `skills/base.md` runs in every session, and the sweep keeps its
condition. A session skips a step only on what it holds, never on how it
arrived.**

Step 3's condition asked which of two routes activated the skill. Two sessions
stand measured against it and neither made the distinction, in either direction.

## Evidence

- **The condition and what it rested on.**
  [`D-SKL-015`](skl-015-a-step-is-skipped-only-where-it-has-already-run.md)
  wrote it on 2026-08-04. The step is already done where the guide's own answer
  named this skill, and stays where the client matched the skill on its
  `description`. Its first **Assumed** is what this entry now reads. That a
  session can tell which of the two ways it arrived by, which nothing measured.
- **The first sighting, before the condition existed.**
  `feedback/archive/2026-08-04-055715`, a php-cs-fixer setup in
  `/home/benji/projects/ext-guidedtour`. It loaded the schema in the same
  `ToolSearch` batch as the scope tools and never called it. Its stated reason
  is coverage rather than route. The skill's own reference "carries the workflow
  for this exact layer" and "I assumed task_guide would restate that".
- **The second, after publication.** The 2026-08-10 core patch review
  `D-SKL-033` names, transcript `d7b5b468-5ef2-4e9e-81df-27a4afbdce07` in
  `/home/benji/projects/typo3-cms`. It activated `typo3-core-patch-review` from
  the skill's `description`, which is the side the condition said keeps the
  step. It read the base whole and did not call the tool in 143 calls.
- **Neither session reached for the condition.** Read in that transcript on
  2026-08-11: `typo3_task_guide` occurs in two assistant blocks in the whole
  file — the `ToolSearch select:` batch that loaded its schema beside
  `typo3_project_describe`, `typo3_hint_lookup`, `typo3_forge_lookup` and
  `typo3_gerrit_lookup`, and the closing debrief. No turn before the written
  review names `base.md`, the order or its steps. The session passed the step
  over rather than decided against it.
- **What the sweep's condition asks instead.** Which files a change touches,
  which the session holds when it reads the step. That is the difference between
  the two conditions, and it is why only one of them has fired.

## Decided

- **The condition comes off step 3**, in `skills/base.md` and in `R-SKL-005`,
  which carries the order in prose. The step runs in every session, this skill's
  own tasks included.
- **What the step rests on instead is the path-specific brief**, which is what
  both sessions needed and neither had. The tool composes the brief from `paths`
  as well as `task`, and no skill knows which paths the caller holds.
- **This entry names the cost and takes it.** A session the guide routed here
  spends one call on an answer already in its context. That is cheaper than a
  step every session has to decide about, and the decision is what neither of
  the two made.
- **Rejected: writing what would make the call happen where the condition did
  not cover the session.** It rests on the same distinction. A session that does
  not establish which route it arrived by cannot establish that it is in the
  uncovered case. `D-SKL-033` is the other half of why: activation is the
  client's, and nothing this server publishes forces a call.
- **The sweep's condition stays**, with the evidence
  [`D-SKL-015`](skl-015-a-step-is-skipped-only-where-it-has-already-run.md)
  carries for it rather than a second copy of it. Its second **Wrong if** has
  not fired, and what it asks is answerable from the files in front of the
  session.
- **The order carries one condition from here**, which is what makes the
  preamble's "narrow on purpose" a statement about a single sentence.

## Assumed

- That the removal of the wording is what makes the call happen. Neither session
  cited it, so nothing establishes that its absence changes their behaviour.
  What the removal buys for certain is that this file no longer defends a
  passed-over step.
- That the duplicate call is cheap. It is one call per guide-routed session,
  measured as a count and not against what a session gives up for it.
- That the two conditions differ in the way this entry says. Step 5's has stood
  published since 2026-08-04. Nobody has seen a session skip the sweep on a
  change that touches TYPO3 API, which is an absence of sightings rather than a
  measurement.

## Wrong if

- A session with the unconditional step in front of it skips `typo3_task_guide`
  anyway. Then the wording was never what decided it, the answer is outside this
  file, and what is left to weigh is the channel `D-SKL-033` records as untried
  — the project's own agent instruction file.
- The duplicate call is what a guide-routed session runs short on. It spends the
  call, gets the brief it already holds, and the work is worse for it. Then the
  condition paid for itself. What somebody has to find is a form of it the
  session can answer from what it holds.
- The sweep is skipped by a session reading the condition off the skill it
  activated rather than off the files the change touches. Then the line this
  entry draws between the two conditions is not the one that matters, and both
  come off.

## Since then

The third **Assumed** fell. A session that skipped the sweep on a change that
touches TYPO3 API was on the board 52 minutes before the commit of this entry.
So the absence of sightings was an absence of readings. What it shows is not the
third **Wrong if** as written. It read the step's other paragraph, the one that
exempts a task that produces no change. So the line between the two conditions
holds. What does not hold is the last **Decided**: step 5 carries two tests for
one skip. `D-SKL-037` is that reading.
