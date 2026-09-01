---
id: D-SKL-021
title: 'Triage and fetching a patch are two workflows'
date: 2026-08-05
status: open
coveredBy:
  - SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder
  - SkillTest::everySkillStatesWhatItOwns
  - SkillTest::judgmentHeavySkillsKeepTheirChecklistBesideThem
---

# D-SKL-021 — Triage and fetching a patch are two workflows

**Saying what an open issue still claims and fetching a patch under review each
earn a task skill, and each ends at the outcome that stops the work.**

Both were reachable only as the opening steps of `typo3-core-patch-development`,
where they are priced as the prelude to a change somebody has already decided to
make. Read that way the cheap outcomes disappear: a triage exists to be able to
end at "this is gone", and fetching a patch exists to be able to end at "this no
longer applies, and here is where". A workflow that treats those as failures
resolves past them, which is how a session produces a patch nobody wrote.

## Evidence

- `R-SKL-016` already fixes that the assessment before a patch reads the tracker
  and the review server. What it does not carry is the case where there is no
  patch to assess yet: the session holds no issue number, and the backlog is the
  input.
- The tracker offers no way to reach that backlog until `D-ANS-054`, so no skill
  could have routed to it.
- The Gerrit workflow document carried the push side whole and the reading side
  as one bullet pointing at a menu in a browser. Measured on 2026-08-05, the
  fetch is not guessable from it: `refs/changes/02/95102/2` resolves over the
  review server's URL and returns nothing over the GitHub URL the same checkout
  fetches from, so `git fetch origin refs/changes/…` fails in a checkout whose
  push would reach the change.
- Both skills judge rather than build, so both keep a checklist beside them —
  the verdicts, and the rule that decides a conflict.

## Decided

- Two skills, not one and not a section of the patch workflow. They own opposite
  halves of "what is the state of this": one reads a report against the code,
  the other reads a proposed change against the code.
- Each stops before the act that belongs to somebody else. The triage writes a
  verdict and touches the tracker in no way — no comment, no closing, no
  reassignment. The checkout applies and rebases locally and pushes nothing,
  because a rebased patch set pushed is a patch set opened in the author's name.
- The conflict rule is one sentence: a conflict is resolvable only where the
  change itself decides it. Everything else in that checklist is worked examples
  of the two sides of it.
- Restoring the checkout is a step of the workflow and not an afterthought of
  the stop. It runs whichever way the rest went, because a checkout sitting on a
  fetched patch set is as bad a place to start the next task from as to abandon,
  and it is one order: end what is in progress, return to the recorded branch,
  establish that nothing of the patch is left untracked, fast-forward from the
  remote the checkout fetches from rather than from the review server, and put
  the installed dependencies back in step. The last two are where it goes wrong
  silently — the change refs are on the other URL, and dependencies belonging to
  the other revision fail as a test failure.
- The fetch went into `knowledge/documents/`, not into the skill. A skill may
  carry no command the checkout has not been asked for, and a ref form written
  into a file installed in somebody else's project is a fact no release here
  corrects.
- Both shipped as drafts first, held back by a declaration in their own front
  matter that no longer exists — `D-SKL-087`. Published on 2026-08-05 after the
  maintainer read them, with the core suites and the DDEV project named at the
  step that reproduces — the two facts the first draft left to
  `typo3_test_run_guide` alone and that decide whether a reproduction is about
  this checkout at all.

- `SKILL-12` and `SKILL-13` in `scenarios/contracts/task-skills/` are what
  measure the behaviour none of the tests above reads off the file.
## Assumed

- Triage is a task people bring as a task. If it only ever arrives inside "fix
  this issue", the description routes to nothing and the skill is a directory
  nobody loads.
- The stopping rules are readable off a conflict without the issue in hand. The
  checklist says that needing to re-read the issue is itself a stop, which
  assumes that boundary is recognisable at the time.

## Wrong if

- A recorded run reaches the triage skill and produces a verdict with no branch
  and no line — the shape the skill exists to prevent, surviving it.
- Sessions reach the checkout skill and stop on conflicts that were plainly
  transcription, which would say the rule is drawn where it costs more than it
  saves.
- The two descriptions collide with `typo3-core-patch-development` in practice:
  a request to fix an issue loads the triage skill, or a request to check an old
  report loads the patch skill.
- Nobody uses either as a workflow of its own, and both are only ever reached
  from the patch skill — which would say they were two sections and not two
  skills.
