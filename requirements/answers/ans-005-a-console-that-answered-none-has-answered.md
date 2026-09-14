---
id: R-ANS-005
title: 'A console that answered "none" has answered'
status: held
heldBy:
  - LabelSearchTest::aConsoleThatCannotRunIsStillUnanswered
  - LabelSearchTest::aConsoleThatFoundNothingIsAnAnswer
---

# R-ANS-005 — A console that answered "none" has answered

**A console that ran and answered "none" is an empty answer.**

The `unsupported` answer belongs to a console the server could not reach or that
failed. This is
[R-ANS-001](ans-001-could-not-ask-never-looks-like-does-not-exist.md) in the
other direction. A zero-hit answer dressed as a breakage sends the caller to fix
an installation instead of narrow a query.

## From

The same feedback; the agent read the console's zero-match warning as an
unreachable installation (2026-07-29).
