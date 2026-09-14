---
id: D-KNW-129
title: Opening a patch set on somebody else's change is a subject this server owns
date: 2026-08-27
status: open
coveredBy:
  - KnowledgeTest::aPatchSetOnSomebodyElsesChangeSaysWhatItOwesThatAuthor
  - KnowledgeTest::aSearchWhoseMatchesAreAllInOnePageAnswersWithThePage
  - SkillTest::extendingSomebodyElsesChangeIsAWayIntoTheCheckout
---

# D-KNW-129 — Opening a patch set on somebody else's change is a subject this server owns

**The corpus states what a patch set opened on somebody else's change owes. What
the amend does to authorship, and what the upload says to its author.**

A user asked a session twice to put local work on top of an open Gerrit change
and push it back. The session found the one sentence in reach that rules the
case out, and worked the mechanics and the etiquette out on its own.

## Evidence

- The probe misses. `bin/cli hints:probe` with the feedback's own task reaches
  `site-sets` and nothing else. That task is to extend somebody else's open
  Gerrit change with local work and push a new patch set. The curated vocabulary
  admits that hint rather than what it says.
- The page that carries the procedure stops at the author's own change.
  `knowledge/documents/core/contribution/gerrit-workflow.md` has **Update an
  Existing Patch**: amend rather than add a commit, keep the `Change-Id`, fetch
  the current patch set first. Nothing in it says what changes when the commit
  belongs to somebody else.
- The two published skills disagree about whether the case may happen at all.
  `typo3-core-patch-development` routes it: *"Somebody else's change picked up
  to be finished arrives that way"*.
  `skills/typo3-core-patch-checkout/references/checklist.md` says a push of the
  carried state *"would be opening a patch set in somebody else's name — that
  belongs to the workflow that owns amending a change, and only where the change
  is yours to amend"*. The session read the second.
- No intent names the task. `amend` occurs once in
  `knowledge/task-intents.json`, inside the `submission` checklist, and on no
  intent's `match` or `matchWeak`.
- The intent a session does reach declares the task changes nothing.
  `patch-checkout` carries `changesNothing: true`. So a request that lands on it
  by `patch set` or `cherry-pick` gets a brief that routes only the workflows
  that change nothing,
  [`D-SKL-039`](../task-skills/skl-039-a-brief-that-changes-nothing-routes-only-the-workflows-that-change-nothing.md).
- The three ways in the checkout skill offers all end with the patch read or
  tried. Onto the branch it targets, into a worktree beside it, or onto current
  code as a commit of this session's own. Local work layered onto the patch set
  is none of them.
- What the session did instead is the whole of the report. It stashed against
  the skill's rule because the user asked for it. It transcribed the files whose
  two bases it had verified identical and resolved the rest by hand. It
  established that `--amend` keeps the author and moves the committer. It ended
  on `review/95369` with an amended commit, an end the skill's mandatory undo
  does not allow for.

## Decided

- **Step 1a, taken on.** The answer is not here in any form, and the sentence
  nearest to it forbids the task. What the section says is a reading of TYPO3's
  own process rather than of this repository, so the reading is the card's first
  step —
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md)
  does not reach it, because this run made no such lookup.
- The mechanics land in
  `knowledge/documents/core/contribution/gerrit-workflow.md`, beside **Update an
  Existing Patch**, and not as a fourth way in with its own commands. The
  checkout skill says why in its own second paragraph. The refs, the remotes and
  the commands are lookups. A copy of them in a skill goes stale in somebody
  else's project with nothing to report it.
- The skills carry the routing and the stopping rule. The checklist's clause now
  routes rather than forbids. It is the sentence that stopped the session and it
  disagrees with the other skill in the same install.
- One case rather than three. The clean work tree the checkout skill demands,
  the end it does not allow for, and the absent mechanics are one task. Those
  are three views of it. Local work is this way in's material rather than its
  obstacle, and work that continues on the review branch is how it ends.
- `normal`, not `low`. One session reported it, which is not what raises a card.
  What raises this one is that two published files contradict each other about
  it, and that a session followed the one that was wrong.
- `coveredBy: []`, because what would hold this is an assertion over the
  document and the skills, and the commit that writes them writes it.

## Assumed

- That a patch set uploaded onto another author's open change is TYPO3 practice.
  The feedback states it and this run has not read it anywhere. The route rests
  on it entirely, which is why it is the first thing the card establishes.
- That the case is a section of the page rather than a document of its own. The
  page already carries the procedure end to end, and a second document would
  split a reader who was told to read this one whole.

## Wrong if

- The contribution guide answers a foreign change with a comment rather than
  with a patch set. Then what the corpus owes is the etiquette and a stop rule,
  and the checklist's clause was right for the wrong reason.
- A session reads the new section and opens a second change anyway. Then the
  `Change-Id` half was delivered and did not take, and the lever is wording
  rather than the corpus.
- The route arrives and the clean work tree rule stops the next session all the
  same. Then the obstacle was the skill's precondition and the section behind it
  changed nothing.
- Everything the case needs turns out to be one sentence in the section that is
  already there. Then this made a section out of a sentence, and the cost falls
  on a reader who was told to read the page whole.

## Since then

A session read the assumption and it holds. The contribution guide answers a
foreign change with a patch set and asks a reviewer to push a fix rather than
vote against it. The review server grants that push to registered users by
default. A scratch clone settled the rest — the amend keeps the author, the
identifier survives single and unchanged, and a cherry-pick answers the same
way. So the first **Wrong if** has its answer and the other three need a session
in the field.

One bullet of the evidence was a misread and stands rather than gets an edit,
because it is what the judging session read. The review the skill rules ask for
happened before the merge and returned one correction. That is the first skill
here with a review before it shipped rather than after.
