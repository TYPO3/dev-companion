---
id: D-KNW-141
title: 'The shape of a patch is stated where a patch is judged'
date: 2026-09-02
status: open
coveredBy:
  - KnowledgeTest::theRulesSayWhatShapeAPatchIsLeftIn
---

# D-KNW-141 — The shape of a patch is stated where a patch is judged

**What shape a patch is left in is stated in `core/contribution/rules`, and in
neither of the two skills that route to it.**

The card serving this proposed the patch development skill and the review skill,
one half each. Both keep no copy of what a lookup owns, and one already says so
in its second paragraph.

## Evidence

- The maintainer ruled it a rule on 2026-09-01 and called it general rather than
  the core's alone — `D-FBK-053`.
- The section was already half there. `## Code Style` in
  `core/contribution/rules` said "Avoid unrelated refactoring in bug fix
  patches" and nothing about the formatting a patch leaves, the arrays it
  rewrites, or the file it replaces instead of editing.
- Nothing enforces it and nothing undoes it. The core's fixer normalises the
  trailing comma per form — `Build/php-cs-fixer/config.php`,
  `trailing_comma_in_multiline` — and takes no position on whether a given array
  is written on one line or many, read in
  `.checkouts/main`.
- `SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns` is the rule the proposed
  home would have broken, and the review skill states the same thing in its own
  words: the contribution rules are lookups, and a copy of them there is one
  that cannot be corrected.
- The review half is already carried, twice. The review skill has a section on
  where the review ends and the rework begins, and the case the report actually
  hit — a change to something deliberate while a rework was authorised — is what
  `feedback/2026-09-01-210110` reports and what its own card takes on.

## Decided

- Four bullets in `## Code Style`, phrased as what the patch does rather than as
  what a reviewer dislikes: edit rather than rewrite, leave the form of what was
  not touched, do not collapse or expand an array unless that is the change, and
  write a new one expanded so the next diff marks a key rather than a line.
- The tests half is stated there too, because it is the same rule applied to the
  file a reviewer reads least carefully: an assertion joins the test that covers
  the case, and the fixtures a removed test used stay.
- Nothing goes into either skill, and neither is edited. What routes to this
  document routes to it whole.
- The statement stays core-scoped, in a document withheld outside a core
  checkout. The general claim was the maintainer's and the evidence is the
  core's review practice, so a project-scope copy would be a wider claim than
  what was read.

## Assumed

- That a session reaching for this asks in the words of the code style rather
  than of the review. `code style for a core patch` and
  `unrelated refactoring in a patch` reach the document;
  `keeping the diff small for the reviewer` does not, and lexical matching is
  what `D-ANS-003` settles.

## Wrong if

- A session reformats past its change anyway, which would put the lever in the
  review skill after all — as a step rather than as the copy this rejected.
- A project or extension author reports the same finding, which would make the
  core-only home the boundary drawn one notch too tight.
