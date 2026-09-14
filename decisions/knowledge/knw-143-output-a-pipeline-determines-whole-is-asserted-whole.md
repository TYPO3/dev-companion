---
id: D-KNW-143
title: 'Output a pipeline determines whole is asserted whole'
date: 2026-09-02
status: open
coveredBy:
  - HintsTest::sanitisedOutputIsPinnedWholeAndAPublishedPathIsNot
---

# D-KNW-143 — Output a pipeline determines whole is asserted whole

**A test pins a sanitiser's output with `assertSame` against the whole
serialized string, and not a string with a value the change does not decide.**

The maintainer ruled the rule and left its home open on 2026-09-01. The reading
settled it: this is about how a test asserts, which is `core-tests`.

## Evidence

- `feedback/2026-09-01-210550`, with the boundary already in it. Icon markup
  carries a cache-bust value, so the same reason does not reach it.
- That boundary is exact and holds. `SvgSpriteIconProvider` builds
  `<use xlink:href="...">` from `PathUtility::getSystemResourceUri()`. The
  default publisher writes package assets "to the public `_assets` directory
  using a hash as directory name". So the string carries a hash the change did
  not cause, read in `.checkouts/main` on 2026-09-02.
- The core does not follow the rule where it matters most.
  `Tests/Functional/Resource/Security/SvgSanitizerTest` asserts with four
  `assertStringContainsString` and three `assertStringNotContainsString`, and
  the `SvgDocumentService` unit tests assert parsed attributes rather than the
  serialized document.
- `core-tests` already carries the neighbour sentence: a change that alters
  rendered output has assertions far outside the class's own test, verbatim.

## Decided

- The statement goes to `core-tests`, beside that sentence. The subject is what
  an assertion is worth, which is test shape, and the security hints answer
  where a value reaches a sink.
- It says the core's own sanitiser suite asserts with contains, because the
  hint's reader is one file away from an imitation of it. That is the same shape
  as `php-value-checks`, where the corpus states a rule the checkout does not
  keep.
- The hint states the boundary as the rule rather than as an exception. The
  assertion covers what the pipeline determines, and the published asset path is
  the instance where that is less than the whole string.
- No word of the vocabulary is general. `assertSame`, `SvgSanitizer`,
  `serialized SVG` and `sanitised output` join it and `assertion` does not.
  `D-KNW-142` is the entry where a general word carried this hint into an
  extension brief the same day.

## Assumed

- That a session writes such a test with the canonical output captured once and
  pinned, which the report states and no core test demonstrates.

## Wrong if

- A session pins a whole string that carries the asset hash anyway. That would
  make the boundary a sentence nobody reads rather than one nobody needs.
- The sanitiser suite is rewritten to assert whole strings, which would make the
  statement a description of the core again and the warning beside it wrong.
