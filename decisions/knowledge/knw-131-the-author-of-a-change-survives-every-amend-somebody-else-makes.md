---
id: D-KNW-131
title: The author of a change survives every amend somebody else makes
date: 2026-08-27
status: confirmed
restsOn: [D-KNW-129]
coveredBy:
  - KnowledgeTest::aPatchSetOnSomebodyElsesChangeSaysWhatItOwesThatAuthor
---

# D-KNW-131 — The author of a change survives every amend somebody else makes

**The author line of a core change is never overwritten, whoever amends it.**

`D-KNW-129` built the route to open a patch set on somebody else's change. The
page it wrote measured what `git commit --amend` does: the author stays, the
committer moves to whoever pushed. Beside that it kept
`git commit --amend --reset-author` as the overwrite, with the Contribution
Guide's reading that a change reworked past recognition may take it.

## Evidence

Put to the maintainer on 2026-08-27, with the page and both skills written and
the branch held back for the review `writing-a-skill.rst` asks for. The answer
was that the author should not be overwritten and always stays.

That is a rule about attribution rather than about git. A patch set is
somebody's improvement of a change that stays its author's. An amend that takes
the author line turns a contribution into a substitution. The history then says
whoever last touched a change wrote it, which is the one thing the committer
line already says correctly.

## Decided

- The exception goes. The author of a change stays its author whatever the
  proportion of the patch that is now somebody else's. What a contributor gets
  for their work is the committer line and the comment they leave on the change.
- The page still names `--reset-author`, because a command nobody names is one
  somebody reaches for. It names it as the thing this workflow does not do.
- `typo3-core-patch-development` says it at the push, which is the only moment
  somebody could type the flag.

## Wrong if

- A session reports a change reworked so completely that the author line reads
  as false to its own author. The author asks for it to move.
- The Contribution Guide changes what it says about `--reset-author`, which a
  session reads from `.checkouts/` rather than from this entry.

## Confirmed on 2026-08-27

The maintainer settled it the day the route came, before the merge of the branch
that carries it. So nothing shipped under the read it replaced.
