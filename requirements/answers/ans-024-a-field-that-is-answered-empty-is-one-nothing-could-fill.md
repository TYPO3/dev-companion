---
id: R-ANS-024
title: 'A field that is answered empty is one nothing could fill'
status: held
restsOn: [D-ANS-056]
heldBy:
  - ForgeTest::aPageThatCouldNotBeFilledIsStillTheHitsThatMatched
  - ForgeTest::aSearchHitIsFilledFromTheIssuesTheHitsAre
---

# R-ANS-024 — A field that is answered empty is one nothing could fill

**A record answers a field empty because the source did not carry it, never
because the tool skipped the call that would have carried it.**

A reader takes an empty key as a fact about the thing: no area, nobody assigned,
nothing has moved since the filing. Where the value exists one call away, the
answer is a false statement rather than a short one. The caller has nothing in
front of it that says which of the two it holds.

## From

Two searches returned 50 rows with `category`, `assignedTo`, `createdOn` and
`updatedOn` empty in every one. The issues all have an area and two dates
(`feedback/2026-08-05-033902`). The session that reported it had already given
up on that path for age. One that trusted the fields would have concluded the
backlog had no categories and no activity.

## Held by

- The other half is not guarded: that every other lookup that fills a record
  from one source names no field a second source holds. Nothing reads the answer
  shapes against the endpoints behind them.
