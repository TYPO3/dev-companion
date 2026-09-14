---
id: D-SKL-068
title: An audit's list is established against the work already in flight
date: 2026-08-21
status: open
coveredBy: []
---

# D-SKL-068 — An audit's list is established against the work already in flight

**`typo3-extension-health` establishes per item, before it shows the list, what
the repository already carries unmerged. The surface it asks is wider than the
open pull requests.**

The skill owns the gate at which the maintainer agrees to a list. A list that
proposes work already done on a branch nobody merged is one a maintainer cannot
agree to. Nothing between the write of the list and its display asks the
question.

## Evidence

- The feedback of 2026-08-19 09:43, from a full audit of a blog extension before
  its v14 release. The run mapped 23 open pull requests against its 17 items and
  reported item 2 as untouched by any of them. The maintainer then named a
  branch it had not looked at. `git branch -a` turned up 13 pushed branches with
  no pull request at all. One of them carried item 2 already fixed. It had the
  same diagnosis the run had reached on its own and the test the run had found
  absent. That branch also carried two v14 defects the audit had not found.
- What the skill says today. Steps 5 to 7 write the list, show it and keep it in
  the session; steps 8 to 12 work it off. `pull request` appears in step 11,
  which asks the maintainer where the commits land. It appears in the closing
  boundary paragraph, which sends one proposed change to
  `typo3-extension-patch-review`. Neither is the question, and no step between 5
  and 6 asks it.
- The audit brief is silent too. The `audit` intent in
  `knowledge/task-intents.json` carries six checklist items. What a change
  removes, what a core removal owes, the two `runTests.sh` checks. Then the
  finding gate, the declared checks, and each finding handed over with its
  consequence. None of them names work that exists.
- **The same failure got its decision once already, one audience over.**
  `D-SKL-008` and `R-SKL-014` put the issue and the review server into the core
  patch review. The third recorded `REVIEW-03` run had judged a patch as a
  change on its own. It was part 2 of a series whose part 1 was already in
  `origin/main`. The change in hand read as the whole of the work is the same
  mistake in a different workflow.
- A third arrival, the feedback of 2026-08-21 07:40. `typo3_gerrit_lookup`
  answers one change and not its relation chain. So 91563 read alone says a
  feature exists. Read as a stack of fifteen it says what it consists of, which
  parts landed and which stand abandoned. Its lever is a tool rather than a
  skill, so that card stays its own and is not taken over here.

## Decided

- The gap is real and the answer is *queued* rather than closed on the spot.
  What changes is a skill's contract, which lands in somebody else's project and
  gets a review rather than an improvisation.
- The step sits between writing the list and showing it. The list is what gets
  agreed, so the state belongs on the item and not in a paragraph beside it.
- **The skill states the surface, because the obvious read is too narrow.** Open
  pull requests, branches pushed without one, and the maintained release lines.
  The branch with no pull request is the one the run missed, and it is where a
  maintainer's own unfinished work sits.
- The method is not decided here. Whether a git command belongs in a published
  file at all, and which one, is the todo's first step. The run reported that
  `git cherry` compares patch-ids and calls a squash-merged branch outstanding.
  It reported that how far behind the branch is dominates a two-dot diff against
  the base. What settled it was a two-dot diff restricted to the files the
  branch touches, reached in four attempts. Nothing here verifies that, and the
  next release of this server does not correct a command in a skill.
- Priority `normal`, above the unjudged cards and below the decided work. What
  sets it is three arrivals at one shape across two audiences, one of which
  already cost a wrong statement to a maintainer. What keeps it off `high` is
  that a single measurement is one repository.

## Assumed

- That the repository half stays the session's own to establish.
  `skills/base.md` already says this server does not read the work tree and that
  the branch is the caller's. So the step instructs rather than routes to a tool
  this server would have to grow.
- That an extension audit usually runs where something waits unmerged. The one
  measurement is a repository with 23 open pull requests and 13 pushed branches,
  which is not evidence about the ordinary case.

## Wrong if

- Most audits run on repositories with nothing unmerged. Then the step costs a
  read per item and returns nothing. What it should have been is one question
  about the repository rather than a state on every item.
- A session drops a finding because a branch claims to fix it. An unmerged
  branch is a claim and not a fix. The checklist's *What a dropped candidate
  owes* is the bar it would have to clear.
- A recorded run follows the method the skill ends up with and reports a landed
  branch as outstanding, or an outstanding one as landed. That would say the
  method belongs where a release can correct it rather than in a published file.
- The next report of the same omission comes from `typo3-core-patch-review` or
  `typo3-extension-patch-review`. Then the rule belongs in `skills/base.md`,
  where every workflow reads it, rather than in the one skill the feedback asked
  about.

## Since then

The step exists and a measure of the method preceded it, against a fixture
repository with six branch shapes and this repository's own. The run's report
holds. `git cherry` calls a squash-merged branch's commits outstanding, and what
the branch is behind on dominates an unrestricted two-dot diff. The diff
restricted to the files the branch touches answers both correctly.

**What the run did not report is that only its empty answer settles anything.**
The restricted diff is symmetric. So a branch whose fix is already in the base
produces exactly the diff an outstanding branch produces. That happens once the
base edited one of its files afterwards. So the skill states the empty answer as
the mechanical half and sends the non-empty one back to the finding. `gh` is not
assumed, so the git half is the floor.
