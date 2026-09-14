---
id: R-SKL-008
title: 'A task skill does not run without the server it came from'
status: held
restsOn: [D-EVI-002]
heldBy:
  - SkillTest::theBaseStopsTheTaskWhenTheServerIsNotConnected
---

# R-SKL-008 — A task skill does not run without the server it came from

**Every published task skill establishes that this server answers before it does
any of the work, and stops with that finding when it does not.**

A skill is a copy the installer wrote into somebody else's project. It loads
from disk. So the session reads its order, its rubric and its confidence with or
without the tools behind it. Nothing on either side reports the difference. The
failure is therefore silent by construction. What comes out is a review in the
skill's voice, built from general TYPO3 knowledge. No reader can tell it apart
from one with evidence under it.

To establish it costs nothing; the first call of the order is the proof. The
answer when it fails is the finding itself, not a fallback. The session may go
on only after it has said the server is absent and the user has asked it to go
on anyway. Then the answer carries that sentence.

## From

Sessions that ran the installed skills against an unconnected server and
returned a full answer regardless, repeatedly and without either side aware of
it (2026-07-31).

## Held by

- `SkillTest::theBaseStopsTheTaskWhenTheServerIsNotConnected`, which holds the
  precondition in `skills/base.md` and therefore in every published copy. That a
  session stops is not guarded, and will not be; see `D-EVI-002`.
