---
id: R-FBK-016
title: 'A field that arrived carrying its call is refused'
status: held
restsOn: [D-FBK-044]
heldBy:
  - FeedbackTest::aFieldCarryingTheCallItArrivedInIsRefused
  - FeedbackTest::aReportQuotingTheMarkersIsStillRecorded
---

# R-FBK-016 — A field that arrived carrying its call is refused

**The channel refuses a recorded field that carries the frame of its own call,
with what to change in the message.**

Such a field is not a report but the rest of the call, folded into the argument
before it. What follows the bad close never arrived as an argument at all, and
nothing else can tell afterwards that the session wrote a suggestion.

## From

34 of 270 feedback with the frame, in two batches: 2026-07-29 and the 14 of
2026-08-04 17:58 to 18:02. That session made 456 tool calls and mangled only
these.
