---
id: D-ANS-127
title: Knowledge that yields a judgement is delivered where the work finishes
date: 2026-09-01
status: open
readings:
  - 2026-09-02
coveredBy: []
---

# D-ANS-127 — Knowledge that yields a judgement is delivered where the work finishes

**A document that yields a judgement gets its name where the work ends. A
session fetches only the documents that yield an artifact at a step's start.**

This server offers every document it hands over the same way. That is an
`availableHints` index and a `documents` array, both read at the start of a
step.

## Evidence

- One session on 2026-08-31 read its own transcript and reported the split. It
  fetched `extension/testing/phpunit` and `project/testing/playwright`, which
  produce two XML files and a spec. It fetched neither
  `any/testing/browser-check` nor the six `extbase` hints, which produce a
  decision about what already exists. Both were in answers it had received.
- The same session filed six further reports that are instances of it. The
  content-element skill fired 12 seconds after the user wrote "bitte lies nach".
  The first backend screenshot came 78 seconds after the user asked how the
  session had verified the work, three and a half hours in. The session reported
  a rename complete on 107 green tests while 118 records carried a CType nothing
  declared.
- What it did fetch, it fetched well. The `documents` array is the only reason
  the session found the phpunit guide, and that call is the one the session
  names as its best. So the channel works and the moment is wrong.
- The corpus already states the practice for one subject.
  `any/testing/proving-a-condition` carries the negative control that makes one
  result evidence, for a TypoScript condition. Nothing states it for a browser
  suite, where two of that session's tests passed because their assertion could
  not fail.
- Read on 2026-09-01: no skill body carries a `documentId`. So the sentence a
  workflow states at the moment it applies is a sentence, and nobody can fetch
  the page behind it from there.

## Decided

- The asymmetry is real and is this server's to answer, so this entry records it
  rather than six separate wording notes.
- What follows from it is placement, not new prose. A judgement document gets
  its name at the step that finishes something, in a skill, beside the sentence
  that already asks for it. Its `documentId` stands there, because a session can
  fetch a `documentId` and not a sentence.
- The cards this judgement left name the places one at a time; this entry says
  they are one finding.
- Rejected: a mark "yields a judgement" on a document in the `documents` array
  alone. It is cheap and it is still delivered at the start, which is the half
  that failed.

## Assumed

- That one session that read its own transcript is evidence about more than that
  session. It measured rather than recalled, and it corrected itself once in the
  same debrief, which is what the evidence rests on.
- That a session reads a skill at the step it names. The same session skipped
  two crossings written as steps, so this is the assumption most at risk.

## Wrong if

- A session fetches a judgement document from a skill and the work still ships
  with no look at it. That would say the moment is not the lever and the
  practice is.
- The split turns out to be one client's order of a large answer rather than a
  property of what the entry yields. The same session reported that it read a
  `documents` array top-down for what it came for, which is the rival
  explanation and stays open here.
