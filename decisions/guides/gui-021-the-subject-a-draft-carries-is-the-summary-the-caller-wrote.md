---
id: D-GUI-021
title: The subject a draft carries is the summary the caller wrote
date: 2026-08-27
status: open
coveredBy:
  - CommitMessageGuideTest::aSummaryPassedBesideAMessageReplacesTheSubjectAlone
  - CommitMessageTest::theLengthCheckNamesWhatToCutAndWhoCutsIt
---

# D-GUI-021 — The subject a draft carries is the summary the caller wrote

**`typo3_commit_message_guide` measures the subject and hands back the summary
it got. A shorter one is a different claim and only the caller can make it.**

The check named the budget and stopped there. A session that had just learned
its subject was four characters over wrote four candidates by hand and measured
them in a shell. It spent a call to confirm the one it chose.

## Evidence

- The reported query was re-run against the server as it is now (2026-08-27, the
  subject exactly as `feedback/2026-08-25-114617` records it, `workflow="core"`,
  `releases=["main","14.3","13.4"]`, `issue="110534"`).
  `summary-length-preferred` came back with the same numbers the report quotes:
  56 characters, a 47-character summary, 9 for the prefix, 43 left. The draft
  carried the 56-character subject. The report holds unchanged.
- What a cut is, from the session's own four candidates. "Make the git based CGL
  suites work in worktrees" became "Make CGL suites work in git worktrees". That
  drops three words and moves a fourth to qualify something else. No rule over
  the string produces that, and one that deletes or cuts produces a subject that
  says what the commit did not do.
- The reflow the report contrasts it with is not the same act. `wrapBody()`
  moves line breaks and keeps every word; a cut removes words. This repository
  draws that line for its own prose in `AGENTS.md`. `bin/cli prose:format`
  rewraps and `prose:check` only reports the long sentence, "since a long
  sentence can be the right one and a rewrite driven by a counter produces two
  short sentences saying what one said".
- The call that would have saved the round trip already works and stands nowhere
  a caller reads. `message` plus
  `summary="Make CGL suites work in git worktrees"` ran on 2026-08-27. The
  subject changed, the body, the `Resolves:`, the `Releases:`, the `Change-Id:`
  and the `Signed-off-by:` stayed, and the length check cleared.
  `CommitMessageGuide::answer()` merges the explicit arguments over the parsed
  ones, and only a code comment says so.
- The report's second claim does not hold. A real `Signed-off-by:` came back in
  the draft of both runs; `parse()` refuses `Co-Authored-By` and
  `Claude-Session` and drops nothing else but its own placeholder. What the
  session read was `missing-sign-off` on a draft that never carried one.
- The 52 characters are the whole subject line's. The Contribution Guide
  appendix that `knowledge/documents/core/contribution/commit-messages.md` names
  as its source says this (read 2026-08-27). "Keep the whole line below 52
  characters if possible, but below 72 in any case". The document put the 52 on
  the summary and the 72 on the subject. That makes the reported 56-character
  subject correct and the check that fired on it wrong.

## Decided

- The length checks say how many characters the summary has to lose and that the
  draft keeps the summary as it arrived. They say which call measures a
  replacement. Each is one sentence in `subjectLength()`, which
  `summary-too-long` and `summary-length-preferred` share.
- The composed call has its name where the caller stands: passed beside a
  message, `summary` replaces the subject alone. That is the shape the session
  that reported assembled by hand.
- The document says the 52 is the whole subject line's, verified against the
  source it already cites. `D-FBK-052` is why that half sits in no queue: the
  lookup happened in this run.
- Rejected: a cut to the returned subject the way the body reflows, which the
  report asks for first. It is the difference between a moved line break and a
  deleted word.
- Rejected: a `summaryCandidates` parameter that measures several subjects in
  one call. It is a schema change bought with one round trip, well below what
  `D-FBK-027` asks of a new capability. A caller able to write four candidates
  can measure them where it wrote them.
- Rejected: a check that fires at exactly 52. The guide says "below 52 if
  possible" and the hook refuses nothing under 73 (`D-GUI-020`), so a check
  there reports a defect nothing objects to. The budget of 43 the answer states
  for a nine-character prefix is this threshold rather than the guide's word,
  one character apart on a preference.
- The tool description stays. It promises a draft that is ready to commit, and a
  subject over the preferred length is one the hook accepts.

## Assumed

- The appendix read on 2026-08-27 is the rule that holds a contributor. The
  checkout states no length rule but the hook's, which measures 72 and nothing
  else — `D-GUI-020`.

## Wrong if

- A session reads the new check, shortens the summary, and still assembles the
  message by hand. Then the composed call was not the gap and the lever is
  somewhere else.
- Sessions still report the confirmation call. Then the measure belongs here
  after all, and the parameter rejected above is worth its schema.
- A caller passes a summary that is shorter and says something the diff does
  not, because it read the check as a demand for characters. Then the cut named
  in characters costs more than it saves.
