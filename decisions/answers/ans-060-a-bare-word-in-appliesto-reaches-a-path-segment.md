---
id: D-ANS-060
title: 'A bare word in `appliesTo` reaches a path segment'
date: 2026-08-07
status: open
coveredBy:
  - HintsTest::anExtbasePersistencePathIsNotAnsweredWithAnotherSubsystem
  - HintsTest::pruningThePathPatternsLeftBothSubjectsReachable
---

# D-ANS-060 — A bare word in `appliesTo` reaches a path segment

**A one-word `appliesTo` pattern matches the caller's paths as a prefix. So
`storage` in the FAL hint claims every path with a `Storage/` segment.** The
keyword tier then puts that hint above the ones that are about the subsystem the
path names.

Two sessions reported the same wrong answer from two tools on one day, and both
named FAL hints returned for Extbase persistence paths.

## Evidence

- `feedback/2026-08-07-132426` called `typo3_hint_lookup` with three paths under
  `typo3/sysext/extbase/Classes/Persistence/` and got `datahandler-basics`,
  `system-extension-boundaries` and `fal-storages-drivers`. It names the two it
  wanted and did not get, `persistence-reading` and `extbase-domain-mapping`,
  both present in `availableHints`.
- `feedback/2026-08-07-065259` reports the same three plus
  `extbase-domain-mapping` from `typo3_task_guide` with the same paths, hours
  earlier and in a different task. Its last line asks why those paths select
  `fal-storages-drivers` at all.
- Re-run here on 2026-08-07 against the corpus as it is: the call returns
  `fal-storages-drivers` with `keywords: 7`, `score: 0`. Seven is the length of
  `storage`, and the score is zero — nothing in the hint's own words answers the
  query. `persistence-reading` and `extbase-domain-mapping` do not come back.
- `Hints::scoreKeywords()` is where it happens. The matcher asks a bare-word
  pattern of the task with `carriesWord()` and of the paths with `carries()`.
  The second is a prefix match with no word boundary; `D-ANS-050` set that split
  so a query could reach `ThumbnailViewHelper.php`. `fal-storages-drivers` is
  the only hint in the corpus whose `appliesTo` carries the bare `storage`.
- The sort in `Hints::find()` reads `keywords` before `score`. A hint that
  matches one seven-character pattern and answers nothing therefore outranks one
  whose own words answer the query and whose vocabulary nobody anticipated.
- A measurement, not an argument, tested the bare `storage` dropped. It removes
  `fal-storages-drivers` from the Extbase call. Three FAL queries reached it
  through that pattern: "file storage driver configuration", "which storage does
  this file come from", "storage uid 0 public directory". Each still ranks it
  first, on its text alone. The change went back; it is the todo's material, not
  this entry's.

## Decided

- The false positive and the two absent hints are one finding with two halves.
  Only the first half is in the corpus. The pattern removed stops the wrong
  answer and does not produce the right one. Measured again with `storage` gone,
  `persistence-reading` and `extbase-domain-mapping` still do not come back.
- So the repair is the matcher rather than the data, and it goes to the queue
  instead of into this entry. It touches `src/`, which is the line
  [judging.md](../../documentation/records/judging.rst) draws around what a
  judging run may improvise.
- This entry decides that this is a defect and not an accident of wording. Two
  sessions, two tools, one read of the corpus. The caller named the subsystem in
  the paths, which is the least ambiguous thing a caller can give.

## Assumed

- The path is the stronger signal where the two disagree. A caller that names
  `typo3/sysext/extbase/Classes/Persistence/` has said which subsystem this is,
  and free text with "storage" in it has not.
- The three FAL queries measured stand for the pattern's worth. They are the
  wordings that reach it through `storage`, picked by hand rather than swept.
  `bin/cli hints:coverage` is the sweep and it has not run against this change.

## Wrong if

- ~~A sweep shows FAL queries that lose their answer when the bare pattern goes.
  That would say the pattern earns its keep and the fix is entirely in the
  rank.~~ Fired on 2026-08-08. The bare `record` and `records` dropped cost
  `EXT-02` its only hint, so the prune is not the fix and the rank is.
- Paths weighed above free text cost a hint a query reaches today only because a
  path was vague. That would say the two signals have no order.
- ~~The same shape turns up for a hint with no bare-word pattern at all. That
  would say the tier order is the whole of it and `appliesTo` is innocent.~~
  Fired on 2026-08-07. The worse offender was a pattern that claims
  `typo3/sysext/`, which every core path carries.

## Since then

The third **Wrong if** fired within the hour and two things change in place. The
false positive was never one hint. Another outranked the FAL hint on a path
fragment, and its own words answered the query no better. So what claims another
subsystem's path is any short pattern, with punctuation or not.

The second correction disproves **Decided** rather than refines it. This entry
took the word of the session that reported it for which hints are about the
subsystem. A read of both bodies says otherwise: one is the core query builder
and its restrictions, the other the model and its mapping.

## Since then

The matcher half landed and it is not what that paragraph proposed. Subsystem
detection never landed, because a measurement first showed the failure has a
simpler shape and a worse offender than either reported hint. One hint claims a
path fragment every core path carries. It matched on all five measured calls and
scored zero on all five, above the two hints that answer the question.

So what was wrong is the tier order rather than the patterns, which is the
question this entry left open.

## Since then

On 2026-08-08 the shape turned up on a third subsystem, and the fix this entry
had in the queue turned out to have no warrant. Three core frontend paths drew
two sitepackage authoring hints on two bare words that matched inside a class
name and a directory.

The corpus prune had its trial and the sweep disproved it, which is the first
**Wrong if** fired. The bare words dropped cost two contract cases their only
hint, and both earn their keep on the task text. What they should not claim is a
path, and no rule separates one bare word inside a class name from another.

## Since then

On 2026-08-24 a second measurement covered the scope tier this entry set aside,
on two calls where it does change the order. The read above found two
scope-bearing hints below the ones that answer. These two show the same hints
above them. In a brief for two core paths, `extension-asset-build` and
`project-build-and-scripts` fill two of the four slots and push `backend-ui` and
`javascript-unit-tests` into `omittedHints`. In one for two paths under
`packages/`, `core-tests` ranks first, above the hint that binds there.

So "filling slots nothing better competes for" holds where the answer lists
everything it matched, and not where it carries the strongest few. `D-ANS-097`
is what carries the question from here.
