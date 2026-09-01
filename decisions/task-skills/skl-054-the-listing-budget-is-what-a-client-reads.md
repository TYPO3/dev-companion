---
id: D-SKL-054
title: 'The listing budget is what a client reads'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn
---

# D-SKL-054 — The listing budget is what a client reads

**The description budget is counted over the published skills rather than over
the directory, so a draft costs no listing anything until the commit that
publishes it.** The room for a thirteenth description is a question about which
twelve are worth theirs, and it is asked at publication.

`D-SKL-026` set the ceiling at what the trim of 2026-08-08 left. The twelve
descriptions stand at 3597 characters of it, so the guard as written refused any
new skill file, a draft included — a workflow nobody can load would have been
charged for room in a listing it does not appear in.

## Evidence

- The listing costs 3597 characters against a ceiling of 3600, measured on
  2026-08-18 over the twelve published skills. A thirteenth description of the
  length the other twelve average adds about 300, so no new skill fits without
  one of them being shortened.
- `Installer::skills()` is the directory minus what declares itself a draft, and
  it is what every other consumer already reads: `Sdk\Skills` serves it,
  `ScopeTest` announces it, `SkillTest::everyPublishedSkillIsNamedByAnIntent`
  routes to it. `everyDescriptionIsWrittenToALengthOfItsOwn` was the one
  assertion reading the directory instead.
- A draft is published to nobody, which a smoke test over the installer holds:
  `install` writes no draft, `install --drafts` writes it into that one project,
  and the next `update` takes it out again.
- The ceiling is a ratchet rather than a measurement. Its own comment says it is
  "what the trim of 2026-08-08 left, with room for a rename", and what is
  actually left over is decided by the client's own bundled skills, which took
  5997 of the 6000 characters a 200k session had that day.

## Decided

- The budget is counted over `Installer::skills()`. What it guards is what a
  client reads in one listing, and a draft is in no client's listing.
- The ceiling stays 3600. Raising it now would spend room for a workflow nobody
  has reviewed, and the number is the one thing holding the twelve short.
- Publication of a thirteenth skill owes the room. That is the commit where a
  description is shortened, or where two workflows turn out to be one — a
  reading of all thirteen descriptions, which is not available to the session
  writing the first draft of one of them.
- Rejected: raising the ceiling by the draft's own length. It reads as making
  room and is really removing the guard, because every following draft asks for
  the same.
- Rejected: leaving the assertion on the directory and shortening a published
  description now. The description that would be cut is chosen to make room for
  an unreviewed file, which is the wrong order — `D-SKL-035` buys a new skill a
  baseline run precisely so that the domain is settled before anything is spent
  on it.

## Assumed

- That `--drafts` is rare enough for the budget it does spend to be nobody's
  problem. It is a per-run choice in one project, taken by somebody trying a
  draft out, and that session is the one reader who knows the cost.
- That the twelve are not already over what a real client leaves. 3600 is a
  ratchet nobody has re-measured against a client since 2026-08-08, and this
  entry does not re-measure it either.

## Wrong if

- A draft is published by the `--drafts` route into a project that then loses a
  published skill's description silently. The cost would be real and unpaid, and
  the count would have to include the drafts after all.
- The publishing commit for `typo3-distribution-content` finds no description it
  can shorten and raises the ceiling anyway. This entry would have deferred the
  decision rather than placed it.
- More than one draft accumulates in `skills/`. The directory would then hold
  workflows nobody is reviewing, and a guard that no longer counts them is what
  let it happen quietly.

## Since then

The room was found on 2026-08-19 and both drafts were published. The trade this
entry says is asked at publication was asked there: of the two readings it
named, the maintainer took the merge of `typo3-extension-cleanup` and
`typo3-extension-conformance` over trimming further, and the ratchet moved as
well because the merge frees 232 of the 350 one description costs —
`D-SKL-064`.

The distinction went with them on 2026-09-01: `Installer::skills()` is the whole
directory now, so the budget counts every skill there is and the ceiling is what
the entry leaves standing — `D-SKL-087`.
