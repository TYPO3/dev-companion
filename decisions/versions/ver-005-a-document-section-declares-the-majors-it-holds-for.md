---
id: D-VER-005
title: A document section declares the majors it holds for
date: 2026-08-04
status: open
coveredBy:
  - KnowledgeTest::aBoundSectionIsKeptOnTheMajorItHoldsFor
  - KnowledgeTest::aDeclarationBelowTheFirstLineOfContentBindsNothing
  - KnowledgeTest::noProseDocumentDatesAStatementInItsSentence
  - KnowledgeTest::noProseDocumentNamesACheckOnlySomeBranchesHave
  - KnowledgeTest::theBindingDoesNotReachTheCallerAsPartOfWhatItBinds
  - VersionsTest::proseSaysWhereARangeItDoesNotCarryLives
---

# D-VER-005 — A document section declares the majors it holds for

**A knowledge document section declares the majors it holds for, and the
caller's target filters every prose answer instead of a label that says
unfiltered.**

`D-VER-002` refused a bound mechanism for prose and had the better of the
argument for the corpus it addressed. What it did not have in front of it is a
section whose body is a file the caller writes out verbatim. There the document
is the only place that can state the range.

## Evidence

- `D-VER-002` rests on two properties. A caller reads the corpus whole rather
  than filters it, and the same statements carry a bound where a caller acts on
  them. A shipped file has neither. The document is where the caller acts, and
  no hint carries the file for the range to sit on.
- Its confirmation on 2026-08-02 is what the cost of no bound looks like.
  `typo3_script_lookup` handed a 12.4 contributor `-s checkIntegrityXliff`,
  `-s normalizeXliff` and `-s build`, none of which that branch has. The remedy
  took the commands out of the prose, because their range already lived on the
  suite. A file has nowhere to go.
- The divergence a skeleton has to survive has a measurement. Between
  `typo3/testing-framework` lines 8 and 9 both PHPUnit XML files differ in the
  schema URL, `10.1` against `11.2`, and in
  `beStrictAboutTestsThatDoNotTestAnything`. Line 9 and `main` are identical.
- Nothing new has to exist one layer down. `Versions::holds()` answers whether a
  range covers a major, `Versions::target()` and `Versions::targets()` resolve
  what the caller is on, and `Hints::forVersion()` is the filter this mirrors.

## Decided

- The section declares the bound directly under its heading, and
  `Documents::sections()` reads it. A section that declares nothing holds on
  every covered major, the same as a hint statement with no `since` or `until`.
  So the corpus as it stands today changes in no answer.
- Prose::NOT_VERSION_BOUND goes. A prose answer states the range of the section
  it returns, which is what the constant used to call impossible.
- The three tools that render this corpus take an optional `targetVersion`
  together: `typo3_rule_lookup`, `typo3_script_lookup`, `typo3_task_guide`. A
  caller that learns one of these answers has to find the next one built the
  same way. The parameter on one of them is the split that makes the corpus
  answer two ways.
- The declaration is data, so the two guards in `KnowledgeTest` change around
  the new premise rather than go. A version written into a sentence stays wrong,
  and a suite only some branches carry stays wrong, because a filter can read
  neither.
- Per-paragraph bounds stay refused. The section is the unit, which is what
  `D-VER-002` called per-bullet metadata and what this does not introduce.

## Assumed

- A section is a fine enough unit for prose that changed inside the covered
  range. Nothing measures that. The case that disproves it is a section half of
  which holds on one major.
- A caller on no installation, with no target stated, gets every variant with
  its range named, and that serves it. That is what the corpus does everywhere
  else, and for a file it means a choice of one rather than a read of both.

## Wrong if

- A caller with no target gets two variants of one file with nothing in the
  answer that says which to write out.
- A bound appears on a section whose body is prose rather than a file. So the
  corpus starts to filter the long form of a subject that the hints already
  carry with a bound.
- The declaration reaches a caller as part of the file it binds.
- A section has a bound and nothing reads the range it declares, because the
  tool the caller used passes no target. Nothing runs over that one. Which tools
  render this corpus is a read rather than a count, and whoever adds a fourth
  would have to give it the target.
