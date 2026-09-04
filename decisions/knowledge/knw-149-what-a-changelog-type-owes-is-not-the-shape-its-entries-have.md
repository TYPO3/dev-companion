---
id: D-KNW-149
title: What a changelog type owes is not the shape its entries have
date: 2026-09-04
status: open
coveredBy:
  - HintsTest::whatAChangelogTypeOwesIsSaidApartFromWhatItsEntriesCarry
---

# D-KNW-149 — What a changelog type owes is not the shape its entries have

**The corpus states the sections each changelog type owes and, separately, that
the entries in the tree carry more and fewer than that — the `Important`
template most of all.**

One sentence about obligation was read as a description of the files, and the
shipped template describes them least well of the three sources.

## Evidence

- `feedback/2026-09-03-105549`. A session writing an `Important` entry found
  three accounts disagreeing: this corpus said `Important` has no Impact
  section, `Build/rstTemplates/rstTemplateImportant.rst` offers Affected
  installations and Migration and no Impact, and the neighbouring entries it
  opened carry Description and Impact. It followed the neighbours.
- Counted in `.checkouts/main` over
  `typo3/sysext/core/Documentation/Changelog/`, on 2026-09-04. Of 349
  `Important` entries, 55 carry an Impact section, 25 a Migration and 13 an
  Affected installations. The share is the same on 12.4 and 13.4 — 32 of 271 and
  38 of 310 — and higher in the recent directories: 6 of the 10 in `14.3.x`.
- So the feedback's own claim is wrong as stated. Impact is common in
  `Important` entries and far from universal, which is what makes the obligation
  sentence right and the reading of it wrong.
- The other half of that sentence does not hold either. Of 969 `Deprecation`
  entries 951 carry a Migration and 284 an Affected installations; of 1057
  `Breaking` ones, 994 and 368. Affected installations is in both templates and
  in under a third of the entries.
- The templates on `main`: Breaking and Deprecation offer Description, Impact,
  Affected installations, Migration; Feature offers Description and Impact;
  Important offers Description, Affected installations, Migration — the only one
  whose sections are the ones its entries mostly do not have.
- `Build/Scripts/validateRstFiles.php` reads the include, the anchor, the title
  block and the index line, so none of this fails a check.

## Decided

- Step 4, wording. The rule was delivered and read, and the sentence stated an
  obligation in a form that reads as a description.
- The document and `documentation-changelog` both say the obligation and then
  say that the entries differ, rather than one of the two.
- No share is written into either. The numbers move with every release, and what
  a session needs is that the tree disagrees with the template — the counts are
  here, on the day they were taken.
- The reader is sent to a neighbouring entry in the target directory, which is
  where that session settled it and where the fence and the index tags are read
  from anyway.
- Nothing is said about correcting the template. It is the core's file, and this
  server describes what is there.

## Assumed

- That an entry carrying an Impact section is not a defect nobody has reported.
  Nothing in `Howto.rst` or the validator forbids one, and the share is rising
  rather than being cleaned up.

## Wrong if

- The core adds a check that fails an `Important` entry carrying an Impact
  section, which would make the practice the defect and the template right.
- A session reports writing the wrong shape after reading the corrected
  sentence, which would mean the obligation and the practice cannot be said in
  one place.
