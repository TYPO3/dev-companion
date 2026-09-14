---
id: D-FBK-038
title: What decides a breaking removal is the caller, not the marker
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::whetherARemovalIsBreakingIsAnsweredWithoutTheMarker
---

# D-FBK-038 — What decides a breaking removal is the caller, not the marker

**The corpus and the corrected boundary answer both tool-gap reports from the
patch-review session, and neither becomes a tool.**

`D-FBK-037` took the API-stability one on as a lookup. The core's own changelogs
say a lookup would answer beside the question.

## Evidence

- `Breaking-110319-RemovedUnusedInternalBootstrapMethods` removes three
  `@internal` methods of `Bootstrap` and stands as **Breaking**. Its **Affected
  installations** section names who called them — non-Composer bootstrap scripts
  — and says the extension scanner reports the usages.
- `Important-108796-InternalShortcutClassesRenamedToBookmark` renames
  `@internal` classes and says outright that "some extensions might have used
  them although they were internal". It stands as **Important**, with no Impact
  and no Affected installations section at all.
- So the marker is an input. A lookup that answers `@internal: true` would have
  told the reviewer of 110319 that the removal was not breaking. That is the
  opposite of what the core concluded, a confident wrong answer in place of a
  read.
- The corpus already answers the sibling question this way. The first statement
  of `deprecated-apis` sends the caller to read the `@deprecated` declaration
  itself rather than offers a lookup for it. Nobody has reported that as a gap.
- The actual gap is reachable now and was not before.
  `bin/cli hints:probe "is removing this internal method breaking"` matched
  nothing, with every hint back as the index.
- The reviewer read the class anyway. A patch review opens the changed file to
  review the diff in it, so the annotation costs no call. That is the case
  `D-FBK-027` names as one that does not qualify. A fact the caller reads once
  from its own checkout, and a cost that is the model's read rather than its
  calls.

## Decided

- No API-stability lookup. `deprecated-apis` carries what `@internal` says and
  that it sits on the class and on the member. It carries that it does not
  settle whether a removal is breaking. It carries what does: whether anything
  outside the core calls it, which is what a Breaking entry's Affected
  installations section states.
- The `breaking` intent settles that question before it says how to write the
  entry, because a wrong answer to it produces the entry.
- An absent annotation stands as no statement either way, because that is the
  conclusion a marker-shaped answer invites.
- No tool that reads git, as in `D-FBK-037` and for its reasons.
  `git show --name-only --format=%B HEAD` is one command the caller already
  runs, and git carries none of the access-path trap that decided the Forge
  case.
- Both feedback have their answer and sit in the archive. The card `D-FBK-037`
  queued is gone rather than rewritten; what it was to design does not exist.

## Assumed

- That the two changelogs are the rule rather than two picks. They are the two
  the corpus stood against, and no sweep of every `@internal` removal across the
  covered majors ran.
- That a reviewer who is told the marker does not settle it will look for the
  call sites. The statement names the extension scanner and the Affected
  installations section so that step has somewhere to go.

## Wrong if

- A session reads the statement and files a removal of an `@internal` member as
  Important where the core would have called it Breaking. That would mean the
  caller's name as the test was not enough to make it happen.
- Somebody sweeps the `@internal` removals of a covered major and finds the
  marker does decide it, with these two as the exceptions. Then the lookup is
  back on the table and this entry is what the measure stood against.
- A review of a patch with no checkout asks for the annotation and has no file
  to read. That is the case that would put both declined tools back together.
