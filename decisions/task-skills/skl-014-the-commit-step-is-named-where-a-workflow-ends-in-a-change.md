---
id: D-SKL-014
title: 'The commit step is named where a workflow ends in a change'
date: 2026-08-04
status: open
coveredBy:
  - SkillTest::theCommitStepIsNamedWhereASkillsWorkflowEndsInAChange
---

# D-SKL-014 — The commit step is named where a workflow ends in a change

**A published skill whose workflow ends in a change to the repository names
`typo3_commit_message_guide` with `workflow="project"`. The routing entry for
the commit message names that argument.**

Three of the four channels that could carry the step already do, and the fourth
is the one an extension author arrives through. A session that fixes a bug in
its own extension reaches the commit from its own habits. This server may take
no credit for a message that conforms out of those habits.

## Evidence

- **The run.** `feedback/2026-08-04-012644`, `/home/benji/projects/syntax`,
  `bk2k/syntax` 5.0.0 on TYPO3 14.3.0 under DDEV. This server ran on stdio with
  all 26 tools and the nine published skills in the session's context. Told to
  reproduce a frontend defect, fix it and commit it, it made 37 calls, every one
  of them Bash, Read, Edit or Write. It called none of the tools and activated
  no skill. The message that landed conforms; it came from the session's own
  habits, so it is evidence about the model and not about this server.
- **The fourth channel, read in this checkout on 2026-08-04.** Only
  `skills/typo3-core-patch-development` and `skills/typo3-core-patch-review`
  name `typo3_commit_message_guide`, and both open with the core's Gerrit
  workflow. None of the seven skills for extensions carries a commit step at
  all. The single line about a commit anywhere in them,
  `skills/typo3-extension-testing/references/static-quality.md`, says to keep a
  format pass in its own commit and routes nowhere.
- **The other three carry it.** Driven over stdio the same day,
  `typo3_task_guide` with this feedback's own task text and
  `Configuration/Sets/Base/setup.typoscript` names the guide twice. Once in the
  checklist and again under "Next lookups for this task", both times with
  `workflow="project"` and with the reason the default is wrong there. The
  default itself stays. `changeType` and `summary` without `workflow` answer
  `Resolves: #ISSUE_NUMBER`, `Releases: RELEASE_TARGET` and
  `ERROR: A Forge issue is required`, and close with the sentence that names
  `workflow="project"`. `knowledge/server-scope.json` names the argument in the
  covered topic "Commit messages" and not in its routing entry "Writing or
  amending the commit message".
- **The ladder stops at step 2.** `bin/cli hints:probe` on the feedback's own
  query reaches `backend-typescript`, `backend-ui` and `language-files`. Those
  are the domains its words happen to spell, since the order of two Prism
  plugins is not a TYPO3 subject. Nothing about the commit step is absent from
  `knowledge/`. It is in a tool the session never called, which is delivery.
- **One report, and no sibling.** `bin/cli feedback:list` on 2026-08-04: 10 open
  across three directories, eight of them from `ext-guidedtour` and one from
  `typo3-cms`. This is the only card from `/home/benji/projects/syntax` and the
  only one written by `claude-opus-5`.
- **`D-GUI-002` has been waiting for exactly this run.** Its **Wrong if**,
  agents commit in a project repository and never pass the argument, dates from
  2026-07-29. Its first claim stood open until this session. It names the seven
  skills as the worst of the four channels.

## Decided

- **Placement, not a new capability.** Step 2 of the ladder in
  [judging.md](../../documentation/records/judging.rst): the answer exists and
  the route to it does not pass where this task passed. Nobody builds a tool or
  a `knowledge/` entry.
- **In the skill body, not in `skills/base.md`.** That file is the order every
  task *starts* in and all nine skills carry a copy of it. So a commit line
  there would repeat in the two core skills what their own sections state. Those
  are "Commit and push" and "Commit shape and target branch". `D-SKL-013`
  settled the same fork the same way. The side that reaches a caller who arrived
  without a skill is the tool's answer, and that side already carries the step.
- **The routing entry gains the argument**, so the one place outside a skill
  that names the step says which workflow the caller is in.
- **A read decides which of the seven get it, not this entry.** A workflow that
  ends in a review changes nothing and commits nothing, and each skill's own
  body is what says which it is.

## Assumed

- That a session which loads one of those skills reads the closing step. Nothing
  measures how far into a skill a session gets. `D-AUD-003`'s **Confirmed on**
  is the one read there is, a run that activated the skill and followed it to
  step 2 of five.
- That a tool named at the end of a body is routing rather than a second copy of
  what the tool owns. That is what the two core skills already do.

## Wrong if

- A second session fixes something in an extension and commits, with the step in
  the skills, and activates no skill again. Then the skill channel is not the
  route this task takes, and what remains to suspect is the descriptions a
  client chooses each skill on. No published skill describes how to reproduce
  and fix a reported defect in an extension. The `typo3_task_guide` brief for
  that task text names no skill either.
- A skill that only reviews gains the step. A review changes nothing,
  `R-GUI-006`. A commit line in it is the patch checklist that entry exists to
  keep out of a review's answer.
- The routing entry changes nothing because nobody reads it. It sits behind
  `typo3_server_scope`, and `D-AUD-003` measured what that costs. A caller has
  to call a tool to learn that it should call tools.

## Since then

Implemented on 2026-08-04. A read of each body found where its own workflow
ends, and the five that end in files take the step in every case. The
unpublished draft carries it into its review. The two core skills stay as they
were, since the project workflow in either would drop the rules that hold a core
patch.

The second **Wrong if** fired the same day: a skill that only reviews had gained
the step, because its own body carried an improvement branch. The maintainer's
answer is that the branch should not have been there. The first of the three
places was the one no body could have corrected. The `description` opened
"Review, audit, or improve", which is the line a client selects on. A skill of
its own (`D-SKL-016`) answers what that opens rather than this entry. The entry
stays open on the first **Wrong if**, which is behaviour nobody has watched.

## Since then

A session read the last bullet, that a read decides which of the seven get the
commit step, once more on 2026-08-19. The answer changed because the file did.
`typo3-extension-cleanup` and `typo3-extension-conformance` became one skill. So
the workflow that ended in a review and the one that ended in a change are the
same one, `D-SKL-064`. The step is in it, after the gate its report half closes
on. What holds the placement is an order rather than the absence that held it in
the audit.
