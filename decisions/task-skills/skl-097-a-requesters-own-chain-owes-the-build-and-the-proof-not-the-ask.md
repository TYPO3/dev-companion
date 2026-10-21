---
id: D-SKL-097
title: A requester's own chain owes the build and the proof, not the ask
date: 2026-10-21
status: open
coveredBy:
  - SkillTest::aRebaseOfOnesOwnChainReachesTheCheckoutAndSaysWhatStays
---

# D-SKL-097 — A requester's own chain owes the build and the proof, not the ask

**`typo3-core-patch-checkout` says what a rebase of the requester's own chain
keeps and what falls away. The ask, the comment and the stop at a conflict fall
away. The target branch, the build per link, `git range-diff`, the sweep, the
checks on the new base and the push stay.**

## Evidence

- One debrief of 2026-10-16, a rebase of the 13-change chain 95425..95437 onto
  `main` with the build output rebuilt in every commit, then the push.
- [`feedback/2026-10-16-074733-the-checkout-skill`](../../feedback/archive/2026-10-16-074733-the-checkout-skill-has-no-short-path-for-the.md).
  The skill said the owner rule "has nothing left to protect" and nothing
  about the rest. The session skipped the sweep, the handover for the push and
  `typo3_test_run_guide`, and reported none of it. The brief named the
  development skill alone, as a submission.
- [`feedback/2026-10-16-074733-no-procedure`](../../feedback/archive/2026-10-16-074733-no-procedure-rebuilds-the-committed-build.md).
  No page rebuilt every commit of a chain. The session wrote a
  `git rebase --exec` script and checked the result with `git range-diff`.
- [`feedback/2026-10-16-074733-what-worked`](../../feedback/archive/2026-10-16-074733-what-worked-build-output-page-gerrit-workflow.md).
  The strengths. `committed-build-output` gave the clean baseline, the lock file
  and the orphan output. `gerrit-workflow` gave the fetch URL and kept the WIP
  state. `chainedAt` equal to `patchSet` on all 13 links said the chain was
  consistent. The measured lines made the session trust the steps.
- Read on 2026-10-21. The base already owes the sweep for a carry onto current
  code, and the development skill owes the checks on the new base. A scratch
  repository showed that an amend in a `--exec` step carries into the commits
  above it. `git range-diff` compares the message as well as the diff.

## Decided

- The section lists what stays rather than what may be skipped. A session that
  skipped three steps had no list to hold its work against.
- The sweep stays. The code under the chain moved, which is the case the base
  names. The skill says to narrow it by the tags of the extensions the chain
  touches.
- The push stays in `typo3-core-patch-development`, as its rebase section and
  the push. A chain whose every link came back `=` owes no new message,
  changelog entry or review.
- `committed-build-output` gains "Rebuilding Every Commit of a Chain", and the
  chain bullets of the checkout skill name it. The session's own measurement
  stands in it with its date.
- "rebase the chain", "relation chain", "rebase the patch" and "rebase the
  change" route to the checkout skill. The brief keeps its change items. A
  rebase writes commits, so it is no brief for work that changes nothing.

## Assumed

- That the requester's own chain is the common case of a chain rebase. The
  skill already says a chain "is regularly the requester's own".

## Wrong if

- A session rebases its own chain, reads the section and still skips a step it
  lists. Then the list is not the lever, and the report form of `D-SKL-074` is
  what remains.
- A session follows the brief's test coverage item on a rebase that adds no
  content. Then a rebase needs a brief of its own shape.
