---
id: R-FBK-005
title: 'A feedback is attributed to the model that left it'
status: held
heldBy:
  - FeedbackTest::aNoteSaysWhichModelLeftIt
  - FeedbackTest::aNoteWithoutAModelSaysSo
---

# R-FBK-005 — A feedback is attributed to the model that left it

**A feedback carries the model that recorded it, and one without an attribution
says so rather than carries nothing.**

Much of what arrives is not about an answer but about a session. A skill whose
steps the session loaded and did not run, a tool nothing reached for, an order
the session read and then inverted. That is behaviour, and behaviour belongs to
one model. Without attribution, two models' habits arrive as one report. Then
nobody can ask the question the feedback poses: is the instruction absent, or
was it present and not followed.

The channel asks for the model rather than infers it. The handshake names the
client, not the model behind it. The write never fails on it, because a feedback
is worth more than its attribution. A model that does not know its own
identifier sends `unknown` rather than an invented one; an attribution nobody
can trust is worse than none. So the channel always writes the field, `unknown`
included, which is also what a feedback recorded before the field existed reads
as.

## From

The feedback of 2026-07-31 17:21, which reported that the session loaded the
conformance skill's instructions and did not run its lookups. It came from a
site package whose published skill was byte-identical to this repository's, base
and checklist included. So the gap it named was not in the text the session had.
The one thing that would have made it actionable was the one thing the feedback
could not carry. That is which model read those steps and walked past them.
