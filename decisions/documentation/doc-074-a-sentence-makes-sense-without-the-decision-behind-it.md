---
id: D-DOC-074
title: A sentence makes sense without the decision behind it
date: 2026-11-28
status: open
coveredBy:
  - ProseTest::aSentenceIsFlaggedForWhatTheRulesReplace
---

# D-DOC-074 — A sentence makes sense without the decision behind it

**A sentence makes sense to a reader who never read the decision behind it. It
says what to do in literal words and leaves out the picture of the reason.**

The maintainer quoted two paragraphs of the core patch review checklist on
2026-11-28 that "no person understands".

## Evidence

- The two paragraphs said "Assessed with nothing under it is the cheapest
  sentence in a review" and "a quiet surface went quiet after the reading rather
  than before it". Both compress `D-SKL-007` into a picture.
- Both passed `bin/cli prose:check`. Their sentences were short, active and in
  the present tense. The word list of `D-DOC-073` had none of their pictures.
- `D-SKL-112` and `D-DOC-073` fixed the form of a sentence. A sentence in a good
  form can still name a thing only the decision introduces, such as "the drop".
- A count over `skills/`, `knowledge/documents/` and `documentation/` on
  2026-11-28 found "cheap" and its forms 34 times, "buy" 10, "pay" 9 and "earn"
  8.

## Decided

- The test for a paragraph: a reader who never saw this repository says in one
  sentence what to do after it. A paragraph that fails is rewritten in literal
  words, with every instruction and fact kept.
- A sweep on 2026-11-28 applied the test to `skills/` and
  `knowledge/documents/`, one commit per area. It rewrote only the paragraphs
  that failed.
- `bin/cli prose:check` also flags "cheap", "dispose of", "let go", "go quiet",
  "pay", "buy" and "earn" in a new sentence. The list catches the known pictures
  only. Whether a sentence is clear stays a read.

## Wrong if

- The new words flag mostly literal sentences, such as a price or a payment, and
  writers learn to skip the report.
- A reader still asks what a swept paragraph means. Then the test is too weak,
  and a read by a reader without context has to become a step of the work.
