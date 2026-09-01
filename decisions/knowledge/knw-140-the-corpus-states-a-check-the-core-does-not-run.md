---
id: D-KNW-140
title: 'The corpus states a check the core does not run'
date: 2026-09-02
status: open
coveredBy:
  - HintsTest::theCheckThatSaysWhichValueWasMeantNamesWhatWillNotRaiseIt
---

# D-KNW-140 — The corpus states a check the core does not run

**`empty()` is stated as a defect to raise, in a corpus whose own subject calls
it everywhere and never fails on one.**

Every other statement here is what the core does. This one is what a reviewer
holds against it, so it says both, and the sentence that makes it usable is the
one saying nothing will raise it for you.

## Evidence

- The maintainer ruled it a rule on 2026-09-01 and gave the reason on
  2026-09-02: `empty()` is a falsiness test rather than a comparison against an
  expected type. `D-FBK-053` is the migration the report arrived in.
- The reason does not rest on a package. `phpstan/phpstan-strict-rules` rejects
  the call and is in no covered checkout's `composer.json` and in no
  `Build/phpstan/*.neon`, so a statement resting on it would hold nowhere the
  corpus answers for.
- The core's own analysis cannot raise it. `Build/phpstan/phpstan.neon` is at
  level 5 on every covered branch, and the four rules it adds are about
  `instanceof`, a forbidden attribute, named arguments and `unserialize()`.
- The call is not rare there. Counted on 2026-09-02 over
  `.checkouts/main/typo3/sysext/*/Classes`: 2614 of them.

## Decided

- A hint of its own in the PHP conventions, `php-value-checks`, rather than a
  sentence in `core-static-analysis`. That hint answers what the core's analysis
  rejects, and this is the opposite case.
- The substitution is stated per expected type and said to be a reading rather
  than a rewrite, because `($x ?? false)`, `$x !== []` and `$x !== ''` are
  different answers to the same call and only the value says which.
- The tension is stated in the hint rather than left to the reader. A session
  that infers style from the code around it reproduces `empty()`, which is what
  the report reached this server with.
- The strict rules are not named in the hint. Naming a package the caller may
  not run would make the rule read as that package's, and the caller can do
  nothing with it.

## Assumed

- That a reviewer raising this is the common case and a checker running it is
  the rare one. Nothing here measures either; what is written is what the
  maintainer holds a patch to.

## Wrong if

- A covered branch adds the strict rules or a rule of its own against the call,
  which would turn the statement into what the core enforces and move it to
  `core-static-analysis`.
- A session takes the hint as licence to rewrite `empty()` calls it was not
  changing, which is the diff `D-FBK-053`'s minimal-diff card is about.
