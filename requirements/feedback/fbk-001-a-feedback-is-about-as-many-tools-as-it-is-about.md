---
id: R-FBK-001
title: 'A feedback is about as many tools as it is about'
status: held
heldBy:
  - FeedbackTest::severalToolsStaySeveralTools
  - FeedbackTest::theListCanBeRestrictedToOneTool
  - FeedbackTest::theRecorderStillTakesAListTheSchemaNoLongerDeclares
  - StdioServerTest::aListOfToolNamesIsRefusedWithTheTypeItWanted
---

# R-FBK-001 — A feedback is about as many tools as it is about

**A feedback is about as many tools as it is about.**

The names survive the record as names, the listing shows them as a list, and a
caller can filter the store by one of them. The obvious question to ask of it is
what is open about one tool.

They arrive as one string, with commas between them. The declared argument is a
plain `string` since `D-ANS-017`, and the channel refuses a list on the wire.
That is about what a client can compose, not about what a feedback may say, and
this holds either way.

## From

Four tool names recorded as one unsearchable word, because the field stripped
everything that was not `[a-z0-9_]` (2026-07-29).
