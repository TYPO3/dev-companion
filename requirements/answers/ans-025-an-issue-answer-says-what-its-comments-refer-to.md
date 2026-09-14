---
id: R-ANS-025
title: 'An issue answer says what its comments refer to'
status: held
restsOn: [D-ANS-057]
heldBy:
  - ForgeTest::theFilesHangingOffAnIssueAreNamed
---

# R-ANS-025 — An issue answer says what its comments refer to

**An issue read whole names the files on it, so a comment that consists of
references to them does not read as an empty comment.**

Redmine writes an inline image as `!name.jpg!`, and that is what a comment about
a render defect is largely made of. Handed back as prose alone it is a bare
filename with no file anywhere in the answer. The evidence the report turns on
is invisible to a caller who never learns it exists.

## From

A triage of #88556 read the text of eleven comments and none of the seven
attachments (`feedback/2026-08-05-033846`). Two of them changed the outcome. The
2019 report still happens, and the 2023 comment filed under the same number does
not reproduce at that layer. Without them the session would have filed one wrong
verdict for both.

## Held by

- The sentence beside the list, which says what `!filename!` in a comment means,
  is not guarded. The tool renders it, and no test here drives one that reaches
  the tracker.
