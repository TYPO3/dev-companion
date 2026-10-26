---
id: D-ANS-188
title: Several hints come back by id in one call
date: 2026-10-26
status: open
coveredBy:
  - HintsTest::severalHintsComeBackByIdInOneCall
---

# D-ANS-188 — Several hints come back by id in one call

**`typo3_hint_lookup` takes `ids`, a list of hint ids, and answers with each of
them whole in the order given. The sitepackage skill asks for its hints that
way.**

## Evidence

- [`2026-10-26-134303-hint-lookup`](../../feedback/archive/2026-10-26-134303-hint-lookup-takes-one-id-per-call-the.md).
  A run with `typo3-sitepackage-development` made six calls by id, each with the
  same envelope of target version, domains and withheld index. A session pays
  one context per call (`D-FBK-020`).

## Decided

- **A list parameter beside `id` rather than a union.** `D-ANS-017` keeps every
  argument to one type, so `id` stays a string. Both together are one list.
- **The skill passes its ids in one call.** The brief's checklist keeps naming
  one id per item, because each item says what that one hint answers.

## Wrong if

- A session passes `ids` and reads only the first hint, because the answer is
  long.
