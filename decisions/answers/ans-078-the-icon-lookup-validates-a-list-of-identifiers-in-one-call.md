---
id: D-ANS-078
title: The icon lookup validates a list of identifiers in one call
date: 2026-08-12
status: open
coveredBy:
  - IconLookupTest::severalIdentifiersAreAnsweredOneByOneInOneCall
---

# D-ANS-078 — The icon lookup validates a list of identifiers in one call

**`typo3_icon_lookup` takes several identifiers at once and answers each one
registered or not, beside the ranked search it already answers.**

A validation of three identifiers read out of one Fluid template cost three
calls. Each of them answered the question in one field and spent the rest of the
payload on a rank of neighbours nobody had asked about.

## Evidence

- `feedback/2026-08-11-055257` counts it: one exploratory call, which was the
  right shape, and three existence checks after it. Each answered
  `exactMatch: true`; `actions-open` carried 22 suggestions with it.
- The cost is this server's own rule. The `instructions` the server sends at
  initialize say to call this tool before the session chooses or emits a backend
  icon identifier. Four skills repeat it. So a change that touches three icons
  pays three calls because we told it to. That is `D-FBK-027`'s "paid
  repeatedly" in its sharpest form: the repetition is not the session's habit,
  it is our instruction.
- The tool has no shape for it. `query` is one string, and
  `Icons::looksLikeIdentifier()` decides per call which mode the answer is in.
  Two identifiers in one query fall to the ranked search, which is the wrong
  answer rather than a slower one.
- A second, list-valued argument beside the free-text one is already how this
  server takes several of something. `typo3_hint_lookup` and `typo3_task_guide`
  both take `paths` beside `task`, and place each entry on its own.
- The same change answers the repeated `scope` paragraph the feedback also
  reports, and not a drop of it. It is on every answered lookup on purpose. The
  tool gets a query rather than a task. So an identifier handed over without it
  is usable in a frontend template, where it is wrong. One call carries one copy
  of it.

## Decided

- Built, as a list-valued argument beside `query` rather than as a second tool.
  The subject and the source are the same; what differs is the shape of the
  answer, which is what an argument is for.
- A validation answers per identifier and carries no rank for the ones that hit.
  What the installation registers is the answer, and neighbours of a correct
  identifier are noise.
- A miss keeps its suggestions. `D-ANS-016` is why. An identifier the
  installation does not register is the case where the next step is the answer.
  This is the tool whose misses look most like hits.
- The schema is still open, and it is the card's first step. Whether the
  validated identifiers come back in the `icons` list the tool already declares
  with a per-entry verdict, or in a section of their own. The first keeps one
  result set and is the recommendation; the second is what a client that renders
  `icons` as matches would need.

## Assumed

- That a caller with several identifiers has them all before the first call. The
  reported case read four out of one template, and an identifier discovered by
  the answer to the previous one would not batch.
- That the rank stays worth its place for a hit nobody asked to validate.
  Nothing measures which of the two modes callers reach for more often.

## Wrong if

- Callers still pass one identifier at a time with the list argument. That would
  say the batch was never what cost them and the shape of the answer was.
- A caller reads the per-identifier verdict as a rank anyway. A client that
  renders the list without the verdict field would hand a caller an absent
  identifier that looks registered. That is the failure `D-ANS-006` and the
  suggestion-versus-match split already exist to prevent.
