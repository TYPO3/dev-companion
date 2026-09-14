---
id: D-ANS-057
title: 'What hangs off an issue is named'
date: 2026-08-05
status: open
coveredBy:
  - ForgeTest::theFilesHangingOffAnIssueAreNamed
---

# D-ANS-057 — What hangs off an issue is named

**A single-issue read answers with the files attached to it — name, type, size,
date and URL — and fetches none of them.**

On a report about a render defect the evidence is a screenshot, and Redmine puts
it into a comment as `!name.jpg!`. The text of such a comment is a bare filename
that refers to something the answer never mentioned exists.

## Evidence

- `feedback/2026-08-05-033846`: #88556 carries seven attachments, and two of
  them decided the triage. One showed the editor's source view, the other the
  second reporter's literal database content, which did not reproduce at the
  layer the first one did.
- Without them the session would have taken "the problem still exists in
  12.4.27" at face value and filed one verdict for two different defects. Its
  words: a text-only read of this issue is actively misleading rather than
  merely incomplete.
- The comment that carries the decisive evidence reads, in full, as three
  filenames and the words around them. Nothing in the previous answer said a
  file existed.
- `include=attachments` is one parameter on the read the tool already makes, and
  the `content_url` it answers with needs no credential. Measured against
  forge.typo3.org on 2026-08-05: seven files, with type, size and upload date.

## Decided

- The list and not the bytes. A caller that can read images reads an image, and
  a server that transcribed one would answer for what it saw.
- The upload date comes with each file, because that is what says which comment
  a filename in prose belongs to. Seven files uploaded on two days across four
  years is two pieces of evidence, not seven.
- The answer says what the `!filename!` syntax is, next to the list. A caller
  that has both still has to connect the bare filename in a comment to the file
  above it.
- No fetch, and no attachment tool. What a caller does with an image is the
  caller's, and this server reaches the tracker for what the tracker knows.

## Assumed

- The caller can read an image. Where it cannot, the list is still what says the
  comment above it is not empty, which is the failure mode the feedback
  reported.
- Attachments are worth their space in the answer. An issue with forty of them
  would push the comments down the page, and no measurement covers one.

## Wrong if

- A session reports the list as noise on issues where the evidence is textual.
- An issue with many attachments crowds out the notes, which are what carries
  the decision.
- The download URLs no longer answer without a credential, which would make the
  list an offer this server cannot keep.
