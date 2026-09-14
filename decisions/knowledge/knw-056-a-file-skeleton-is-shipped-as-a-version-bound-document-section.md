---
id: D-KNW-056
title: A file skeleton is shipped as a version-bound document section
date: 2026-08-04
status: open
coveredBy:
  - KnowledgeTest::codeFencesSurviveTheSectionSplit
---

# D-KNW-056 — A file skeleton is shipped as a version-bound document section

**The corpus ships a file a caller writes into its own repository as one
document section. The section fences it whole and binds it to the majors it
holds for.**

The corpus answers as prose today what an extension needs to start its tests.
Which two files to copy out of `typo3/testing-framework`, what to change in
them, which variables the run needs. The caller reconstructs the files from that
description every time, and for Playwright outside the core there is no
description to reconstruct from at all.

## Evidence

- The corpus already returns what a skeleton needs. `Documents` splits on `##`
  and returns a section in its original form, code fences included.
  `typo3://core/{id}` serves the whole document uncut beside it.
- The scope has a place already, and this stretches nothing.
  `Documents::isCoreOnly()` reads it off the `covers` entry that names the
  document, and those entries already carry `scope`. So a document that answers
  for an extension is a topic declared with that scope. That all five documents
  today are the core's own is the subjects their writers chose.
- One file fits a section and a file with its prose does not.
  `MAX_SECTION_LENGTH` is 2400 characters; `UnitTests.xml` is 1845 bytes,
  `FunctionalTests.xml` 1881, and the core's `playwright.config.ts` 1611. The
  cut is fence-aware, which prevents an unclosed code block and not half an XML
  file.
- The bound is not theoretical. Between `typo3/testing-framework` lines 8 and 9
  both XML files differ in two lines. Those are the PHPUnit schema URL, `10.1`
  against `11.2`, and `beStrictAboutTestsThatDoNotTestAnything`. Line 9 and
  `main` are identical. A caller on the older line handed the newer file gets a
  schema URL for a PHPUnit it does not have.
- A document is the one part of the corpus with no bound at all. A hint carries
  `since` and `until`, a catalog entry carries them, and `TestSuiteHints`
  filters by the target major. `Documents` reads a title and its sections and
  nothing else.

## Decided

- The surface is the document corpus. This session drafted a
  typo3_skeleton_lookup and dropped it. It would have added a registry entry, an
  output schema, contract tests and a second place to state the scope. That is
  for an answer the corpus already returns in the right shape.
- One file per section, its explanation in the section beside it, because the
  budget fits one of the two. A file that outgrows the budget is one the corpus
  does not ship, rather than a reason to raise it.
- The document itself declares the bound per section and `Documents::sections()`
  reads it. So one place carries it and a renamed heading cannot unbind a file
  without a word of it. This session rejected a sidecar that maps heading to
  range for that. It is the same statement in a place that can disagree with the
  first.
- This session rejected one document per major. The divergence measured is two
  lines in forty-five, and a lookup would return both sections for one query and
  leave the caller to pick.
- The version stays out of the prose and out of the heading, which is what
  `HintsTest` already enforces for a hint.
- A check holds the copy the corpus ships, rather than a promise. It compares
  each derived file against the release it came from below `.checkouts/`, over
  the pairs `Upkeep\TestingFramework` computes. Without it this is the file
  nobody updates, which is what `R-KNW-047` refused for the bootstrap.

## Assumed

- A caller that gets a fenced file writes it out rather than paraphrases it.
  Nothing here measures that.
- The skeletons worth a place in the corpus stay inside the section budget. The
  three measured do. A fixer configuration or a CI workflow may not, and the
  first one that does not is what tests the rule above.
- A run can verify the Playwright skeleton. It has no upstream to diff against,
  since the core's own configuration points into the core tree. So this
  repository authors it, and `skeleton:check` says nothing about it.

## Wrong if

- A skeleton reaches a caller cut by `MAX_SECTION_LENGTH`, as a file that is
  syntactically incomplete.
- A shipped file drifts from the release it came from and nothing fails.
- A second surface starts to ship files, so a caller has two places to ask and
  they can disagree.
- The declared bound line reaches a caller as part of the file it binds.
