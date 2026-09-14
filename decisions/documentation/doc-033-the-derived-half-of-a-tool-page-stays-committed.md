---
id: D-DOC-033
title: The derived half of a tool page stays committed
date: 2026-08-14
status: open
coveredBy:
  - ToolSurfaceTest::everyPageIsWhatTheRegistryDeclares
---

# D-DOC-033 — The derived half of a tool page stays committed

**The derived half of `documentation/server/tools/` stays in the checkout,
because it is the whole documentation diff of most commits that change a tool.**

Reproducibility was the argument for a generation at render time, and it is not
the question. What decides it is what a reader takes the lines for.

## Evidence

- Measured on 2026-08-14 over the 28 pages: 9,011 derived lines against 8,815
  recorded ones. The derived lines are the head of each page, plus the whole
  page for the eight tools in `ToolCalls::derived()`.
- Over the last thirty commits that touched `src/Tool/`, 27 carried a page diff
  and every one of those touched the derived half. Nine touched the recorded
  half as well. So eighteen commits' entire documentation change lay in the
  derived half, and a generation would have left them with none.
- The diff comes from a command rather than a choice. `ToolSurfaceTest` holds
  every page to the registry, so a commit that rewrites a description and leaves
  the page as it is fails `composer ci`. Nobody types these lines and nobody can
  skip them.
- Somebody has read it at least once. `3501f48d` is a bugfix whose body states
  which hint ids the answer no longer offers and which 19 it now withholds. The
  1,063-line diff on `typo3_hint_lookup` is the only place that is visible.
- Whether anybody read the other twenty-six is not answerable here. This
  repository merges fast-forward and its five merge commits are all from
  2026-08-01 and 2026-08-02. So nothing records what somebody looked at before a
  branch came home. No feedback, decision or commit message reports a page diff
  that caught anything.

## Decided

- The maintainer, asked on 2026-08-14 with three answers priced: the derived
  half stays in git and the card closes. What remained was a want rather than a
  fact, which is why it went as a question rather than a settlement.
- Rejected: a generation of the whole derived half in `documentation:prepare`.
  It cuts through a page, which `D-DOC-016` decided against. The eighteen
  recorded pages would open at `Answered` with no heading and no label.
  `readme.rst` cannot go with them because its head stands hand-written above
  the generated listing.
- Rejected too: a generation of the eight wholly derived pages alone, although
  it keeps every other file a whole document. `typo3_hint_lookup` is one of the
  eight, so it is the answer that would have removed the one diff demonstrably
  read.
- The recorded half was never on the table. `tools:record` refuses without
  `.checkouts/`, and this repository is the only place that evidence exists.

## Assumed

- The cost of them is the review diff and nothing else. One command rewrites
  them and `composer ci` catches a session that forgets. So a stale page shows
  up before the commit rather than in front of a reader.
- 9,011 lines in a directory nobody edits do not get in the way of a diff that
  matters. Nothing measured says they do, and nothing measured says they do not.

## Wrong if

- Somebody reports that they skipped a tool commit's review because the diff was
  mostly generated. Then the lines are in the way of exactly the review they
  exist for.
- A page diff is never again the only place a behaviour change is visible.
  `3501f48d` is one instance, and one instance is what this rests on.
- The derived half grows enough that `bin/cli tools:index` is no longer a cheap
  thing to run before a commit. That is the point at which a forgotten run
  becomes the ordinary case rather than the exception `composer ci` catches.
