---
id: D-SKL-074
title: 'A skipped step is named where the report is written'
date: 2026-08-24
status: open
coveredBy:
  - SkillTest::theReportNamesTheStepsOfTheOrderItDidNotReach
---

# D-SKL-074 — A skipped step is named where the report is written

**The obligation to name a step the session did not reach belongs where the
session writes a report, not inside each step it is about.**

It stands twice today, both times inside the step it exempts. Three sessions
from three task shapes took the exemption and reported nothing.

## Evidence

- **The sighting.** `feedback/2026-08-24-110949`: a core patch review of a local
  commit in EXT:impexp in `/home/benji/projects/typo3-cms`, one modified class
  and two new test files. It took step 5's exemption correctly — a review
  produces no change — and did not name the step in its report. Its own read is
  the position. The sentence is the last of nine paragraphs, after two other
  conditions. It is the only instruction of that step that has to survive into a
  document written half an hour later.
- **The file it describes is today's.** `skills/base.md` has not changed since
  `04ffc96d` (2026-08-21), which rewrote what each runtime lookup adds. Step 5's
  closing sentence is `7f5657d9` (2026-08-14), the commit
  [`D-SKL-037`](skl-037-the-sweeps-exemption-names-what-a-task-produces.md)
  wrote. `skills/typo3-core-patch-review/SKILL.md` gained two paragraphs after
  the report landed at 11:09:49+00:00, `ea48a398` at 16:19 and `db92a9ea` at
  17:22 local. Neither is in its *Report* section.
- **Two earlier sightings, from another task shape and another directory.** Both
  are `/home/benji/projects/blog` on 2026-08-18.
  `feedback/archive/2026-08-18-070611` reports the obligation "stated twice and
  complied with neither" on a DDEV boot and reads the failure as placement
  rather than wording. `feedback/archive/2026-08-18-074327` reports steps 2, 4
  and 5 skipped on a task that changed code, with nothing that names the skips.
  Three sessions, three task shapes — a boot, a change, a patch review.
- **Both statements sit inside a step.** Step 2 says it of itself, that a reader
  cannot tell a step passed over in silence from one the session dropped. Step 5
  closes with it. Nothing at the end of the order says it, and no task skill's
  report section does. `typo3-core-patch-review` closes on the checklist's
  surfaces marked assessed, unassessed or not applicable, which are surfaces of
  the patch rather than steps of the order.
- **What holds it today is blind to where it stands.**
  `SkillTest::theDeprecationSweepIsSkippedWhereNoTypo3ApiIsTouched` asserts the
  sentence is somewhere in `skills/base.md`, and `R-SKL-005` carries it inside
  its paragraph about the sweep. A move leaves both green.
- **The exemption itself held.** The session read the property, applied it to a
  shape the examples do not name and got the right answer. That meets
  `D-SKL-037`'s first **Assumed** on a third shape. What failed is the reporting
  half of the same entry.
- **The corpus was probed.** `bin/cli hints:probe` on the feedback's own subject
  matched nothing and returned 102 candidates as the index, which is the
  expected answer. The subject is this repository's own skill file rather than
  TYPO3.

## Decided

- **Step 4 of the ladder, on where the sentence sits rather than on what it
  says.** It arrived in the active skill, and the session read it whole and
  understood it, since it quotes both halves of the exemption. The ladder names
  "buried below the part that answers the common case" as step 4's own evidence.
- **Queued rather than closed on the spot.** `skills/base.md` goes into somebody
  else's project as `references/base.md`, where the next release of this server
  does not correct a wrong sentence. `R-SKL-005` and `SkillTest` carry the same
  wording.
- **It stays written once, in the base.** `R-SKL-005` already says a skill
  states what it adds to the order and never a second copy of it. The place the
  caller is when it takes the exemption is the end of the order rather than the
  middle of one step.
- **Rejected: the feedback's own suggestion**, a clause in each task skill's
  *Report* section. Every published skill carries the base, and nobody can
  correct any of those copies. That is why `D-SKL-037` rejected a review-shaped
  sweep in the review skill.
- **Rejected: the gate `D-SKL-049` weighs as its place.** That entry recommends
  a wait because nobody could attribute a fourth intervention against three
  unmet corrections afterwards. This is one obligation that exists, moved to
  where a session discharges it, which is the kind of correction the same entry
  credits. Its 2026-08-24 note carves
  [`D-SKL-072`](skl-072-a-workflow-handover-names-the-calls-the-next-order-restarts-with.md)
  out of the same corpus on that ground.
- **The priority is `normal`**, set by three sessions from three task shapes,
  and by a change that is one paragraph in one file.
- **The wording is not written here**, and neither is what becomes of the two
  statements now standing in steps 2 and 5. Both are the todo's first question.

## Assumed

- That a session writing a report reads the end of the order it started from.
  All three sightings say the report comes last and far from the step, and none
  of them measures what it reread.
- That one statement where the session writes the report beats two where it does
  the work. It stands twice today and no session complied either time.
- That the three shapes report the same failure. Each names the position rather
  than the sentence, and none disputes what the sentence asks.

## Wrong if

- A session with the obligation at the end of the order still closes a report
  without naming the step it skipped. The position was then not the lever, and
  what remains is the gate `D-SKL-049` weighs.
- A session reads the exemption, stops at the step and never meets the closing
  line. Then the statement inside the step is what reached the shapes that did
  comply, and the move traded a near statement for a far one.
- A session names the step and a reader still cannot tell the report from one
  that walked past it. That is `D-SKL-037`'s third **Wrong if** by way of this
  entry, and the debt was the naming rather than the placement.
- The next sighting comes from a session that read its skill's *Report* section
  and not the base's order. Then the obligation belonged in the skill after all,
  and "written once" is what it cost.

## Since then

The work answered the two statements the entry left open. Step 5's closing
sentence is gone and step 2 keeps its "Say so.". So what a step asks locally
stayed local and the obligation over the order did not stay in either. The
sentence now stands last in *The order*, after **Then** read the checkout. It
covers every step rather than the two exemptions it stood under. A report names
a step an earlier answer discharged for the same reason it names a skipped one.
`SkillTest::theReportNamesTheStepsOfTheOrderItDidNotReach` holds where it
stands, which is what the old assertion could not.
