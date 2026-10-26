---
id: D-SKL-099
title: A document of a review is the report in its own order
date: 2026-10-26
status: open
coveredBy:
  - SkillTest::aDocumentOfAReviewTakesTheShapeOfTheReport
---

# D-SKL-099 — A document of a review is the report in its own order

**A document the caller asks for after a core review takes the shape of the
report section of `typo3-core-patch-review`. No guide of its own carries that
shape.**

## Evidence

- [`feedback/2026-10-26-102428-a-review-write-up`](../../feedback/archive/2026-10-26-102428-a-review-write-up-for-a-document-has-no.md),
  `/home/benji/projects/typo3-cms`. A session reviewed and amended change 97164.
  Then the user asked for a summary document. The session wrote a page with an
  overview, findings, open points and verification. It had no ranks, no path and
  line per finding, no dropped candidates and no surface status.
- The shape was in the session. The *Report* section of the review skill gives
  the five ranks and the surfaces. `references/checklist.md` gives the five
  parts of a finding. The session names both, so the skill arrived and was read.
- The section said where a file goes and nothing about its shape. "Write it to a
  file only where the caller asks for one" was the only sentence about a
  document. A reader can take a document as a second kind of answer with a shape
  of its own.
- `typo3_task_guide` names a guide in `guides` only where one writes the work
  up. For a review the skill does that, and the brief names the skill. So an
  empty `guides` is the correct answer and not a gap.

## Decided

- **Step 4 of the ladder, wording.** The rule arrived and did not say that it
  holds for a document too.
- **One sentence in the *Report* section**, after the sentence about the file.
  It says that a document is this report, in this order and with every part.
- **Closed on the spot.** The change is one sentence in a published skill, and
  it needs no lookup about TYPO3. The maintainer asked for the todos to be
  worked in the same session.
- **Rejected: a guide such as `core/review/the-review-report`.** The skill owns
  the order it reports in, which its closing paragraph says. A guide would be a
  second copy of the same ranks and fields, and the first of the two to go
  stale. That is the reason `D-SKL-074` rejected a clause per skill.
- **Rejected: `typo3_task_guide` naming that guide for `audit`.** No guide
  exists to name, and the brief already names the skill.

## Assumed

- That the session reread the *Report* section when it wrote the document. It
  quotes the section's contents, so it had them in context.

## Wrong if

- A session with this sentence in front of it writes a document of a review in
  another shape. The lever is then not the wording, and a template the session
  fills in is what remains.
- A caller asks for a document that is not the review, such as a note for the
  issue. The sentence then forces the report shape onto a text that needs
  another one.

## Since then

The maintainer asked the same day for reports two reviews can be compared by. So
the template this entry rejected exists after all, as `references/report.md` in
each review skill rather than as a guide (`D-SKL-100`). The rejection of a guide
in `knowledge/documents/` stands. The sentence about a document stays, and now
points at the skeleton.
