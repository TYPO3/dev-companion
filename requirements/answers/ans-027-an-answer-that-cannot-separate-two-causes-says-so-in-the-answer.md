---
id: R-ANS-027
title: 'An answer that cannot separate two causes says so in the answer'
status: held
restsOn: [D-ANS-062]
heldBy:
  - GerritTest::aReviewNoteOnTheIssueTurnsTheHedgeIntoAnAnswer
  - GerritTest::anEmptyAnswerForACommitSaysWhatItCannotSeparate
  - GerritTest::anEmptyAnswerForANamedChangeSaysWhatItCannotSeparate
  - GerritTest::anEmptyBacklogSaysWhatItCannotSeparate
---

# R-ANS-027 — An answer that cannot separate two causes says so in the answer

**Where a lookup names one record and finds nothing, the answer says which two
causes it cannot separate: an absent record and an unreadable one.**

A client reads the tool description at install; a session reads the answer as it
writes the verdict. A status word that overstates what the call established acts
as an established fact, and nothing downstream can tell it from one.

## From

`feedback/2026-08-07-132416`, 2026-08-07. `typo3_gerrit_lookup` answered `empty`
for a Change-Id taken out of the commit under review. It answered `unavailable`
with `source-not-answering` for the same change read by number. Both were an
anonymous read of a private change. The review made "this was never pushed" its
first finding and recommended coordination with an author who did not exist as a
separate party.

**Measured on 2026-08-07.** Asked directly, the review server answers
`change:95162`, the change the report is about, with `200` and `[]`. That is the
same as a change number that exists nowhere. The two really are one answer. The
`source-not-answering` the report saw beside it was a call with no answer rather
than a second shape of the same cause.

**Since 2026-08-07 one of the two is separable**, on the issue side. Gerrit Code
Review posts a note on the tracker for every patch set it receives. So an empty
search plus a review URL there is not two possibilities: the change exists and
this reader may not see it. The answer says that instead of a hedge, and it asks
the tracker only on the empty path, 0.12 seconds measured. The side the report
was about stays a hedge. A tracker search for a change number costs 2.5 seconds
and answers two issues, one unrelated, and a search for a Change-Id answers
nothing.
