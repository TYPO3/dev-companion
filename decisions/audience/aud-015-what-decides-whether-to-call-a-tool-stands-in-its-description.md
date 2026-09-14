---
id: D-AUD-015
title: What decides whether to call a tool stands in its description
date: 2026-08-27
status: open
readings:
  - 2026-09-01
coveredBy: []
---

# D-AUD-015 — What decides whether to call a tool stands in its description

**A fact that decides whether to call a tool stands in that tool's description.
A client that defers reads the input schema only once the caller has chosen the
tool.**

`typo3_commit_message_guide` names the branches that take a patch today when a
call comes without `releases`. Its description says the tool checks the trailer
against them, which is a check on what the caller supplies. The sentence which
says the tool supplies the set stands one property down in the input schema.

## Evidence

- [`feedback/2026-08-26-223348`](../../feedback/archive/2026-08-26-223348-no-answer-for-which-branches-a-bugfix-s.md)
  wrote `Releases: main, 14.3, 13.4` for a core bugfix off `git branch -r`. It
  flagged the line to the user as unverified, and never called the tool. Its own
  reason: nothing in the description suggested this server would know the
  project's release-support state.
- Re-run on 2026-08-27 through `CommitMessageGuide::answer()` with
  `workflow="core"`, a `BUGFIX` summary, `issue: 76202` and no `releases`. The
  answer names `main, 14.3, 13.4` as the lines that can take a patch at all. It
  says a bug fix goes to `main, 14.3`, and to an older line only where the
  severity earns it.
- So the guess was wrong rather than unverified. The answer names `13.4` on a
  priority bug fix or a grave defect, and the reported patch was neither.
- The clause the session read is the last of six sentences: "the trailer is also
  held against the branches that take a patch today". It promises validation, so
  a caller who does not hold the set reads it as something to have already.
- What that caller needed is the `releases` property of `inputSchema()`. It
  reads "Left out, the draft carries a RELEASE_TARGET placeholder and the checks
  name the lines taking a patch today".
- The client defers schemas. The same session's
  [`feedback/2026-08-26-223325`](../../feedback/archive/2026-08-26-223325-the-stated-entry-point-and-both-fitting-skills.md)
  records that it loaded `typo3_task_guide`'s schema through a search call.
  [`feedback/2026-08-19-090401`](../../feedback/archive/2026-08-19-090401-tools-arrived-as-bare-names-with-no-schemas-and.md)
  reports tools that arrived as bare names with no schemas at all. The sentence
  that would have caused the call therefore sits behind the choice it had to
  cause.
- Both halves predate the feedback. `knowledge/release-lines.json` landed on
  2026-08-05 with
  [`D-ANS-058`](../answers/ans-058-the-release-lines-a-trailer-claims-are-a-lookup.md),
  and the description's branch clause on 2026-08-24 with `0a26c2b9`.
- The placement
  [`D-ANS-104`](../answers/ans-104-the-maintained-release-lines-are-placed-where-a-task-names-a-branch.md)
  made that same day did not reach it either. Its carrier is
  `typo3_gerrit_lookup`, and the same session's strength report
  [`feedback/2026-08-26-223414`](../../feedback/archive/2026-08-26-223414-four-things-in-the-forge-tool-that-carried-the.md)
  says why. The inline `reviews` field of `typo3_forge_lookup` gave it the
  change numbers and states, and "it is also why typo3_gerrit_lookup never had
  to be opened".
- Four sessions have now built this set by hand. One on 2026-08-05 from a count
  of trailers on forty commits, two on 2026-08-24 and 2026-08-26 from
  `git branch -r`. One on 2026-08-25 from the dates `git for-each-ref` prints,
  which a rule lookup corrected in the next turn.
- This entry established nothing about TYPO3. The branch facts are the ones
  `knowledge/release-lines.json` already carries, and its windows are what the
  re-run printed.

## Decided

- The judgement is [judging.rst](../../documentation/records/judging.rst) step
  4, wording. Not 1a, because `release-lines.json` holds the windows and the
  guide prints them. Not 1b, because no verb is absent and the tool that owns
  the trailer answers it. Not 3, because `knowledge/task-intents.json` names
  this tool for a core patch before the push. The `routing` block of
  `knowledge/server-scope.json` ends the review entry with it.
- The description arrived and did not take, which is what puts it at 4 rather
  than 2. What did not arrive is the sentence in the input schema, and a move of
  that sentence up is the same change.
- **Queued** rather than closed on the spot, because the change is in `src/`.
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md)
  keeps that half of the line whatever the judging run holds.
- The surface is `CommitMessageGuide::description()`. The branch clause turns
  from a check into a supply. The tool names the lines that take a patch and
  where a change of this shape goes, whether or not the call carries `releases`.
- The set itself stays out of the description.
  [`D-ANS-073`](../answers/ans-073-what-can-take-a-patch-and-where-this-one-goes-are-two-readings.md)
  is the boundary. The description says the tool answers the question and never
  what the answer is, because every one of those states is a date that passes.
- The priority is `normal`, which is what
  [`D-AUD-014`](aud-014-a-description-opens-with-what-the-callers-own-route-cannot-do.md)
  set the same surface at on the same day, on one measured failure. This one has
  four sessions behind it and a trailer that was wrong.
- Nothing holds it, so no requirement exists. The assertion would have to be
  that one description says what one of its own parameters says. A check that
  reads for that is a keyword the next rewrite moves.

## Assumed

- That the session would have called the tool had the description said it
  supplies the set. It names the description as the reason it did not, and does
  not say how far into it it read.
- That the deferral is the client's for every tool rather than for the ones it
  happened to load. Two feedback report it, and neither says a description
  arrived truncated.

## Wrong if

- A session reports that it went to `git branch -r` after it read a description
  that says the tool names the branches. Then the description is not what
  decides the call, and what remains is a wider placement than `D-ANS-104` made.
- A feedback reports the clause as noise. A caller who writes a commit message
  in their own repository meets a core trailer statement where the tool's
  subject belongs.
- The next session calls the tool and goes to the checkout all the same. That is
  the run `D-ANS-058`'s first **Wrong if** has waited for since 2026-08-05.
