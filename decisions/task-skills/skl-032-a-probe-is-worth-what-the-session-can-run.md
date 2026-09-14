---
id: D-SKL-032
title: 'A probe is worth what the session can run'
date: 2026-08-10
status: open
---

# D-SKL-032 — A probe is worth what the session can run

**The scratch-probe permission carried the review wherever the session could run
a suite against the question. The same session asserted for hours wherever it
had to look at the answer.**

One debrief reported both, and they are one boundary rather than a success and
four failures.

## Evidence

- `feedback/2026-08-10-101751` is the strength. The session added throwaway
  tests below `Build/Sources/TypeScript/backend/tests/`, ran the JavaScript unit
  suite against them, read what they printed and put the tree back. Two reasoned
  findings became measured ones. The part worth more is that the probe refuted
  one of its own predictions. It had called the `@starting-style` entry
  animation dead code, and the probe measured opacity 0.135 one frame after the
  class change.
- The dropped-candidate rule is what made that refutation land as a written
  disposition rather than a quiet deletion. A lookup rather than taste disproved
  three of the five candidates it dropped, which is `D-SKL-007` read from the
  other end.
- The costs in the same session are all on the other side of the same line.
  Whether a CSS feature is inside the browser baseline
  ([`D-KNW-066`](../knowledge/knw-066-the-browser-baseline-is-a-release-day.md)).
  Whether the component sits where it should on a scroll
  ([`D-KNW-068`](../knowledge/knw-068-looking-at-a-backend-change-is-a-suite-the-core-already-carries.md)).
  How to reach the installation that has the content
  ([`D-KNW-069`](../knowledge/knw-069-a-browser-in-a-container-reaches-a-site-on-the-router.md)).
  A suite run and a read of its output answer none of them. The session shipped
  three blind corrections into the first two before the developer stopped it.
- Where the session did have a command, it used it correctly with no further
  help. It held the Gerrit patch set's commit against `git rev-parse HEAD` to
  prove it reviewed the current one. It read the reviewer comment on the
  previous patch set out of Forge rather than report a finding somebody had
  already made.

## Decided

- Nothing changes in the skill. The paragraph and the dropped-candidate section
  are what the session names. A strength is evidence about a boundary rather
  than a decision to confirm
  ([`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)).
- This entry writes the boundary down because the next editor of that paragraph
  cannot see it. What it grants is worth exactly as much as the commands the
  session can name. It says "run a targeted suite" because that is the kind of
  measurement this server can point at. A permission to look at something is not
  the same sentence and does not follow from it.
- Where a suite cannot answer, what the session needs is a route rather than a
  permission, and the three entries above are that route. Whether the review
  skill should carry the looking step as well as the running one is a question
  for the run that has both — not a paragraph written from this reading.

## Assumed

- That the probe is what produced the two measured findings and the refutation.
  The session says so and nothing here re-ran the review.

## Wrong if

- A session with the route now written down still asserts a positional finding
  it could have looked at. Then the gap was the permission after all, and the
  skill is where it belongs.

## Since then

The route is still there and reachable.
`bin/cli hints:probe "is this CSS feature inside the browser baseline"` returns
`css-browser-target` at `appliesTo(26) + text(279)`, and the two hints that
answer looking at a backend change — `browser-tests` and
`browser-test-accessibility` — are in the corpus beside it. `D-KNW-066`,
`D-KNW-068` and `D-KNW-069` all stand.

Nothing has tested the **Wrong if**. The only feedback since that names a probe
or a positional finding is the pair this entry was written from, both stamped
2026-08-10, so no session has had the route written down and asserted anyway.
That is the run this entry is waiting for, and it is a review of a backend
change rather than a reading here.
