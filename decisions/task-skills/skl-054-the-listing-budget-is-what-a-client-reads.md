---
id: D-SKL-054
title: 'The listing budget is what a client reads'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn
---

# D-SKL-054 — The listing budget is what a client reads

**The description budget counts the published skills rather than the directory.
So a draft costs no listing anything until the commit that publishes it.** The
room for a thirteenth description is a question about which twelve are worth
theirs, and publication asks it.

`D-SKL-026` set the ceiling at what the trim of 2026-08-08 left. The twelve
descriptions stand at 3597 characters of it, so the guard as written refused any
new skill file, a draft included. A workflow nobody can load would have paid for
room in a listing it does not appear in.

## Evidence

- The listing costs 3597 characters against a ceiling of 3600, measured on
  2026-08-18 over the twelve published skills. A thirteenth description of the
  length the other twelve average adds about 300, so no new skill fits unless
  one of them shrinks.
- `Installer::skills()` is the directory minus what declares itself a draft, and
  it is what every other consumer already reads. `Sdk\Skills` serves it,
  `ScopeTest` announces it, `SkillTest::everyPublishedSkillIsNamedByAnIntent`
  routes to it. `everyDescriptionIsWrittenToALengthOfItsOwn` was the one
  assertion that read the directory instead.
- A draft goes out to nobody, which a smoke test over the installer holds.
  `install` writes no draft, `install --drafts` writes it into that one project,
  and the next `update` takes it out again.
- The ceiling is a ratchet rather than a measurement. Its own comment says it is
  "what the trim of 2026-08-08 left, with room for a rename". The client's own
  bundled skills decide what remains, and they took 5997 of the 6000 characters
  a 200k session had that day.

## Decided

- The budget counts `Installer::skills()`. What it guards is what a client reads
  in one listing, and a draft is in no client's listing.
- The ceiling stays 3600. A raise now would spend room for a workflow nobody has
  reviewed, and the number is the one thing that holds the twelve short.
- Publication of a thirteenth skill owes the room. That is the commit where a
  description shrinks, or where two workflows turn out to be one. That is a read
  of all thirteen descriptions, which the session that writes the first draft of
  one of them cannot make.
- Rejected: a raise of the ceiling by the draft's own length. It reads as room
  made and is really the removal of the guard, because every later draft asks
  for the same.
- Rejected: the assertion left on the directory and a published description cut
  now. The choice of the description to cut serves room for an unreviewed file,
  which is the wrong order. `D-SKL-035` buys a new skill a baseline run
  precisely so that the domain stands settled before anybody spends anything on
  it.

## Assumed

- That `--drafts` is rare enough for the budget it does spend to be nobody's
  problem. It is a per-run choice in one project, taken by somebody who tries a
  draft out. That session is the one reader who knows the cost.
- That the twelve are not already over what a real client leaves. 3600 is a
  ratchet nobody has re-measured against a client since 2026-08-08, and this
  entry does not re-measure it either.

## Wrong if

- A draft goes out by the `--drafts` route into a project that then loses a
  published skill's description silently. The cost would be real and unpaid, and
  the count would have to include the drafts after all.
- The publication commit for `typo3-distribution-content` finds no description
  it can shorten and raises the ceiling anyway. This entry would have deferred
  the decision rather than placed it.
- More than one draft accumulates in `skills/`. The directory would then hold
  workflows nobody reviews, and a guard that no longer counts them is what let
  it happen quietly.

## Since then

The room turned up on 2026-08-19 and both drafts went out. Publication asked the
trade this entry says it asks. Of the two readings it named, the maintainer took
the merge of `typo3-extension-cleanup` and `typo3-extension-conformance` over a
further trim. The ratchet moved as well because the merge frees 232 of the 350
one description costs (`D-SKL-064`).

The distinction went with them on 2026-09-01. `Installer::skills()` is the whole
directory now, so the budget counts every skill there is and the ceiling is what
the entry leaves in place (`D-SKL-087`).
