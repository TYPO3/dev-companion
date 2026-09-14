---
id: D-FBK-027
title: The server builds what costs its caller round trips
date: 2026-08-03
status: open
---

# D-FBK-027 — The server builds what costs its caller round trips

**A capability is worth the calls it takes off the caller. A cost that lands
here to save several there is the trade this server is for.**

`D-FBK-020` established that a session pays one context per call rather than one
per token. That is a fact about how a session reads; this is what follows for
what gets built.

## Evidence

- The measured case is a Forge issue. `feedback/2026-08-02-144511` and `145217`
  record what it cost. `WebFetch` returned 403, and `curl` with a browser-like
  user agent returned **200 with a challenge page**, a success status around a
  non-answer. The default user agent finally returned JSON. That then needed a
  search by hand because the decision sits in `journals[]` rather than in the
  issue body. Four round trips and a trap that reads as a result, for one
  question.
- A lookup answers the same question in one call, with the fields named in the
  schema. The caller pays one call and reads no HTML.
- Nothing about that cost is specific to one session. Every core task that
  starts from an issue pays it again. That is what separates it from a fact a
  caller reads once in its own checkout.

## Decided

- A capability gets taken on when it removes round trips the caller pays again
  and again. The count is the argument, and the feedback that reports it usually
  counts it — that is the number a judgement reads.
- The cost that moves here is the point rather than the objection. Two APIs that
  belong to somebody else become a surface this repository maintains, and
  nothing here reports it when they move. That is the price for a gain the
  caller gets in every session. It is the reason a one-session convenience is
  not the same case.
- What does not qualify: a fact the caller reads once from its own checkout. An
  answer whose cost is the model's read rather than its calls. And anything
  whose lookup would answer `unavailable` often enough that the caller pays a
  call for nothing. The failure shape is part of the design, not a follow-up.

## Assumed

- That the caller's call budget is the scarce thing. It is on the clients
  measured here. A client that batches calls or charges differently would move
  the line, and no such client has a measure.
- That a maintained surface is cheaper for everybody than a rediscovery of the
  same access path in every session. Four sessions in one directory rediscovered
  the Forge one; none of them wrote it down where the next would find it.

## Wrong if

- A lookup built on this rule spends more time on reports that it could not
  answer than on answers. Then the call it saved was never the expensive part
  and the recipe was the right answer.
- The rule starts to justify tools for tasks nobody repeats. The signal is a
  `taken on` whose evidence names one session and no count.

## Since then

Neither **Wrong if** has fired in a way that asks for a change. The first has no
field data and what stands in for it points the other way. The recorded answers
carry sixteen `answered` across the five tools that reach a host against one
`unavailable`. That one is a version outside the covered set, refused before any
question went out. No feedback reports one of these tools with the answer that
it could not answer.

The second has its measure under `D-FBK-026`, whose second **Wrong if** watches
the same signal from the ladder's side. The gap is a second session and not the
research.
