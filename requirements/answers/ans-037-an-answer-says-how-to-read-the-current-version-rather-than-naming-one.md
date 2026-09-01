---
id: R-ANS-037
title: An answer says how to read the current version rather than naming one
status: held
judged: 2026-09-01
---

# R-ANS-037 — An answer says how to read the current version rather than naming one

**An answer touching a version outside TYPO3 — node, a GitHub Action, DDEV, a
library — says where the current release is read rather than naming one.**

A version in a hint is right on the day it is written and silently wrong
afterwards, which is why a count of something that grows is not written down
here either. What the answer carries instead is the place the caller reads it
from: the runtime's own release schedule, the action's repository, the registry
the library is published to.

What a TYPO3 line requires is a different statement and stays. That is a floor
the core declares, it is carried as data on the statement rather than in the
sentence, and a caller told what is required has been told something that does
not move under them.

`R-COD-004` is the same demand on this repository, and `R-SKL-029` on a
published skill.

## From

The maintainer's instruction of 2026-08-29. The corpus had not been read for
version numbers of this kind, so what it cost was unmeasured.

## Held by

`not guarded`. The sweep was run on 2026-09-01 over `knowledge/` and found
nothing to rewrite: no answer offers an outside version as the one to adopt.
What it did find is four kinds of number that stay — what a TYPO3 line declares
as a floor, a version quoted out of a tool's own output, a format's version such
as XLIFF, and a phrase a query matches on such as "PHP 8.4 syntax". A check
would report all four, so telling them from a recommendation is a reading rather
than a pattern, and that is why nothing holds this.

`task-intents.json` is what the demand already looks like where it is met: the
answer about `typo3/coding-standards` names no release and says to let the
solver pick one.
