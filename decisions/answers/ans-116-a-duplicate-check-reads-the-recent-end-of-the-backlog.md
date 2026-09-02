---
id: D-ANS-116
title: 'A duplicate check reads the recent end of the backlog'
date: 2026-08-27
status: open
coveredBy:
  - ForgeTest::aMergedUnionKeepsTheEndItWasAskedFor
  - ForgeTest::aMissNamesTheEnumerationAsACallToCompose
  - ForgeTest::anAreaSaysThatTheIssuesUnderNoneAreOutsideIt
  - ForgeTest::theRecentEndCarriesNoTriageWorkflow
  - ForgeTest::theRecentEndIsOrderedByWhatWasFiledLast
  - ForgeTest::theRecentEndSaysWhichWayTheRestOfTheSetLies
  - ForgeTest::theWindowIsOneFilterHoweverManyEndsItHas
---

# D-ANS-116 — A duplicate check reads the recent end of the backlog

**Whether a defect has already been filed is settled by enumerating the newest
issues of an area rather than the most neglected, which is the end `open` does
not reach.**

`D-ANS-038` sends a caller whose `query` matched nothing to `open` with
`category`, and that route settles the question on a small area and not on a
large one. What separates the two was never stated, so a caller reads a page of
fifty as the set it was promised.

## Evidence

- [`feedback/2026-08-24-225214`](../../feedback/archive/2026-08-24-225214-no-way-to-establish-that-an-issue-has-not-been.md)
  spent three `query` calls on three wordings before filing, all empty, and
  reports the asymmetry as the cost: a hit is conclusive and an empty is not.
  The user filed #110533 afterwards, at which point the number answered
  perfectly.
- Both orderings point at the neglected end. `open` takes `oldest` and `stale`,
  which `Forge::backlog()` sends as `created_on:asc` and `updated_on:asc`. A
  duplicate of a defect somebody has just found is at the other end of both.
- The page does not cover a large area. Measured 2026-08-27 against the tracker:
  Backend User Interface holds 437 open issues, 227 of them Bugs, and 153 filed
  within the last year. `limit` stops at 50 and there is no offset, so the rest
  is reachable by nothing that points at the recent end — `createdBefore` and
  `updatedBefore` both narrow away from it.
- The areas that route was written on are the small ones. Import/Export (T3D) 26
  — the case `feedback/2026-08-24-110926` was settled on — RTE 35, Fluid 40,
  against Localization 116, Extbase 141 and the 437 above.
- **An issue filed under no area is in no area.** #110533 carries no Category at
  all, read from the tracker on 2026-08-27, so no wording of `category` could
  have reached the very issue this feedback could not find. `D-KNW-113` measured
  the population on 2026-08-24: 14042 bugs carry none, one of them filed that
  day.
- The endpoint already answers both halves of the missing shape.
  `sort=created_on:desc` answers 2482 open issues newest first — #110539,
  #110537, #110536, all filed 2026-08-26 — and the 96 open issues filed since
  2026-08-01 come back from `created_on=>=2026-08-01`. Measured 2026-08-27
  through `Http\Fetch`, on the same `/issues.json` the enumeration already
  reads.

## Decided

- Built, as a further way to order and narrow the enumeration rather than as a
  fourth way into the tool. The question is the one `open` already answers about
  one backlog, and the record a caller reads back is unchanged, which is the
  rule `D-ANS-038` set for the second way in and `D-ANS-100` followed.
- The boundary is the recent end of a selection: which issues were filed or
  touched lately, in an area or across the project. Nothing here decides
  duplication, which is `D-ANS-038`'s second **Assumed** and stays the caller's.
- **The route survives an issue with no area.** An area filter cannot reach one,
  so a duplicate check that can only be narrowed by `category` answers a
  question about the filed-under-an-area part of the backlog while looking like
  an answer about the whole of it.
- What `D-ANS-038`'s miss promises is trimmed to what the call delivers.
  "Reading those subjects is what settles whether somebody already reported
  this" holds below the page bound and overstates above it, and the answer
  already carries the `total` that says which the caller is in.
- Queued rather than closed on the spot. The ordering is a declared `enum` and
  the sentence is in `src/`, which is the line
  [`documentation/records/judging.rst`](../../documentation/records/judging.rst)
  draws around a judging run.
- Nothing holds this yet. The test declares the id in the commit that builds it.

## Assumed

- That a duplicate of a fresh report is among the newest issues of its area.
  That is why the recent end is worth reaching, and a report of a defect that
  has been there for years is the case it does not cover.
- That reading subjects is what settles a negative at all. The alternative is
  that no enumeration settles it and the honest answer is that the tracker
  cannot be asked, which is what the feedback suspected.

## Wrong if

- The newest page does not hold the duplicate either, on the cases callers
  actually bring. Then the recent end is not where a duplicate is, and the
  question wants matching rather than enumerating.
- A duplicate check is settled by `query` in practice, and the empty answers
  this rests on are the exception. Then what was needed was better words and not
  another way in.
- The issues carrying no Category turn out to be a shrinking set. Then an area
  filter reaches nearly everything, the coverage bound above is a historical
  artefact, and only the size bound is real.
- Callers reach the newest page and then ask again in other words, which is
  `D-ANS-054`'s first **Wrong if** fired one ordering further along.

## Since then

Built on 2026-08-27 as one ordering and one filter on the enumeration that was
already there, both verified against the tracker that day together with the
range form — which is how both ends of that field reach it as one filter rather
than as a second replacing the first.

The window is what carries the coverage bullet rather than the area: a caller
who narrows only by area is answered that an issue filed under none is in no
area at all, and told the call that drops it. What the page cannot hold is
answered at the end it was asked about, the standing sentence pointing the other
way.

The way in is `backlog` since 2026-09-02, not `open` — `D-ANS-137`.
