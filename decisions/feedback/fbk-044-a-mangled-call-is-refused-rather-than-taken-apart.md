---
id: D-FBK-044
title: A mangled call is refused rather than taken apart
date: 2026-08-04
status: open
coveredBy:
  - FeedbackTest::aFieldCarryingTheCallItArrivedInIsRefused
  - FeedbackTest::aReportQuotingTheMarkersIsStillRecorded
---

# D-FBK-044 — A mangled call is refused rather than taken apart

**The channel refuses a feedback field that arrives with the frame of its own
call in it. The thirty-four already stored get one repair by hand.**

Two batches of feedback arrived with their suggestion buried in the middle of
the observation. The parameter had closed with a tag named after itself and
swallowed everything behind it.

## Evidence

- 34 of the 270 feedback in the corpus carry the frame: 20 from 2026-07-29,
  before the `model` field existed, and 14 from 2026-08-04 by `claude-opus-5`.
  Two clients, five days apart, so it is not one session's slip.
- The shape is the same in all 34. The observation ends in `</observation>`,
  then one `<parameter name="suggestion">` block, closed with `</suggestion>`,
  with `</parameter>` or with nothing, sometimes with `</invoke>` after it.
- The 2026-08-04 session is readable end to end — transcript `4b813d94` in
  `/home/benji/projects/site-new`, 13:23 to 18:02, no compaction. It made 456
  tool calls. The 14 malformed ones are all `typo3_feedback_record` and every
  other call is clean, 47 `Write` and 55 `Edit` with long multi-line content
  among them. The two feedback schemas arrived through `ToolSearch` at 17:56,
  two minutes before the first record.
- What the client stored as the call's input names five arguments — `category`,
  `model`, `tool`, `query`, `observation` — and no `suggestion`. The schema has
  it optional, so nothing rejected the call and the writer omitted a heading for
  an argument that never arrived.

## Decided

- **Refused, not reconstructed.** A server that takes another party's broken
  protocol frame apart guesses at the shape of somebody else's bug on every
  write. Afterwards nothing tells the guess from the report. A refusal is
  actionable while the session still runs. The 14 calls were seconds apart, and
  the first rejection would have corrected the other 13.
- The check is **structural rather than topical**. It sees a field that ends in
  the call's end tag, or a parameter that opens a line of its own. A feedback
  *about* this failure quotes those markers inline and mid-sentence, and a test
  holds that such a report is still recorded.
- The check covers all three prose fields. Which one swallows the rest depends
  on the order the parameters came out in, and nothing here decides that order.
- The 34 stored ones get their repair in the commit that adds the guard. The
  markers go, the suggestion moves into the section it belongs to, and no other
  word changes. Every removed line had a check back into the file it came from.
  One diff a reviewer can read is not the rule this entry refuses to be.
  `D-FBK-039` did the same for 43 mangled names.

## Assumed

- That the emitted call closed each parameter with a tag named after itself. The
  transcript keeps the parsed input rather than the model's text. So what stands
  established is the result, the tail of the call inside the first field, and
  the cause is an inference from it.
- That a refused session re-sends. Every model this repository has feedback from
  retries a tool error, and the message names what to change.

## Wrong if

- A session meets a refusal and files nothing at all. Then the report is lost
  where a split would have kept it. The call taken apart with a marker that says
  so is the answer after all.
- A legitimate report meets a refusal because it quotes the markers.
  `FeedbackTest` holds the one case that exists, and a second shape would show
  the check reads the subject rather than the structure.
- The frame arrives with the parameters in another order, or from a client that
  builds them differently, and the check does not see it.
