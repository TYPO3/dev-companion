---
id: D-KNW-133
title: A guide's whenToUse names the answer it hands over
date: 2026-08-28
status: open
readings:
  - 2026-09-01
---

# D-KNW-133 — A guide's whenToUse names the answer it hands over

**A `whenToUse` says what the page puts in the caller's hands, because that is
what decides whether the call is made at all.**

`D-KNW-057` settled that a document declares when to reach for it. What that
declaration has to say is the half a session has now paid for: a line describing
the page's method reads as a page about the method, and the caller solves the
question elsewhere.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-001428`](../../feedback/archive/2026-08-28-001428-two-routes-this-server-offered-and-the-session.md).
  The package declares `^13.4 || ^14.3` and only 14.3.6 is installed. The
  question was whether `GeneralUtility::getFileAbsFileName()` resolves a
  relative path the same way on 13.4.
- **The route was named twice and not taken.** Both the
  `typo3-extension-patch-review` skill and `typo3_project_describe`'s own
  `guides` array named
  `extension/compatibility/a-declared-major-that-is-not-installed` for exactly
  this question. The session fetched 13.4's `GeneralUtility.php` from
  `raw.githubusercontent.com` instead.
- **It says why, and the reason is the line.** It read "It names the reading
  that settles the question; the shape itself is read from the branch" as a page
  about obtaining a branch, "and I had network and a single symbol".
- **The page answers the single symbol.** Read here on 2026-08-28: it hands over
  `git cat-file -e <branch>:…`, `git grep -n "function <name>" <branch> …` and
  `git show`, says the diff is the one call where a whole subtree is the
  question, and says that where no checkout is at hand the file is read out of
  that major's released package — "where two or three symbols are the whole
  question, reading those files alone is the smaller step".
- **So the page was one rung cheaper than the route taken**, and the declaration
  is what hid it.

## Decided

- The `whenToUse` now names the invocation and the fallback: one git call
  against the branch, or the released package where no checkout is at hand. The
  reporting session's own wording asked for exactly that.
- **Closed on the spot.** The line is a declaration in `knowledge/`, the page it
  describes was read in this run, and nothing about TYPO3 was looked up.
- Against the report's other branch — a tool taking a symbol and a declared
  major — for now. It is the answer where the page turns out not to hand over a
  reading, and the page does.
- The second half of that feedback is not this: the deprecation sweep's
  exemption is in `skills/base.md`, which is a contract and waits.

## Assumed

- That a session which reads a `whenToUse` naming a command makes the call. The
  same session made every other call the same array named.

## Wrong if

- A session reports calling this document for a whole-subsystem question and
  getting three git invocations where it needed the shape of a subtree. Then the
  line now oversells the page in the other direction.
- Another `whenToUse` is reported the same way. Then the finding is the field
  across the corpus rather than this one line, and what is owed is a sweep of
  all of them.
