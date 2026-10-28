---
id: D-SKL-100
title: The review skills report in one skeleton
date: 2026-10-26
status: open
coveredBy:
  - SkillTest::everyCheckoutValueTheAuditHeadOffersHasADefinition
  - SkillTest::theAuditGivesEachPackageARowOfItsOwn
  - SkillTest::theAuditHeadNamesWhereTheCheckoutStands
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
- **The line form is a gate of named rules**, one per place a report drifted. A
  head field holds its value alone, and a finding carries every field. The three
  lists hold one line each, the claim, an em dash and its source. A status cell
  holds one word, and a surface read in part takes two rows.
- **The answer is the fence and at most a handover after it.** The handover is
  the order of the work and the skill that takes each finding. `REVIEW-01` asks
  "what should happen next", and a ban on the handover would leave that half to
  the *Owner* field. A summary before the fence repeats the head.
- **A rule stands outside a finding's lines.** The health skeleton's *Evidence*
  line said "goes to Open", and two findings copied it as text.
- **Rejected: one shared file that `Installer` copies into each skill**, as it
  copies `skills/base.md`. That is a second publication mechanism for one file,
  and the head and the bands differ per skill anyway. The test holds the three
  copies to each other instead.
- **Rejected: a contract case for the form.** A contract case records no run.
  Every forward review writes this report, so each recorded run measures it.

## Assumed

- That a session fills a skeleton it was handed more reliably than it derives
  one from a list in prose. `D-SKL-099` assumed the opposite for the sentence
  about a document, and nothing has measured either yet.

## Wrong if

- A review report with the skeleton in front of it leaves out a heading or
  invents one. The skeleton then needs a gate in the skill, not more fields.
- A reader who compares two reports still has to read them whole to compare one
  section. The headings then name the wrong units.
- A report still breaks a rule the gate names. Then a rule in prose is not the
  lever, and a filled example in the skeleton is the next step to weigh.

## Since then

On 2026-10-28 a core review of change 97177
([`feedback/2026-10-28-031023`](../../feedback/archive/2026-10-28-031023-the-review-report-keeps-the-headings-but-not.md))
wrote paragraphs under the headings. So the fence, the named rules, one value
per head row and the audit's `Key`, `Checkout` and package rows followed. The
fourth runs, against `49b0adbc`, each cited a rule known by its id alone: a hint
id the brief named without its text, a document "named, not read" and one "via
the skill". So the gate's Rule clause says a name is no source and the session
fetches it. No form for an unread rule exists, because a finding would rest on
it. `REVIEW-02` still wrote a backport paragraph after the fence, which the gate
rules out. The next such breach makes the filled example the step to weigh.
