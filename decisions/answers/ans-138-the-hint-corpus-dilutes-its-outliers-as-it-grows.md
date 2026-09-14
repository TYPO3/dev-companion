---
id: D-ANS-138
title: The hint corpus dilutes its outliers as it grows
date: 2026-09-02
status: open
readings:
  - 2026-09-10
---

# D-ANS-138 — The hint corpus dilutes its outliers as it grows

**The sweep ran again because the mean crossed its ceiling, and it found the
safe range wider than before. So the ceiling measured the wrong direction.**

## Evidence

- Four statements added to `backend-modules` took the mean hint body from 300 to
  302 words over 164 hints, and `MAX_MEAN_BODY_WORDS` failed the suite.
- The sweep ran again on 2026-09-02, with the corpus fixed and `UNDILUTED_WORDS`
  varied. Every case of `theSweep` passes from 20 to 500. At 505 «how do I write
  a good sonnet» gets an answer instead of an empty result.
- The same sweep at a mean of 266 gave 120 to 320. Both ends moved outward while
  the corpus grew by 36 words of mean. So 200 sits nearer the middle of the
  range than it did on the day of its choice.
- The two docblocks disagreed about what 320 was. The floor's says it is the
  upper end of the safe range for `UNDILUTED_WORDS`. The ceiling's reads it as a
  corpus mean at which the sonnet query gets an answer. Only the first was ever
  measured.

## Decided

- `UNDILUTED_WORDS` stays at 200. It is inside a range that has widened twice,
  and nothing in the sweep argues for a move.
- `MAX_MEAN_BODY_WORDS` is 340, which is about one growth spurt above the mean
  it was last measured at. It stands as a trigger for a second measurement
  rather than as a predicted failure, because that is what the two measurements
  support.
- The instruction on it keeps its substance and gains sharper words. When it
  fails, the sweep runs again and the docblock takes what it found. Raising the
  number without that is what it still refuses.

## Assumed

- That more hints dilute an outlier rather than sharpen it, which is what both
  measurements show and what the weight exists to do. One more measurement in
  the same direction would make it a property rather than an observation.
- That the sweep is the whole test. It is twelve cases, and a recall failure
  outside them is invisible here as it always was.

## Wrong if

- The next measurement narrows the range. Growth would then cut both ways, and
  the ceiling is a failure line after all. Its source is then the measured end
  rather than the distance travelled.
- A corpus this size answers something it should not, and the sweep still
  passes. The twelve cases would then be too few to calibrate on, which is a
  larger question than the constant.
