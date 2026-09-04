---
id: D-ANS-148
title: The project answer names a kind of file no declared command checks
date: 2026-09-04
status: open
coveredBy: []
---

# D-ANS-148 — The project answer names a kind of file no declared command checks

**`typo3_project_describe` says which kinds of file the project's own packages
ship that none of its declared commands names as a subject.**

The answer already reports every declared command and every package; what it
does not report is the one that is missing.

## Evidence

- `feedback/2026-09-03-235023`. A sitepackage with one hand-written stylesheet
  and no build step. The answer listed php-cs-fixer, xliff-lint, phpunit and
  playwright; nothing said that the repository checks its PHP and its XLIFF and
  not its CSS. The owner asked, and Prettier with stylelint-config-standard was
  wired as `lint:css` afterwards.
- Everything the finding needs is already read: `Project::commands()` has the
  declared scripts and the tool each line invokes, `Project::extensions()` has
  the packages that are the project's own, and `npmManifests()` has the
  `package.json` beside them. The session names a fourth signal it saw — an
  `.editorconfig` stanza for `.stylelintrc` with no such file — which is a hint
  about intent rather than about coverage.
- The framing that made the commands block pay is the one this has to keep:
  `feedback/2026-09-03-235407` credits "a check you recommend that the
  repository does not declare is a wrong answer" as what stopped it reaching for
  a static analyser. A gap named wrongly is the same mistake from the other
  side.

## Decided

- Taken on. The evidence that it should exist is here, the answer already reads
  every input, and the round trip it saves is the owner's question.
- What is said is what is not covered, and never what to add. A recommendation
  would be this server deciding a project's standards, which `D-ANS-092`'s
  framing exists to refuse.
- Coverage is decided by the tool a declared line invokes, against a curated map
  of tool to the kinds it checks, and by nothing else. A line whose tool is not
  in that map is reported as covering nothing knowable rather than as covering
  nothing, because an unusual toolchain would otherwise produce a gap that is
  not one.
- The kinds counted are the ones a checker exists for — `php`, `css`, `scss`,
  `js`, `ts`, `xlf` — read off the project's own packages. A template or an
  image is not a gap.
- `low`, and said rather than left: one session reported it, the recovery was
  one question from the owner, and the map is maintenance that moves here
  permanently.

## Assumed

- That a project checking a kind through CI alone is rare enough to be worth the
  false gap. Nothing here reads a CI configuration, and the answer already says
  that what it lists is what the repository declares.

## Wrong if

- A session reports a gap that was not one — a checker this map does not know,
  or a kind checked from a workflow file — and acts on it.
- The map turns out to be the work rather than the feature, which is what
  `bin/cli` has commands for elsewhere and this would have none.
