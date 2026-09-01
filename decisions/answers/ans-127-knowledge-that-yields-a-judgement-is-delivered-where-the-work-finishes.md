---
id: D-ANS-127
title: Knowledge that yields a judgement is delivered where the work finishes
date: 2026-09-01
status: open
coveredBy: []
---

# D-ANS-127 — Knowledge that yields a judgement is delivered where the work finishes

**A document that yields a judgement is named where the work ends, because only
the documents that yield an artifact are fetched at a step's start.**

Every document this server hands over is offered the same way — in an
`availableHints` index and a `documents` array, both read while a step is being
started.

## Evidence

- One session on 2026-08-31 read its own transcript and reported the split: it
  fetched `extension/testing/phpunit` and `project/testing/playwright`, which
  produce two XML files and a spec, and fetched neither
  `any/testing/browser-check` nor the six `extbase` hints, which produce a
  decision about what already exists. Both were in answers it had received.
- The same session filed six further reports that are instances of it. The
  content-element skill fired 12 seconds after the user wrote "bitte lies nach";
  the first backend screenshot was taken 78 seconds after the user asked how the
  work had been verified, three and a half hours in; a rename was reported
  complete on 107 green tests while 118 records carried a CType nothing
  declared.
- What it did fetch, it fetched well. The `documents` array is the only reason
  the phpunit guide was found, and that call is the one the session names as its
  best — so the channel works and the moment is wrong.
- The corpus already states the practice for one subject.
  `any/testing/proving-a-condition` carries the negative control that makes one
  result evidence, for a TypoScript condition. Nothing states it for a browser
  suite, where two of that session's tests passed because their assertion could
  not fail.
- Read on 2026-09-01: no skill body carries a `documentId`, so the sentence a
  workflow states at the moment it applies is a sentence and the page behind it
  is not fetchable from there.

## Decided

- The asymmetry is real and is this server's to answer, so it is recorded here
  rather than as six separate wording notes.
- What follows from it is placement, not new prose: a judging document is named
  at the step that finishes something — in a skill, beside the sentence that
  already asks for it — and its `documentId` stands there, because a
  `documentId` is fetchable and a sentence is not.
- The cards this judgement left name the places one at a time; this entry is
  what says they are one finding.
- Rejected: marking a document as "yields a judgement" in the `documents` array
  alone. It is cheap and it is still delivered at the start, which is the half
  that failed.

## Assumed

- That one session reading its own transcript is evidence about more than that
  session. It measured rather than recalled, and it corrected itself once in the
  same debrief, which is what the evidence rests on.
- That a skill is read at the step it names. The same session skipped two
  crossings written as steps, so this is the assumption most at risk.

## Wrong if

- A session fetches a judging document from a skill and the work still ships
  unlooked at, which would say the moment is not the lever and the practice is.
- The split turns out to be one client's ordering of a large answer rather than
  a property of what the entry yields. The same session reported reading a
  `documents` array top-down for what it came for, which is the competing
  explanation and is not settled here.
