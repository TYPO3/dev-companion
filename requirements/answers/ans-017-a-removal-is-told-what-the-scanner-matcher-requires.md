---
id: R-ANS-017
title: 'A removal is told what the scanner matcher requires'
status: held
restsOn: [D-ANS-035]
heldBy:
  - HintsTest::aRemovalIsToldWhatTheScannerMatcherRequires
  - KnowledgeTest::theBreakingRouteStatesWhatTheScannerMatcherRequires
---

# R-ANS-017 — A removal is told what the scanner matcher requires

**A caller who asks this server about a removal of public API learns what the
extension scanner matcher requires. It arrives in the same answer as the `[!!!]`
marker and the changelog file.**

A rule reachable only from the word deprecation reaches the callers who do not
make that mistake. A session that reviews or writes a removal asks about the
removal.

A session settled what the rule says against `.checkouts/main`, and that is
[`D-ANS-035`](../../decisions/answers/ans-035-the-matcher-entry-is-owed-to-what-the-changelog-tag-claims.md).
The entry is due to what the changelog entry's scanned tag claims. That is why
the `breaking` intent states it rather than recommends it.

## From

`feedback/2026-08-01-115109` (2026-08-01), a review of the core patch that
replaces GD-based error thumbnails. It asked `typo3_rule_lookup` for the
convention on the removal of a public method and had to find the precedent with
a grep of the checkout.

Measured on 2026-08-02: `typo3_rule_lookup "extension scanner"` returned the
`## Deprecations` section of `knowledge/documents/typo3-commit-messages.md`.
"breaking change changelog", the `rulesQuery` the `breaking` intent itself uses,
returned four sections, none of which named a matcher.
