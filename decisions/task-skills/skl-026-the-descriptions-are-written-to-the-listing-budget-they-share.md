---
id: D-SKL-026
title: The descriptions are written to the listing budget they share
date: 2026-08-08
status: open
coveredBy:
  - SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn
---

# D-SKL-026 — The descriptions are written to the listing budget they share

**Every published description fits the budget the whole listing shares. A client
that runs out of it drops a description whole rather than shortens it.**

The description is the only part of a skill read before it is chosen
(`R-SKL-010`), and it is not paid for by its own skill. A client reads all of
them in one attachment against one character budget. So every sentence one skill
spends comes off the skill that the listing names alone.

## Evidence

- The arithmetic, read off the client this repository's work runs in, the native
  binary of Claude Code 2.1.226, functions `x4t` and `zNs`. The budget is
  `floor(context window in tokens × bytes per token × skillListingBudgetFraction)`
  **characters**. The fraction defaults to 0.01. The bytes default to 4 for the
  3.x and 4.x models the binary lists by name and 3 for everything else. A 200k
  session is therefore 8000 characters on a 4.5-generation model and 6000 on a
  5-generation one, and a 1M session is 30000. Both defaults are settings —
  `skillListingBudgetFraction`, `skillListingMaxDescChars` — and
  `SLASH_COMMAND_TOOL_CHAR_BUDGET` overrides the result outright.
- What an entry costs and what overflow does. An entry is
  `- <name>: <description>` with the description capped at 1536 characters, plus
  one newline between entries. Over budget, the client's own bundled skills keep
  their full entry. Every other skill appears as `- <name>` and buys its
  description back for its length plus two. The order is
  `usageCount × 0.5^(days since last use / 7)` with a floor of 0.1. That score
  is zero for every skill nobody has used, so on a fresh install the twelve tie
  and the sort is stable. What decides which of them keeps a description is the
  order the client discovered them in.
- What the twelve cost before this change, measured on 2026-08-08: 7153
  characters of description, 7520 of entries, 7177 to buy every description
  back.
- What the client spends before them. The bundled listing measured in one
  session that day, Claude Code 2.1.226, artifacts on, is 5997 characters over
  15 skills. Of those, `dataviz` and the four `artifact-*` skills are 1832.
  Those entries have protection, so they come off the top.
- The four combinations, with the twelve as they were. At 6000 against the full
  bundled listing the floor is 6366 with the twelve listed by name alone. So
  none of them can carry a description at any length. At 8000 against it, 1634
  characters remain and three of the twelve had a description. At 6000 against
  the same client without the artifact skills, 1470. Only a 1M session listed
  all twelve.

## Decided

- The twelve shrink to the sides they own and the words a user brings, and what
  summarised the workflow is gone. 7153 characters of description become 3110,
  7520 of entries become 3477, and every description bought back costs 3134
  rather than 7177. Six of the twelve have a description where three had one, at
  8000 against the measured client. All twelve have one at 8000 against a client
  whose bundled listing is nearer 4000. Which sides each description names stays
  as it was. `R-SKL-010` is what the trim stood against, not what it traded
  away.
- A test holds the total rather than a sentence, because a number in a sentence
  is true on the day of its write. That is `R-SKL-021` and
  `SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn`.
- Trimming further was rejected. What remains is the sides and the triggers. The
  combination that still does not fit, 6000 against a bundled listing of 5997,
  does not fit at any length. The floor is over the budget with the twelve at no
  description at all.
- How many skills this server publishes is **not** decided here. Twelve fit a 1M
  session whole and cannot all carry a description in a 200k one on the current
  model generation, whatever they say. So the question is which of them a
  session on 200k should be able to see. It needs a read of which skills get
  activated, which nothing here has.

## Assumed

- That the bundled listing is roughly this size. The measure is from one client,
  on one machine, on one day. Feature flags this repository cannot see gate
  which bundled skills the listing carries at all.
- That other clients divide their listing the same way. This is Claude Code's
  arithmetic. The installer writes these skills into other agent clients too,
  and this entry read none of theirs.
- That a dropped description is what keeps a skill out of a task. `D-AUD-003`
  measured a session whose skill listing arrived in full and which still worked
  from the checkout. So the listing is one channel and not the only one.

## Wrong if

- A recorded run reaches a session where the twelve arrived described and the
  work was still done without them. Then the budget was never the obstacle for
  that task, and what remains to suspect is the wording rather than its length.
- A client whose bundled listing is well under the budget still drops one of the
  twelve. Then the arithmetic above is a misread, and the entry cost is the
  first thing to measure again.
- A description trimmed here no longer matches a task it used to carry, a
  feedback or a run that names a request that reached nothing. Then a side or a
  trigger went out with the summary, and it goes back at the cost of another
  skill's.

## Since then

The third **Wrong if** happened on one skill. The trim took out the requests a
user's own words reach and put back the step clause cut the same day. A session
then read the result as a branch-switch workflow. The ratchet holds a total and
not a wording, so whoever writes a trim reads what it leaves behind against
`R-SKL-010` and `D-SKL-024`.

The first **Wrong if** fired on a transcript rather than a recorded run. A
session received every description in full, byte-identical to this checkout's
and open on the request's own two words. It worked from the checkout for
thirteen calls. So neither the budget nor the wording was the obstacle
(`D-SKL-033`). The ceiling moved on 2026-08-19, because a thirteenth skill does
not fit a ratchet set to what twelve cost at any wording.
