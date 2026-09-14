---
id: R-FBK-006
title: 'A recorded feedback is reported where it actually is'
status: held
heldBy:
  - FeedbackTest::theRecordedNoteIsReportedWhereItActuallyIs
---

# R-FBK-006 — A recorded feedback is reported where it actually is

**The answer reports the path of a recorded feedback as an absolute path.**

The answer says the feedback went into this server's own checkout rather than
into the project the session works in.

The caller stands somewhere else. A path relative to a root it has never seen
resolves, in the only directory it can check, to nothing. An agent that does
exactly what it should reads a write it cannot verify as a write that failed.
The file is there the whole time, one checkout over. To say which checkout is
the difference between a feedback recorded once and a feedback recorded twice.

## From

The feedback of 2026-07-31 17:23, recorded from a site package. The tool
answered `feedback/<name>.md`. The session searched its own workspace for that
path, found neither the file nor a `feedback/` directory, and reported the
creation as failed.
