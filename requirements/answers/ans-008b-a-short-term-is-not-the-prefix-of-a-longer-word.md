---
id: R-ANS-008b
title: 'A short term is not the prefix of a longer word'
status: held
heldBy:
  - HintsTest::aShortTermIsNotMatchedAsThePrefixOfALongerWord
---

# R-ANS-008b — A short term is not the prefix of a longer word

**A short term matches as a whole word, not as the prefix of a longer one, on
both the query side and the curated vocabulary.**

Prefix matches exist so a stem finds every form of its word. At three characters
there is no form left to find, and it matches whatever starts with those
letters. It compounds with
[`R-ANS-007`](ans-007-the-discriminating-terms-of-a-query-decide-the-answer.md),
which weighs a term by how few documents carry it. An accident that lands in
exactly one document becomes the most discriminating term in the query and
decides the answer. A pattern with punctuation, a path fragment, `.xlf`, `lll:`,
keeps plain containment, because it is specific enough not to land by accident.

## From

`fal`, the File Abstraction Layer, prefix-matched seven hints through "fallback"
and "false". The same pattern reached that hint from a query about a label, as a
plain substring of a longer word (2026-07-30).
