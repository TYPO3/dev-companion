---
id: R-GUI-001
title: "A guide's checks describe the draft it returned"
status: held
heldBy:
  - CommitMessageTest::aPlaceholderHandedBackIsStillAnUnansweredField
  - CommitMessageTest::aTrailerTheDraftCarriesIsNotAlsoReportedAsMissing
  - CommitMessageTest::neitherPlaceholderCouldBeReadAsAnAnswer
  - CommitMessageTest::theDraftNeverCarriesAReleaseTheCallerDidNotName
---

# R-GUI-001 — A guide's checks describe the draft it returned

**The checks a guide returns describe the draft it returns.**

The checks never report a trailer the tool adds itself as absent. What the draft
cannot know it carries as a placeholder rather than as a default. A placeholder
handed back in a message to check is that same unanswered field, and the checks
report it again. The returned draft still carries it, so the checks still name
it.

## From

`Releases: main` appended and `missing-releases` warned in the same answer
(2026-07-29).
