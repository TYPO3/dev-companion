---
id: D-FBK-021
title: A summary feedback is judged against its series, not on its own
date: 2026-08-02
status: open
---

# D-FBK-021 — A summary feedback is judged against its series, not on its own

**A feedback that summarises a session which filed one report per subject gets
its judgement as a map of its halves onto those siblings. It gets no second walk
down the ladder.**

Such a summary carries no lever of its own. Every subject in it is a subject
somewhere else too. A judgement of it as a report produces what the ladder's
preamble names: a gap with a fourth entry written next to three that exist.

## Evidence

- `feedback/2026-08-01-002951` is the first feedback of the series behind
  [`D-FBK-006`](fbk-006-a-name-is-cut-where-the-feedback-starts-to-differ.md),
  the one that kept the shared first line. Its **Suggestion** names four things
  to cover, and each is the whole subject of a named sibling filed within six
  minutes of it:

  | Half of the summary                        | The sibling that owns it                            |
  | ------------------------------------------ | --------------------------------------------------- |
  | `f:if` needs an explicit `f:then`          | `003448`, with the working markup; `003000`         |
  | preview data is the record, not TypoScript | `002926`, `002928`, `002930`, `002745`              |
  | Record API field access                    | `002928`, whose query is three Record API phrasings |
  | test request type and dataset priming      | `003003`; `003929` for the database half            |

- Two of those four are already judged, and by another card than this one. The
  record variable is
  [`D-KNW-014`](../knowledge/knw-014-the-record-a-v14-preview-template-is-handed-is-a-subject-this-server-owns.md),
  step 1a, with `todo/progress/2026-08-02-133246` for `002745`. The Fluid half
  and the functional-test half await a judgement, with their own cards in
  `todo/open/`.
- A series produces summaries in the plural. `003103` is a second one of the
  same session, open, with a card of its own. Its query is "whole-session
  roundtrip audit: backend preview, Record API, Functional test, Fluid".
- The summary's query reaches less than its halves do. `bin/cli hints:probe` on
  the four joined by semicolons reaches `project-extension-tests` and
  `core-tests`, and nothing else. Asked one at a time the same four reach
  `fluid-templates`, `content-elements`, `frontend-records` with
  `tca-schema-api`, and `project-extension-tests`. So the joined query misses
  three of the four hints its own parts find. That is
  [`D-ANS-021`](../answers/ans-021-the-manual-lookup-says-why-a-short-query-ranks-better.md)
  measured on a hint probe rather than on the manual.
- This entry established nothing about TYPO3, on purpose. Whether an `f:else`
  really forces an explicit `f:then`, and what `project-extension-tests` already
  says about a primed database, is the research `003448` and `003003` owe.

## Decided

- The judgement of a summary is the map above, and it stands here so nobody
  derives it a second time. What a wrong call costs is one knowledge entry
  written twice, from two judgements of one session. No reader could tell which
  one either rests on.
- No todo comes from this feedback. Every step it would name is already a step
  on a sibling's card. A second card for the same step is the overlap
  `bin/cli todo:claim` reports.
- Nothing on another branch changed. `002930`, `003000` and the record-variable
  todo are in hand in other worktrees on this entry's day. So this feedback on
  their `Serves:` lines would edit one file in two worktrees at once.
- Whether the summary may go to the archive now is **not** a decision here. It
  is the question on the card, and the card stays in `waiting/` for the
  feedback. That keeps
  [`D-FBK-017`](fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)'s
  invariant either way the answer goes.

## Assumed

- That an agent reads a close of its summary differently from a close of its
  specific reports. Nothing has measured it. `D-FBK-017` assumes the opposite
  for the ordinary case, which is why the archive is a question here rather than
  an answer.
- That the map is complete, and no half of the summary stands orphan. It came
  off the four clauses of the **Suggestion** against the `Query` line of every
  feedback that session filed.

## Wrong if

- ~~A half turns out to have no sibling. The map is then wrong and the summary
  is the only report of that subject. The ladder applies after all.~~ Fired from
  the fourth summary on. The map was right each time. An orphan half is what the
  summary adds, and the ladder ran over that row rather than over the file.
- ~~Every sibling lands and the summary is still open, because each commit that
  closes archives only the feedback it worked off. The summary would then need
  an owner rather than a map.~~ Fired on 2026-08-03 on the first three
  summaries. What they needed was a look at where their siblings had landed. The
  map itself closed the ninth.
- A session judges a summary with a walk down the ladder anyway, and writes the
  entry that a sibling's todo writes again a week later. That is the failure
  this entry stands against, and nothing checks for it.

## Since then

Ten readings, compacted here on 2026-08-28 with the form (`D-DOC-066`). Every
one of them took a summary feedback apart into results that landed in the
entries they were about. `D-FBK-017`, `D-FBK-020`, `D-KNW-100` and a dozen more
name what each changed, which is the rule at work rather than a series of its
own. The reports are in `feedback/archive/`, where the account of one session
belongs.
