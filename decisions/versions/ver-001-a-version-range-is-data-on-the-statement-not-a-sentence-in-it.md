---
id: D-VER-001
title: A version range is data on the statement, not a sentence in it
date: 2026-07-29
status: confirmed
---

# D-VER-001 — A version range is data on the statement, not a sentence in it

**`since` and `until` fields on a statement bind it rather than version words in
its sentence, and a bare string holds on every covered version.**

The knowledge base covers four TYPO3 lines, 12.4, 13.4, 14.3 and main, and until
now every statement held on all of them. That was the rule in `AGENTS.md`: no
version numbers, and where a fact is branch-specific, write the rule that holds
everywhere. It bought branch-neutrality and left out everything that is not.

## Decided

- Bind the statement, not the hint, and bind it with `since` and `until` fields
  rather than words in the sentence. A subsystem does not change wholesale. One
  sentence in it does, and the other six stay as they are, so per-hint versions
  would duplicate what did not change.
- A bare string stays a valid statement and means "holds on every covered
  version". The two hundred existing bullets are exactly that, so the model came
  in without a rewrite of any of them.

## Assumed

- Majors are granularity enough. `13.4` and `13.3` do differ, but the covered
  lines are one release line per major. A range that cannot express a minor is
  better than a range nobody maintains.
- A filter is better than a qualifier once the server knows the version. The
  answer leaves out a statement that does not hold rather than shows it with a
  warning. An answer the caller has to filter is an answer they will not filter.

## Wrong if

- A statement is true on 12.4 and 14 but not on 13. The range cannot say that,
  and it would have to become two statements.

## Confirmed on 2026-08-02

The **Wrong if** has not happened. A session read every bound statement against
the four checkouts and each truth set is contiguous. `bin/cli catalog:check`
says that mechanically for the derived half, and no check can say it for the
judged one. What the read found is the failure a range does express rather than
a hole. `extension-files` bound a fallback with no upper bound while the
statement beside it said it was gone, and that one gained its `until`.
