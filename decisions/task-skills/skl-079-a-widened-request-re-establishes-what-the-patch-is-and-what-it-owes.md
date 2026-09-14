---
id: D-SKL-079
title: 'A widened request re-establishes what the patch is and what it owes'
date: 2026-08-27
status: open
coveredBy:
  - SkillTest::aWidenedRequestReEstablishesWhatThePatchIsAndWhatItOwes
---

# D-SKL-079 — A widened request re-establishes what the patch is and what it owes

**Where the request widens after the patch is under way,
`typo3-core-patch-development` has the session say again what the change is. It
says again which branches it reaches and what it owes.**

Both rules that would have carried it are in the file and both belong to the
assessment. So a session whose task grew eight times re-derived its own scope
four times and threw away two rounds of work.

## Evidence

Measured on 2026-08-27 in this branch, against `feedback/2026-08-24-225243`.

- The session is `/home/benji/projects/typo3-cms` on `claude-opus-5[1m]`, from
  "bitte review mir 93177", roughly twenty user turns, twelve of them
  mid-tool-call. What it became, in order: review, amend, write the Gerrit
  comment, reply to four threads, build a follow-up patch that hardens. Then
  extend it to the client, screenshot it twice, rewrite the client fix twice,
  write a changelog entry, delete it.
- The report names the four re-derivations. Is the hardened part one patch or
  two, does it reach the older LTS, does it need the client, does it need an
  entry. Each is a question the skill already asks once.
- The skill's two nearest rules serve a moment that has passed. "Keep the patch
  one change" stands at the point where a session narrows. `D-SKL-075` put a
  paragraph under it for the list a patch closes on. Both are about work that
  arrives from outside the issue. The other is step 3's "Establish the blast
  radius here rather than meeting it while working", whose own sentence names
  this cost. Discovered step by step, it arrives after the session has
  characterised the change, and then the characterisation is what it has to take
  back. Nothing tells the session to take it back on purpose.
- The changelog rule was here and it arrived.
  `knowledge/documents/core/contribution/changelog.md` gives `Important` as
  "anything else that may require manual action" and says a casual bug fix owes
  none. The skill's own section says to decide the type from what the change
  does rather than from habit. The session reports that it read that rule hours
  earlier and reasoned from precedent when the moment came. That is neither a
  gap nor a delivery failure.
- `bin/cli hints:probe` on the wider request, re-establish what the change is
  and which branches it reaches once the request grows, reaches nothing. It
  returns its 108 candidates as the index. That is right, since the gap is not a
  statement about TYPO3.
- `D-SKL-062` is the lever next door and does not reach this. Its re-ask fires
  where the work enters a subject the opening did not name. It routes to
  `typo3_task_guide`, which answers which workflow a task belongs to. Here the
  workflow was right throughout, and what moved was the change's own
  characterisation inside it.
- Neither activation nor the crossing is at fault, and the report says so from
  the inside. The review skill's three disciplines each caught something, and
  the crossing into this skill fired on the user's own sentence.
- `D-SKL-075` already names this feedback from the other side, the same
  paragraph "fired, but only for the first split". It left its card in place for
  this judgement rather than folded it in.

## Decided

- Step 4 of the ladder, wording. The skill fired, the session read the rules,
  and what did not take is that both stand as one-time acts.
- Queued rather than made on the spot. It rewrites a published `SKILL.md`, which
  `judging.rst` puts on the far side of what a judgement may change in its own
  run. It is the call `D-SKL-075` made on the same file.
- At `normal`, set by what the wider request cost rather than by how many
  sessions reported it. One session reports it, and it counts the loss: two
  rounds of client-side work discarded, an entry written and deleted, four
  re-derivations.
- Where the paragraph goes is the todo's first step. Two places are candidates.
  Under "Keep the patch one change", where `D-SKL-075`'s paragraph sits and
  where a session decides what this patch is. At the foot of "Make the change",
  where a session meets a wider request. The decision between them is a read of
  the body this run has not made.
- The three things re-established are the skill's own vocabulary. The change
  type from step 2, the branches from the blast-radius paragraph, the entry from
  its own section. So the wording can point at them rather than restate them.
- The feedback's second ask is not part of this. `D-KNW-127` answers it, and the
  report shrinks to this half in the same commit.
- `coveredBy: []`, because nothing exists yet. What could hold this is a
  `SkillTest` sentence over the skill body, and it belongs to the commit that
  writes the wording.

## Assumed

- That a session which read a rule at the assessment reads it again at the
  moment the request widens. Nothing here measures that, and it is the
  assumption `D-SKL-062` records for the act list it moved into the
  `instructions`.
- That a wider request is a moment the session notices itself in. The one that
  reported did, since it says the honest answer was a different one each time.
  Nothing in this server sees a user turn that arrives mid-tool-call.

## Wrong if

- A session re-establishes all three and still ships the wrong characterisation.
  Then the wording is not the lever, and what is missing is a gate, which is
  what `feedback/2026-08-17-212218` reports for another skill.
- The wider request arrives and the session does not recognise it as such. Then
  the paragraph never fires whatever it says, and the acts around the moment
  have to name it the way `D-SKL-062` names its own.
- A reviewer asks for a squash of two patches out of one wider request. Then the
  re-established change bought a split the project did not want, and it is the
  same objection `D-SKL-075` carries as its first.

## Since then

The placement stands: the third paragraph of the narrow cluster. A wider request
is a session that decides again what this patch is. The session that reported
names that sentence as the one that fired. The foot of the make section is where
a session meets a wider request and not where it decides anything. `D-SKL-075`
settled the same shape one paragraph earlier. The paragraph points at the three
rather than restates them, so a rewrite leaves no second copy here. The first
**Wrong if** stays unmeasured for the same reason that entry is open on it.
