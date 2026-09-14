---
id: D-SKL-070
title: A description is held to a length of its own
date: 2026-08-24
status: open
coveredBy:
  - SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn
---

# D-SKL-070 — A description is held to a length of its own

**A test holds each skill description to 360 characters, and holds the listing
to no total at all.**

A fixed sum blocks the next skill for the length of the ones written before it,
which is what happened to the fourteenth.

`D-SKL-026` set a ceiling over the listing and `D-SKL-064` raised it. The same
entry said what it could not do: no fixed total absorbs another skill. The
fourteenth arrived and hit the wall exactly as that entry predicted.

## Evidence

- Measured on 2026-08-24. The listing costs 3944 of the 3970 ceiling and the
  asset build description costs 461. So 435 had to come out of fourteen
  descriptions that already existed.
- **Shortening does not reach.** An honest trim of the four longest yields 67
  with no routing token removed. The other ten are lists of triggers —
  `data.xml`, `Playwright`, `PHPStan`, `Gerrit`, `TCA`, `sitepackage` — and a
  description reaches a session by exactly those words.
- `typo3-core-patch-checkout` gets no trim at all. `D-SKL-026`'s **Since then**
  records a session that no longer activated it after the last cut of its
  clause.
- The ceiling has moved twice, from 3600 to 3970 and now again, each time by the
  amount the next skill needed. A number that follows what happened is a record
  rather than a limit.
- Thirteen of the fourteen descriptions are already under 360. The fourteenth
  came down from 429 to 345 for publication, and no trigger came out with it.

## Decided

- The cap is per description, at 360. What it holds is a description that grows
  without bound, which is what crowds a listing and is the thing the ratchet
  exists for.
- The total is not held. Which domains earn a skill decides how many this server
  publishes, and arithmetic over the descriptions that exist never did.
- `SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn` replaces the test that
  summed them, and the entries that named the old one name the new one.
- Rejected: a third raise of the ceiling. Rejected too: a merge of two more
  skills, which bought one publication last time and left the next one at the
  same wall.

## Assumed

- That 360 is a length a description can say what it needs in. Thirteen do, and
  the fourteenth came down with every trigger intact, which is the whole of the
  evidence.
- That the total is somebody else's limit rather than nobody's. A client drops
  whole descriptions where they do not fit, least used first (`D-SKL-026`). So
  there is a real sum somewhere and this entry does not know where.

## Wrong if

- A published skill no longer activates after a cut of its description to 360,
  which is what `D-SKL-026` recorded once already.
- A client drops a description because the listing outgrew what it keeps. That
  is the limit this entry no longer holds, and nobody has measured it since
  2026-08-08. The measurement is what would say whether that was safe.
- The descriptions stay short and the count grows until the listing is the
  problem the total guarded against, one skill at a time.
