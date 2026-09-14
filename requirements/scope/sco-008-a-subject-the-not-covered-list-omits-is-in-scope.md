---
id: R-SCO-008
title: A subject the not-covered list omits is in scope
status: held
heldBy:
  - ScopeTest::aTopicWithAHintIsNotDeclined
  - ScopeTest::noExclusionDeniesASourceTheServerReads
  - ScopeTest::theDeclaredInterpreterIsNotDeclined
---

# R-SCO-008 — A subject the not-covered list omits is in scope

**The declared scope says what the server does, and the not-covered list is
exhaustive.**

A subject that is not on it is in scope, so a thin answer to it is a gap rather
than a boundary.

The two ask for opposite reactions from a caller: leave for the documentation,
or say what was absent. Nothing else in an answer tells them apart.

A subject that is only partly outside names the covered part where it declines
the rest. The topic line cannot say which half a caller holds, so a subject left
whole reads as excluded whole.

## From

`doesNotCover` still excluded "project or third-party extension development" and
"upgrading an installation" while both had hints of their own. A session
reported that as a signal that cost confidence rather than time (2026-07-29).
