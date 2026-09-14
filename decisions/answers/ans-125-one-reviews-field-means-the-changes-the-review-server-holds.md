---
id: D-ANS-125
title: One reviews field means the changes the review server holds
date: 2026-08-27
status: open
coveredBy:
  - ForgeTest::aSingleIssueJoinsTheProseHandlesWithWhatTheReviewServerHolds
---

# D-ANS-125 — One reviews field means the changes the review server holds

**`reviews` means the same thing on every path. That is the union of what the
review server answers for the issue number and what the issue's own text
names.** Today the two calls fill one field name from two sources, and the
cheaper call is the authoritative one.

## Evidence

- `Forge::reviewed()` fills an enumerated row from `changesForIssues(...)` — a
  Gerrit query over commit messages, one per twelve rows, which `D-ANS-069`
  decided and paid for.
- `Forge::entry()` fills a single issue from `self::reviews($texts)`, which
  parses change handles out of the description and the journal. That is what
  `D-ANS-064` decided, and its reason holds: the handle was already in the
  payload and only as prose.
- Neither entry saw the other. `D-ANS-064` is 2026-08-08 and named the field for
  a prose handle. `D-ANS-069` is the same day and filled the same name from the
  review server for the other shape of answer. Nothing in the schema, the
  description or either entry says the two come from different sources.
- `feedback/2026-08-27-145448` is the cost. The session read `reviews: []` on
  issue 97614 and took it as evidence that no change was in flight. It had
  already used a full `reviews` on enumerated rows as a "somebody tried this"
  signal in the same session. It reports that it inferred the construction
  rather than read it, and it says what the wrong conclusion would have cost.
  That is a duplicate patch, paid for by a core reviewer rather than by the
  session.

## Decided

- The union, not one or the other. The prose half lacks a change whose commit
  message names the issue. The Gerrit half lacks a change a comment discusses
  whose commit message never named the issue. An empty array then means neither
  source has one, which is the statement the caller already took it for.
- Deduplicated by change number, since a change that is both named in a comment
  and found by commit message is one change.
- The cost is one Gerrit query per issue read, against the one per twelve rows
  `D-ANS-069` already accepted for the cheaper answer. A single-issue read is
  the call a caller makes a handful of times, and it is the one a patch decision
  rests on.
- The field's description says its provenance either way, because a union of two
  sources is not self-evident from an array of change numbers.

## Assumed

- One session, and it pushed nothing. What it reports is an assumption it made
  and did not act on. So the duplicate patch is the cost it names rather than
  one anybody paid.
- The review server answers a number the same way for one issue as it does in
  the batched form. `changesForIssues()` is what both would use.

## Wrong if

- The union turns out to carry a change the issue has nothing to do with. That
  would say the commit-message filter needs the same narrower form on this path
  that `D-ANS-069`'s batched form assumes.
- A session reports the extra call as the thing that made a single read too
  expensive. That would say the provenance sentence alone was the answer.
- Nobody reads `reviews` on a single issue as a statement about Gerrit once it
  is one. That would say the field was never what the decision turned on.
