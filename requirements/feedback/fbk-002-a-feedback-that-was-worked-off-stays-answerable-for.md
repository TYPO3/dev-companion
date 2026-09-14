---
id: R-FBK-002
title: 'A feedback that was worked off stays answerable for'
status: held
heldBy:
  - FeedbackTest::aNoteThatWasWorkedOffIsStillAnswerableFor
  - FeedbackTest::aNoteThatWasWorkedOffKeepsEverythingItSaid
---

# R-FBK-002 — A feedback that was worked off stays answerable for

**The archive keeps a feedback a session worked off, and it says what came of
it.**

To close one is to move it to `feedback/archive/`, not to delete it. The agent
that recorded a deleted feedback saw only that the file was gone. That reads as
lost, so the agent reports the same gap again and drops a request that needed a
code change in silence. `typo3_feedback_list` reads the archived feedback back
whole, with the commit that archived it as the answer. The feedback a session
worked off before the archive existed carry that commit in their own front
matter. One commit moved them all and says nothing about any of them.

The kept file is also what makes the closed half filterable at all. A feedback
read out of a commit was a filename, and the category, the tools and the model
it carried went with the file.

## From

Seventeen feedback recorded over two sessions, of which the store showed three,
and a re-report of a request that had shipped in the meantime (2026-07-29). A
read of the commit was that answer; what it could not give back was everything
the feedback itself said (2026-08-01).
