---
id: D-ANS-050
title: 'A curated needle matches the word it is'
date: 2026-08-04
status: open
coveredBy:
  - HintsTest::aBriefNamesTheHintsItLeftBehind
  - HintsTest::aBriefNamesTheSkillThatOwnsTheWork
  - HintsTest::aCompoundIsFoundWhicheverWayTheCallerJoinsIt
  - TermSearchTest::aNeedleThatRunsIntoASeparatorIsLeftAsItWas
  - TermSearchTest::aStemRunsPastItsOwnEndAndACuratedWordDoesNot
---

# D-ANS-050 — A curated needle matches the word it is

**`Text::containsWord()` ends a match where the needle's word ends, give or take
an inflection, and `Text::startsWord()` keeps the prefix rule for the stems.**

`test` matched "testimonials", so the guide read "build a testimonials content
element with a custom backend preview" as test coverage. Since
[`D-SKL-013`](../task-skills/skl-013-the-guide-names-the-skill-that-owns-the-task.md)
it named `typo3-extension-testing` ahead of the skill that owns the task. That
entry's second **Wrong if** is this one, and `feedback/2026-08-01-003356`'s own
task is the case.

## Evidence

- **What the loose side does, counted rather than argued.** Every needle of
  every curated vocabulary: the 14 task intents, the domain keywords and
  markers, the hint `appliesTo` patterns, the three hint markers. That is 1232
  of them, asked against the 169 texts this repository has to hand. Those are 3
  forward review prompts, 38 contract prompts, 127 hint titles and the reported
  task. 106 matches run past the end of their needle. 94 of them are the word in
  another form and 12 are another word.
- **The 12.** `test`+"imonials". `maintain`+"able", +"er", +"ers". Those read
  "keep its implementation maintainable" (`SKILL-04`), "the extension's
  maintainers" (`SKILL-07`) and "hand this extension over to a new maintainer"
  (`SKILL-09`) as upgrade work. `boot`+"strap", where the pattern that stands
  for the extension boot files reaches "Bootstrap transition in backend CSS".
  `lit`+"erals", where the TypeScript keyword puts "Fluid Conditions, Escaping
  and Array Literals" in TypeScript. `php`+"stan" and +"unit". `drop`+"ping",
  where the drag-and-drop pattern of the accessibility hint reads "dropping
  support for". One of the 12 is a true match the rule does not carry: `cache`
  reaches "Cacheable", whose hint that title still reaches.
- **The 94 are one thing.** `-s`, `-es`, `-e`, `-d`, `-ed`, `-ing` — labels,
  translations, icons, tests, testing, booting, pushing, seeding. `deprecat` is
  the only needle in the whole vocabulary that is less than a word. Its four
  forms are what `-e`, `-ed`, `-ing` and `-ion` are in the list for.
- **The same rule read by a caller who did not write a word.** Anchoring on the
  right first took 25 hints out of their own titles and dropped the reported
  task's own answer from `text(317)` to `text(48)`. `TermSearch::stem()` cuts a
  query word to six characters and hands the result to the same method. The
  search looks for "testimonials" as "testim" and "deprecated" as "deprec". A
  rule that closes those on the right searches the corpus for a word nobody
  writes.
- **And by a corpus that ends no words.** With the stems separate the suite was
  green but for one case. A core patch review that names
  `ViewHelpers/ThumbnailViewHelper.php` lost the image-processing hint, because
  `thumbnail` reaches that path only when it runs past its own end. A path in
  lowercase is one run of letters. It has no word ends for a rule to hold a
  needle to, which is what makes it the other half of this.
- **Nothing else moved.** `bin/cli hints:coverage` prints the same page byte for
  byte, and `bin/cli hints:probe` answers the reported task with the same two
  hints at the same scores. Over the 169 texts the only detections that changed
  are the five false ones. Those are the `tests` intent off the testimonials
  task, `installation-upgrade` off `SKILL-04`, `SKILL-07` and `SKILL-09`, and
  TypeScript off the Fluid array-literal hint.

## Decided

- **The word rule is what a curated needle gets.** `containsWord()` closes the
  match with `(?:s|es|e|d|ed|ing|ion|ions)?(?!\p{L})`. Those are the forms of
  one word, its plural, its tense, the noun of the act, and not what derives
  from it. `-able`, `-er` and `-ers` are outside the line because that is where
  the measurement put them, not because grammar does.
- **The prefix rule keeps a name of its own.** `startsWord()` is the method as
  it was, and `TermSearch::carries()` is its caller, because a term is a stem by
  construction. `carriesWord()` beside it is the same length floor over the word
  rule, for the curated patterns that go through the same door.
- **So the answer to "needle or corpus" is both, and they are different
  questions.** Whether a needle may run past its end is the needle's own
  property. A stem may, a word may not, and only the caller with it in hand
  knows which it has. Whether there is an end to run past is the corpus's — a
  sentence ends its words and a path does not. `Hints::scoreKeywords()` answers
  the second one. It asks the task text for the word and the paths for the
  prefix, and a pattern that matches in both is still one match.
- **A needle that ends in a separator stays as it is.** Only one that ends in a
  letter can run into the next word. `f:` is the Fluid namespace prefix and what
  follows it is the whole point (`D-ANS-036`); `typo3/sysext/` is a path a file
  name continues. And the right side closes on a letter rather than on a word
  character, so `sys_file` still reaches `sys_file_reference`. That is
  [`D-ANS-006`](ans-006-an-identifier-is-found-however-it-is-spelled.md)'s side
  of the same question.
- **The corpus was not edited.** The check
  [`D-ANS-022`](ans-022-the-matcher-takes-a-hyphenated-compound-apart-measured-over-the-corpus-first.md)
  made, whether the cheaper half is the vocabulary, comes out the same way here.
  One needle in the whole of it is a stem. A mark on the other 1231 as words
  would have to keep step with every one somebody writes afterwards.

## Assumed

- **That the eight endings are the ones this vocabulary needs.** `-ion` and
  `-ions` are in for a form no text in the corpus writes. "Deprecation" is the
  name of the intent `deprecat` selects and a caller certainly writes it, but
  the measurement covers `-e` and `-ed`.
- **That an end nobody wrote down costs nothing.** English doubles a consonant
  before `-ing`. So `drop` no longer reaches "dropping", and no needle of the
  form `plan`, `ship`, `set` reaches its own participle. The one occurrence
  measured was a false match, and a true one would look exactly like a hint that
  is not reached.
- **That a path wants the prefix rule rather than its words.** A split of
  `ThumbnailViewHelper` into words at the lowercase step would reach `thumbnail`
  and `viewhelper` alike, which is more than this restores. Nobody measured it
  and it is not what happened.

## Wrong if

- A hint or an intent a query used to reach is out of reach. The word that
  reached it is a form of its needle. That is an end outside the eight. Or a
  stem somebody wrote into `knowledge/` as a curated pattern where the word rule
  now holds it whole. `bin/cli hints:coverage` names the first kind on its own.
- A path no longer reaches what it reached. The two rules meet in
  `Hints::scoreKeywords()` and nowhere else. So a second caller that matches a
  curated word against paths would get the sentence rule silently.
- The line between an inflection and a derivation turns out to be the wrong one
  to draw. A corpus where `-able` or `-er` carries the subject would want the
  end list as the needle's property after all. That is the corpus edit this
  entry decided against.
