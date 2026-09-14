---
id: D-SCO-008
title: The path decides, and the answer may say it cannot
date: 2026-08-01
status: revoked
revokedBy: D-KNW-005
---

# D-SCO-008 — The path decides, and the answer may say it cannot

**Scope::isOutsideCore() is gone. Scope::audienceOf() answers `core`,
`outside-core` or `uncertain` for one path.**

The two tools that take a `paths` array answer per path. A call that placed
nothing says so instead of hands over the core's process by default.

What
[`D-SCO-007`](sco-007-the-signals-are-combined-per-call.md)
measured, built. The two things it named as absent are the two things here: the
unit of decision, and a third value.

## Evidence

- `Build/` is not evidence of the core. The core keeps `Build/Scripts/` and
  `Build/Sources/` there, and every checkout under `.checkouts/` has both. But
  an extension that compiles anything ships a `Build/` too. So the shape counts
  only where the manifest at the root has not already said this is not the core.
  That manifest is what `Instance` reads a checkout's kind from, so nothing new
  reads the disk for it.

## Decided

- `Scope` reads the path before the call. A path with `typo3/sysext/` or an
  outside-core marker gets its answer from that alone. `Scope` reads what the
  call says only for a path that carries nothing of its own. That is the whole
  of what keeps `META-03` apart. It is a strict refinement of `R-SCO-001`'s
  order rather than a change to it. The ranks are the same, and only the
  haystack shrank from "every path plus the prose" to "this path".
- `uncertain` is what the last rung returns when there is no installation to
  read. It is not a hedge over ambiguous prose. The order resolves a prose
  disagreement, and it has to stay resolved. "Not TYPO3 core, a composer package
  under vendor bk2k" names both sides in one sentence. It is the case where
  nothing spoke at all, which used to come back as `false` and read to every
  caller as the core.
- `typo3_task_guide` keeps one verdict, because a caller asks it about one
  `area` and cannot give it two. This entry deliberately names that a limitation
  of the tool rather than of the decision. The audience is per path either way,
  and what the tool lacks is a parameter, not a rule.
- The payload keeps `outsideCore`. `AGENTS.md` has the schemas add fields rather
  than rename them, and a client that validates against the declared output
  schema would break on a removal. It now means "the whole call is outside",
  which is what it always was for the calls that had one audience. `audiences`
  carries the per-path answer beside it. The rename `D-SCO-006` predicted
  happened where `D-SCO-006` located the pull: in the code. There every session
  that touched the flag derived a scope sentence from its name again.

## Assumed

- A path with no shape of its own is rare in a call that has one with a shape.
  Where it is not, an ordinary two-path call splits into a core block and an
  undecided one, and the answer is longer for no gain. `composer.json` beside a
  sysext path is the shape of that.

## Wrong if

- A caller reads the split answer as two answers to two questions. A mixed call
  now returns hints matched separately per audience. A caller that reads only
  the first block gets half of its own question back. If that shows up, the
  split belongs one level higher. Then the agent makes two calls and the server
  says so, rather than one answer with headings.
- `uncertain` turns out to be the common answer rather than the rare one. Every
  call from a client started outside an installation lands there, and a notice
  that appears on most answers is a notice nobody reads. Then the last rung is
  wrong, not the value. The server would have to ask at initialize time, once
  per session, what places the work, instead of guess per call.

## Revoked on 2026-08-02

The **Decided** that the payload keeps `outsideCore` did not hold. All five
output schemas lost the field: it is `scope !== core`, and every tool that
carried it already carried the scope beside it. Scope::audienceOf() is
`Scope::of()` and answers a case of the `Knowledge\Scope` enum rather than a
string. `outside-core` split into `project` and `extension`, the three audiences
`R-AUD-001` names. See
[`D-KNW-005`](../knowledge/knw-005-scope-is-the-one-word-for-which-work-a-statement-is-for.md).
