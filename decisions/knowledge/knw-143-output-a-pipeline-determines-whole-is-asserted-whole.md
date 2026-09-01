---
id: D-KNW-143
title: 'Output a pipeline determines whole is asserted whole'
date: 2026-09-02
status: open
coveredBy:
  - HintsTest::sanitisedOutputIsPinnedWholeAndAPublishedPathIsNot
---

# D-KNW-143 — Output a pipeline determines whole is asserted whole

**A sanitiser's output is pinned with `assertSame` against the whole serialized
string, and a string carrying a value the change does not decide is not.**

The maintainer ruled the rule and left its home open on 2026-09-01. The reading
settled it: this is about how a test asserts, which is `core-tests`.

## Evidence

- `feedback/2026-09-01-210550`, with the boundary already in it: icon markup
  carries a cache-busting value, so the same reasoning does not reach it.
- That boundary is exact and holds. `SvgSpriteIconProvider` builds
  `<use xlink:href="...">` from `PathUtility::getSystemResourceUri()`, and the
  default publisher writes package assets "to the public `_assets` directory
  using a hash as directory name" — so the string carries a hash the change did
  not cause, read in `.checkouts/main` on 2026-09-02.
- The core does not follow the rule where it matters most.
  `Tests/Functional/Resource/Security/SvgSanitizerTest` asserts with four
  `assertStringContainsString` and three `assertStringNotContainsString`, and
  the `SvgDocumentService` unit tests assert parsed attributes rather than the
  serialized document.
- `core-tests` already carries the neighbouring sentence: a change that alters
  rendered output is asserted far outside the class's own test, verbatim.

## Decided

- The statement goes to `core-tests`, beside that sentence. The subject is what
  an assertion is worth, which is test shape, and the security hints answer
  where a value reaches a sink.
- It says the core's own sanitiser suite asserts with contains, because the
  hint's reader is one file away from imitating it. That is the same shape as
  `php-value-checks`, where the corpus states a rule the checkout does not keep.
- The boundary is stated as the rule rather than as an exception: the assertion
  covers what the pipeline determines, and the published asset path is the
  instance where that is less than the whole string.
- No word of the vocabulary is general. `assertSame`, `SvgSanitizer`,
  `serialized SVG` and `sanitised output` are added and `assertion` is not —
  `D-KNW-142` is the entry where a general word carried this hint into an
  extension brief the same day.

## Assumed

- That capturing the canonical output once and pinning it is how such a test is
  written, which the report states and no core test demonstrates.

## Wrong if

- A session pins a whole string that carries the asset hash anyway, which would
  make the boundary a sentence nobody reads rather than one nobody needs.
- The sanitiser suite is rewritten to assert whole strings, which would make the
  statement a description of the core again and the warning beside it wrong.
