---
id: D-KNW-141
title: 'The shape of a patch is stated where a patch is judged'
date: 2026-09-02
status: open
coveredBy:
  - KnowledgeTest::theRulesSayWhatShapeAPatchIsLeftIn
---

# D-KNW-141 — The shape of a patch is stated where a patch is judged

**`core/contribution/rules` states what shape a patch leaves the code in, and
neither of the two skills that route to it does.**

The card that serves this proposed the patch development skill and the review
skill, one half each. Both keep no copy of what a lookup owns, and one already
says so in its second paragraph.

## Evidence

- The maintainer ruled it a rule on 2026-09-01 and called it general rather than
  the core's alone — `D-FBK-053`.
- The section was already half there. `## Code Style` in
  `core/contribution/rules` said "Avoid unrelated refactoring in bug fix
  patches". It said nothing about the form a patch leaves, the arrays it
  rewrites, or the file it replaces instead of edits.
- Nothing enforces it and nothing undoes it. The core's fixer normalises the end
  comma per form, `Build/php-cs-fixer/config.php`,
  `trailing_comma_in_multiline`. It takes no position on whether a given array
  stands on one line or many, read in `.checkouts/main`.
- `SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns` is the rule the proposed
  home would have broken. The review skill states the same thing in its own
  words. The contribution rules are lookups, and a copy of them there is one
  nobody can correct.
- The review half is already carried, twice. The review skill has a section on
  where the review ends and the rework begins. The case the report actually hit
  is a change to something deliberate under an authorised rework.
  `feedback/2026-09-01-210110` reports that and its own card takes it on.

## Decided

- Four bullets in `## Code Style`, in the words of what the patch does rather
  than of what a reviewer dislikes. Edit rather than rewrite, and leave the form
  of what the change did not touch. Do not collapse or expand an array unless
  that is the change. Write a new one expanded so the next diff marks a key
  rather than a line.
- The tests half stands there too, because it is the same rule applied to the
  file a reviewer reads with the least care. An assertion joins the test that
  covers the case, and the fixtures a removed test used stay.
- Nothing goes into either skill, and neither changes. What routes to this
  document routes to it whole.
- The statement stays core-scoped, in a document withheld outside a core
  checkout. The general claim was the maintainer's and the evidence is the
  core's review practice. So a project-scope copy would be a wider claim than
  what this run read.

## Assumed

- That a session that reaches for this asks in the words of the code style
  rather than of the review. `code style for a core patch` and
  `unrelated refactoring in a patch` reach the document.
  `keeping the diff small for the reviewer` does not, and the lexical match is
  what `D-ANS-003` settles.

## Wrong if

- A session reformats past its change anyway. That would put the lever in the
  review skill after all, as a step rather than as the copy this entry rejected.
- A project or extension author reports the same finding, which would make the
  core-only home the boundary drawn one notch too tight.
