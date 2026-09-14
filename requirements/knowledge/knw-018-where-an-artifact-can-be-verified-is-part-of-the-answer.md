---
id: R-KNW-018
title: 'Where an artifact can be verified is part of the answer'
status: held
heldBy:
  - HintsTest::shippedContentIsAnsweredPastThePointWhereTheFileExists
---

# R-KNW-018 — Where an artifact can be verified is part of the answer

**Where nobody can verify an artifact at the place that produces it, the answer
says where they can.**

A mechanism that runs once leaves its author to read their own output back and
call that a check. So the hint names the place where the artifact runs and what
triggers it again.

## From

`Initialisation/data.xml` regenerated three times in one session and never
imported once, on an installation that had already run it. The hint had the
registry namespace but not the key that unlocks it (2026-07-29).
