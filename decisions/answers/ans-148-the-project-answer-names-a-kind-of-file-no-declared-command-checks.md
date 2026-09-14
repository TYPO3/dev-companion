---
id: D-ANS-148
title: The project answer names a kind of file no declared command checks
date: 2026-09-04
status: open
coveredBy:
  - ProjectTest::aDeclaredCheckerTakesItsKindOffTheList
  - ProjectTest::aKindOfFileNoDeclaredCommandChecksIsNamed
---

# D-ANS-148 — The project answer names a kind of file no declared command checks

**`typo3_project_describe` says which kinds of file the project's own packages
ship that none of its declared commands names as a subject.**

The answer already reports every declared command and every package; what it
does not report is the one that is absent.

## Evidence

- `feedback/2026-09-03-235023`. A sitepackage with one hand-written stylesheet
  and no build step. The answer listed php-cs-fixer, xliff-lint, phpunit and
  playwright; nothing said that the repository checks its PHP and its XLIFF and
  not its CSS. The owner asked, and Prettier with stylelint-config-standard
  became `lint:css` afterwards.
- Everything the finding needs is already in hand. `Project::commands()` has the
  declared scripts and the tool each line invokes. `Project::extensions()` has
  the packages that are the project's own, and `npmManifests()` has the
  `package.json` beside them. The session names a fourth signal it saw, an
  `.editorconfig` stanza for `.stylelintrc` with no such file. That is a hint
  about intent rather than about coverage.
- The frame that made the commands block pay is the one this has to keep.
  `feedback/2026-09-03-235407` credits "a check you recommend that the
  repository does not declare is a wrong answer" as what stopped its reach for a
  static analyser. A gap named wrongly is the same mistake from the other side.

## Decided

- Taken on. The evidence that it should exist is here, the answer already reads
  every input, and the round trip it saves is the owner's question.
- The answer says what has no check, and never what to add. A recommendation
  would be this server's decision on a project's standards, which `D-ANS-092`'s
  frame exists to refuse.
- It stays on the line `D-ANS-133` draws. Both halves are reads of files: the
  kinds the packages hold, and the checkers the declared bodies name. What is
  new is the subtraction rather than an inference about what the repository
  meant.
- The tool a declared line invokes decides coverage, against a curated map of
  tool to the kinds it checks, and nothing else does. A line whose tool is not
  in that map counts as one that covers nothing knowable rather than nothing. An
  unusual toolchain would otherwise produce a gap that is not one.
- The kinds in the count are the ones a checker exists for and a package ships
  as source, read off the project's own packages. Those are PHP, CSS, Sass,
  TypeScript and XLIFF. A template or an image is not a gap, and JavaScript is
  not one either. A shipped `.js` is as often build output or a vendored library
  as source.
- `low`, and said rather than left. One session reported it, the recovery was
  one question from the owner, and the map is maintenance that moves here for
  good.

## Assumed

- That a project which checks a kind through CI alone is rare enough to be worth
  the false gap. Nothing here reads a CI configuration, and the answer already
  says that what it lists is what the repository declares.

## Wrong if

- A session reports a gap that was not one and acts on it. That is a checker
  this map does not know, or a kind with a check in a workflow file.
- The map turns out to be the work rather than the feature, which is what
  `bin/cli` has commands for elsewhere and this would have none.

## Since then

Built the same day. `Project::CHECKERS` maps twelve tool names to the kinds each
checks, and `Project::KINDS` maps five kinds to the pattern that finds each.
`uncheckedKinds` is what the project's own packages ship minus what the declared
bodies name a checker for. The answer says it in one sentence under the command
list.

JavaScript is not one of the five. A `.js` a package ships is as often build
output or a vendored library as source. So one with no check would be a gap in
most repositories and a finding in none.
