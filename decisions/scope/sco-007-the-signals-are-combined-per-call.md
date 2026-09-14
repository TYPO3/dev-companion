---
id: D-SCO-007
title: 'The signals are combined per call'
date: 2026-08-01
status: revoked
revokedBy: D-SCO-008
---

# D-SCO-007 — The signals are combined per call

**Scope::isOutsideCore() combines the signals `R-SCO-001` orders over the whole
call: it folds every path into one string and one boolean comes back. `META-03`
is therefore a feature and not a wording change.**

Nothing decides per path, and nothing can say the audience is uncertain.

`META-03` hands the server two paths of different audience in one session and
asks what applies to each. Read against `Scope`, the question was which half of
`R-AUD-002` the code lacks: the per-path decision, or only the uncertain answer.

## Evidence

- Measured against the contract's own paths.
  `isOutsideCore(['packages/acme_events/Classes/Domain/Repository/EventRepository.php'])`
  is `true`,
  `isOutsideCore(['typo3/sysext/core/Classes/Database/Query/QueryBuilder.php'])`
  is `false`, and the two together are `false` in either order. The
  `typo3/sysext/` short circuit returns before the code looks at the `packages/`
  marker. So the extension path silently gets the core verdict, which is
  verbatim the first outcome `META-03` forbids.
- The haystack is `implode(' ', $paths) . ' ' . $text`. After that line nothing
  can tell which path contributed which marker. So no caller of the flag could
  draw the distinction even if it wanted to. `D-SCO-003` already draws the
  payload distinction per line; the audience it draws it from is per call.
- Of the four call sites, two can receive two paths at all — `typo3_hint_lookup`
  and `typo3_test_run_guide` take a `paths` array. `typo3_task_guide` takes one
  `area` string, so nobody can put the prompt of `META-03` to it in the first
  place.

## Decided

- The code lacks two things rather than one. The unit of decision has to become
  the path. The answer has to gain a third value for the case `R-AUD-002` names:
  signals that disagree with nothing to resolve them. A `bool` has room for
  neither, so this is the rename `D-SCO-006` predicted: `outsideCore` becomes
  the audience of a path.
- Against a mixed call that answers `true` for the whole of it. That keeps one
  verdict per session and merely picks the other side of it. Against a second
  call-wide flag beside `outsideCore`, which is the same verdict under one more
  name.

## Assumed

- `R-SCO-001`'s order survives the change. It orders signals about one piece of
  work, and per path all of them are still available. Only the free text and the
  kind of installation are per call. Both sit at the weak end of it, which is
  where a path with no marker of its own should land anyway.

## Wrong if

- Two paths of different audience turn out to be rare in recorded sessions. The
  common case is one path against prose that disagrees with it. Then the cheap
  answer is enough: a call whose signals disagree names both audiences and
  applies neither silently. That is `META-03`'s second required outcome without
  the first.

## Revoked on 2026-08-22

The statement describes a call that returns one boolean over the whole call, and
`D-SCO-008` opens with the line that that call is gone. Its successor answers a
case of the `Knowledge\Scope` enum per path — `D-KNW-005` — so nothing here
reads as a claim about today. The order the entry established is what
`D-SCO-005` carries and `R-SCO-001` states.

Nothing disproved it. The entry stayed `open` while the API under it changed
twice, which a read of the corpus for the names it spells found.
