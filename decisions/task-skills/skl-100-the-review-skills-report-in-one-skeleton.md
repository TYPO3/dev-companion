---
id: D-SKL-100
title: The review skills report in one skeleton
date: 2026-10-26
status: open
coveredBy:
  - SkillTest::theReviewSkillsReportInOneSkeleton
---

# D-SKL-100 — The review skills report in one skeleton

**`typo3-core-patch-review`, `typo3-extension-patch-review` and
`typo3-extension-health` each write their report in the skeleton of their own
`references/report.md`. The three skeletons share their sections, their order
and the fields of a finding.**

## Evidence

- The maintainer asked for it on 2026-10-26: reports of two reviews have to be
  comparable, so the structure has to be uniform.
- The three *Report* sections disagreed before this change. The core review
  listed five ranks and gave the finding five parts in its checklist. The
  extension review had four bands and other parts. The audit had five severity
  levels, five finding parts of its own, and no `Not reached:` line.
- None of them had a place for a head, an open candidate or the verification. A
  reader compared two reviews by a search through prose.
- [`feedback/2026-10-26-102428-a-review-write-up`](../../feedback/archive/2026-10-26-102428-a-review-write-up-for-a-document-has-no.md)
  asked for a template, and `D-SKL-099` declined it on that one sighting.

## Decided

- **One skeleton, three files.** Each review skill carries
  `references/report.md`, which the server already serves as a `typo3://skill`
  reference. The *Report* section links it and keeps the reason for the order.
- **The same in all three:** a head as a `Field | Value` table that ends on the
  verdict, then the severity headings, then *Checked and correct*, *Open*,
  *Dropped*, *Verification*, *Surfaces* and *Not reached*. A finding carries
  *Where*, *Observed*, *Rule*, *Consequence*, *Remediation* and *Evidence*.
- **Each skill's own:** the fields of the head, the severity headings and one
  field of a finding. The patch reviews ask *Introduced*, and the audit asks
  *Owner*, which step 10 of the audit already demanded.
- **The severity headings stay each checklist's bands.** A core submission, a
  merge and an audit are three decisions. One set of bands for all three would
  rank by a decision none of them makes. The core band *Correct and checked*
  became *Checked and correct*, so the shared heading has one name.
- **Rejected: one shared file that `Installer` copies into each skill**, as it
  copies `skills/base.md`. That is a second publication mechanism for one file,
  and the head and the bands differ per skill anyway. The test holds the three
  copies to each other instead.

## Assumed

- That a session fills a skeleton it was handed more reliably than it derives
  one from a list in prose. `D-SKL-099` assumed the opposite for the sentence
  about a document, and nothing has measured either yet.

## Wrong if

- A review report with the skeleton in front of it leaves out a heading or
  invents one. The skeleton then needs a gate in the skill, not more fields.
- A reader who compares two reports still has to read them whole to compare one
  section. The headings then name the wrong units.
