---
id: D-KNW-149
title: What a changelog type owes is not the shape its entries have
date: 2026-09-04
status: open
coveredBy:
  - HintsTest::whatAChangelogTypeOwesIsSaidApartFromWhatItsEntriesCarry
---

# D-KNW-149 — What a changelog type owes is not the shape its entries have

**The corpus states the sections each changelog type owes. It states apart from
that the entries in the tree carry more and fewer than that, the `Important`
template most of all.**

A session read one sentence about obligation as a description of the files, and
the shipped template describes them least well of the three sources.

## Evidence

- `feedback/2026-09-03-105549`. A session that wrote an `Important` entry found
  three accounts at odds. This corpus said `Important` has no Impact section.
  `Build/rstTemplates/rstTemplateImportant.rst` offers Affected installations
  and Migration and no Impact. The entries next to it that the session opened
  carry Description and Impact. It followed the neighbours.
- Counted in `.checkouts/main` over
  `typo3/sysext/core/Documentation/Changelog/`, on 2026-09-04. Of 349
  `Important` entries, 55 carry an Impact section, 25 a Migration and 13 an
  Affected installations. The share is the same on 12.4 and 13.4, 32 of 271 and
  38 of 310. It is higher in the recent directories, 6 of the 10 in `14.3.x`.
- So the feedback's own claim is wrong as stated. Impact is common in
  `Important` entries and far from universal, which is what makes the obligation
  sentence right and the reading of it wrong.
- The other half of that sentence does not hold either. Of 969 `Deprecation`
  entries 951 carry a Migration and 284 an Affected installations; of 1057
  `Breaking` ones, 994 and 368. Affected installations is in both templates and
  in under a third of the entries.
- The templates on `main`. Breaking and Deprecation offer Description, Impact,
  Affected installations, Migration. Feature offers Description and Impact.
  Important offers Description, Affected installations, Migration, the only one
  whose sections are the ones its entries mostly do not have.
- `Build/Scripts/validateRstFiles.php` reads the include, the anchor, the title
  block and the index line, so none of this fails a check.

## Decided

- Step 4, wording. The server delivered the rule and the session read it, and
  the sentence stated an obligation in a form that reads as a description.
- The document and `documentation-changelog` both say the obligation and then
  say that the entries differ, rather than one of the two.
- Neither carries a share. The numbers move with every release, and what a
  session needs is that the tree disagrees with the template. The counts are
  here, on the day of the count.
- The statement sends the reader to a neighbour entry in the target directory.
  That is where that session settled it and where a session reads the fence and
  the index tags from anyway.
- Nothing says a word about a correction of the template. It is the core's file,
  and this server describes what is there.

## Assumed

- That an entry with an Impact section is not a defect nobody has reported.
  Nothing in `Howto.rst` or the validator forbids one, and the share rises
  rather than shrinks.

## Wrong if

- The core adds a check that fails an `Important` entry with an Impact section,
  which would make the practice the defect and the template right.
- A session reports that it wrote the wrong shape after a read of the corrected
  sentence. That would mean one place cannot state the obligation and the
  practice.
