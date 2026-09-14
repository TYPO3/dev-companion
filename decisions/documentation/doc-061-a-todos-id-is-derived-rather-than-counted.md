---
id: D-DOC-061
title: "A todo's id is derived rather than counted"
date: 2026-08-27
status: open
coveredBy:
  - CliTest::aBranchIsTheTodosIdAndNothingElse
  - CliTest::aTodoIsNamedByItsIdAndNotByTheWorktreeHoldingIt
---

# D-DOC-061 — A todo's id is derived rather than counted

**A citation names a todo as `T-<yymmdd>-<hash>`, which is also its filename
prefix.**

A filename of seventy characters is not something a commit message can name, and
an id from a count of what exists collides between worktrees.

## Evidence

- Two branches cut from the same `main` both wrote `D-ANS-114` on 2026-08-27.
  Each took the next id from a count of the entries it could see. One became
  `D-ANS-115` by hand at merge time.
- Todos go out in batches, three that run, so a counted id collides more often
  than a decision's does, not less.
- The cards from that run carry `2026-08-27-001800` and `2026-08-27-001900`.
  They are a minute apart by luck: `typo3_feedback_record` writes a card per
  feedback, and two agents that record in one second collide.

## Decided

- **The date orders and the digest separates.** `<yymmdd>` is what
  `todo/readme.md` reads the queue by after the priority in each head. The
  digest of the creation instant, the slug and a few random bits is what two
  writers in one second cannot both produce.
- **The id is the filename prefix.** The date stands once rather than beside a
  hash, and `2026-08-27-001900` goes.
- Rejected: a counter. A number from a read of what exists is what put
  `D-ANS-114` on two branches. It is the half of an issue number that does not
  survive parallel work.
- Rejected: an issue number from GitHub. It cannot collide and it is visible off
  this machine. But the queue would no longer be readable from the checkout
  alone, which is most of what `todo/` is for.

## Assumed

- That six characters of digest are enough for a queue of this size. There a
  collision is two cards written in one second whose slug also matches.

## Wrong if

- The hash turns out to be what nobody cites, because the slug is what a person
  reads and `T-260827-a3f9` is what only a command uses.
- The same argument applies to `decisions/` and `requirements/`, whose ids come
  from a count and are what this entry's evidence comes from.

## Since then

The first **Wrong if** landed the other way up. It expected the hash to be what
nobody cites. What happened is that the id is what every command takes while the
slug is what none of them does. The maintainer read `git worktree list` during a
three-todo run and could not tell which todo each held. So the slug goes rather
than moves behind the id. Rejected: the slug kept, because the name would move
when somebody retitles the todo. Two todos with one slug would derive one
branch, which reads as work somebody has in hand. The rename waited for that
run's worktrees to come home. A derivation that moves while one is up offers
work already in flight a second time.
