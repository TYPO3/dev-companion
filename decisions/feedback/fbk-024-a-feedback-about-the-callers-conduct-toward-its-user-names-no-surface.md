---
id: D-FBK-024
title: A feedback about the caller's conduct toward its user names no surface
date: 2026-08-02
status: confirmed
---

# D-FBK-024 — A feedback about the caller's conduct toward its user names no surface

**A feedback about when the caller stops and hands control back to its user is
not walked down the ladder. Every rung names a surface this server owns, and the
turns between a session and the person it works for are none of them.**

That is a result and not a dismissal. Whether this server should acquire such a
surface is the question `feedback/2026-08-01-003931` now waits on. It is not one
a judgement run may answer for text that lands in somebody else's project.

## Evidence

- `feedback/2026-08-01-003931` is a named sibling of the series behind
  [`D-FBK-006`](fbk-006-a-name-is-cut-where-the-feedback-starts-to-differ.md),
  and its Observation is two halves rather than one subject. The first is
  conduct. After the user supplied the `f:then` fix and the "data comes from the
  record" correction, the session "did not stop and confirm direction but
  continued working autonomously". And "conversely that it sometimes stopped at
  the wrong moment instead of continuing to verify". The second is verification:
  the **Suggestion** asks it to "finish verification (e.g. actually rendering
  the preview) rather than stopping or shipping unverified work".
- The second half is a subject a sibling owns.
  `bin/cli hints:probe "verify the rendered backend preview before reporting the work done"`
  reaches `content-elements` at `appliesTo(15) + text(195)` and nothing else.
  That is the same landing
  [`D-KNW-017`](../knowledge/knw-017-a-verification-question-is-routed-to-the-layer-that-verifies-it.md)
  measured from four other phrasings, on `feedback/2026-08-01-003533`. It is
  that entry's routing gap off a fifth sentence, not a second gap. As this
  branch stands, `todo/open/2026-08-02-200948` serves `003533`.
- The first half reaches nothing, in its own words or in a task's.
  `bin/cli hints:probe` on the feedback's own `Query` line, "when to stop after
  user help vs continue autonomously — unclear behavior", matches no hint. Forty
  come back as the index. So does "after a user correction confirm the corrected
  direction before continuing".
- A miss on a hint probe is the wrong instrument here, because the corpus is not
  where such a rule would sit. `knowledge/server-scope.json` declares fifteen
  `covers` topics and nine `doesNotCover` ones, and every one of the twenty-four
  is about TYPO3 or about the installation around it. The nearest boundary is
  *running an installation: server and container setup, deployment, backups, the
  editorial use of the backend*. Its `why` sends it away as a different subject
  with different sources. Nothing on either list is about how the session
  conducts itself.
- The skills do not carry it either, and not by oversight. Grepping `skills/`
  for the caller's user returns seven lines; five are about an end user or a
  user-controlled value. The two that mean the person the caller works for are
  `skills/base.md` and `skills/typo3-extension-upgrade/SKILL.md`. The first says
  "whatever language you are speaking with the user", which is a rule about how
  to phrase a query. The second says "Dropping a major is the user's decision,
  never one taken to make the code simpler". So where this server does name the
  user, it names a **subject that belongs to them**, never a **moment at which
  to stop**. The second is a shape it has never taken.
- The feedback cannot supply the evidence for its own first half, and says so.
  "The exact boundary behavior is ambiguous in my transcript and was never
  recorded; it should be captured precisely from the user's account." A rule
  from that is a guess about what the user wanted, and a skill is a file no
  release of this server corrects.
- This entry established nothing about TYPO3. Every probe and every grep above
  is a query against this repository as it stands on 2026-08-02.

## Decided

- No card comes from the verification half. It is `003533`'s subject, judged as
  `D-KNW-017`, and a second card for one step is the overlap
  `bin/cli todo:claim` was taught to warn about. This is
  [`D-FBK-021`](fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md)'s
  map applied to a half rather than to a whole feedback. `003931` is not a
  summary, but one of its halves is a subject a sibling already owns. The reason
  not to judge it twice is the same reason.
- The conduct half is **proposed** rather than queued or closed. It reached no
  rung. There is no absent statement, because the subject is not TYPO3. No
  absent tool or skill, because a skill orders a task and this orders a
  conversation. Nothing to deliver, route or reword, because nothing exists to
  move; and no decision whose **Wrong if** it satisfies.
- What it would cost, which is why it is not recommended from here. Only two
  surfaces reach a caller before it acts. The `instructions` block at
  initialize, which is 2048 characters on one client's evidence
  ([`D-ANS-004`](../answers/ans-004-the-instruction-budget-is-2048-characters-on-one-clients-evidence.md)),
  and `skills/base.md`, read at the start of a task. Every caller in every
  project pays for a turn rule on either. It competes for the budget with "Start
  every task with typo3_project_describe". That is the entry point
  [`D-AUD-003`](../audience/aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md)
  says the instructions exist to carry, because the tool descriptions never
  arrive.
- The question goes up rather than gets an answer here, and the card stays in
  hand with it. That keeps
  [`D-FBK-017`](fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)'s
  invariant whichever way the answer goes.
- Nothing on another branch changed.

## Assumed

- That the two halves are the whole of the Observation. They came off its
  sentences, and the second is what its **Suggestion** asks for.
- That a rule of this shape belongs to the client harness and the project's own
  conventions rather than to a knowledge server. Nothing here has read one. It
  is an account of what this server is, not a measure of what a client does.
- That nobody can recover the boundary the user meant. The feedback says so
  about its own transcript, and the user was not asked here.

## Wrong if

- A later feedback reports the same boundary with the user's account attached.
  The evidence would then exist, and the question this waits on could stand
  against something rather than against a gap in a transcript.
- The half turns out to be sayable as a TYPO3 subject after all. *A preview is
  verified by rendering it, not by reading the template* is a statement
  `content-elements` could carry. It is most of what this feedback wants. Then
  the conduct half is smaller than it reads and `D-KNW-017`'s bond covers it.
- A conduct rule arrives in `instructions` or `skills/base.md` by some other
  route, unpriced. This entry is then the record that somebody named the cost
  once and left the question open, and the ladder gained a rung nobody declared.

## Confirmed on 2026-08-04

The question went to the user, and the answer is that no such rule exists. The
turns belong to the client harness and the project's own conventions. That was
the second **Assumed**, off what this server is rather than a measure. It is now
what the maintainer says, so the surface stays as it was.

The **Wrong if** stands and none of the three is spent. The answer came against
the cost of the two surfaces rather than against a report of the boundary.
