---
id: D-GUI-013
title: The brief names the sweep a change owes
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::aBriefForAChangeNamesTheDeprecationSweepItOwes
---

# D-GUI-013 — The brief names the sweep a change owes

**`typo3_task_guide` names the deprecation sweep as what a task that writes PHP
still owes, in the brief itself rather than only in `skills/base.md` step 5.**

A session reads the order once, at the start. The clause that says to read it
again sits inside the step the reader has just exempted itself from.

## Evidence

- `feedback/2026-08-18-074327` is a 404 in a blog installation that became three
  commits across `Classes/ViewHelpers`, `Classes/Service`, `Classes/Controller`
  and `Configuration/DataHandler`. It reports steps 2, 4 and 5 of the order
  skipped. It names the cause as one walk of the order against the task as it
  first stood.
- Re-run on 2026-08-18 in this repository, with the change type the session
  passed. The `operations` brief says "Pass changeType where the task does
  change something". It names the focused diff, the test coverage and the commit
  message as what it left out. The sweep is not among them.
- The same re-run with `changeType=bugfix` and those four paths. `nextTools`
  names `typo3_changelog_lookup` "for what 14 changed about this area": no
  `type: deprecation`, no call per declared major, no `tag` bound. So the brief
  a caller reaches after a new classification does not carry the step either.
- `bin/cli hints:probe "deprecation sweep when a task turns into a code change"`
  matches nothing. The obligation is in [`base.md`](../../skills/base.md) and in
  no answer this server composes.
- The wording of step 5 is not what failed. The feedback quotes its re-entry
  clause back correctly: a review asked to make the change starts the order
  again. That clause is the last paragraph of the step whose exemption the
  caller has already taken.
- Half of the report has its answer as it stands. `omittedHints` came back `[]`
  and the brief printed `HINTS_COMPLETE` verbatim beside it. So the sentence
  step 4 sends the caller to read was in the answer the session held.
- That misread has a sighting already.
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)
  records a session on 2026-08-14 that quoted `"omittedHints": []` and then
  asked for a machine-readable form of the sentence beside it.
- The corpus carries the cause rather than this one report.
  `feedback/2026-08-18-081159` reports a skill choice made once against the
  first frame, from another task shape in the same checkout. The **Since then**
  of
  [`D-SKL-049`](../task-skills/skl-049-the-gate-at-the-end-of-a-workflow-waits-for-its-corrections.md)
  already reads three sightings of the shape across two directories.

## Decided

- **Step 2 of the ladder, delivery.** The rule exists, it is correct, and it is
  in no answer the caller holds at the moment the task changes shape.
- **Queued rather than closed on the spot.** The change is a sentence in
  `src/Tool/TaskGuide.php` and a clause in a published skill, which
  [judging.rst](../../documentation/records/judging.rst) reviews rather than
  improvises. It needs no lookup about TYPO3.
- **`normal` rather than `low`.** More than one session reports the cause, from
  two task shapes.
- **Refused: a second, machine-readable form of the "did the brief carry
  everything" signal.** `omittedHints` is that form and a required key of the
  declared schema. What stands instead is one clause in step 4 of `base.md` that
  names it. That step points at the sentence and warns off `hints` while it
  never names the field two sessions have now read and doubted.
- **Rejected: step 5 restated in the brief.** The sweep is five paragraphs, and
  the brief points at the skill that carries the order wherever one owns the
  work. What the brief owes is the obligation and its axes, one call per
  declared major per tag, rather than the step's argument.

## Assumed

- That a caller re-reads the brief and not the skill file when the task changes.
  The feedback says that of itself, and it is why it names `typo3_task_guide` as
  the place the rule would have reached it.
- That the sweep transfers outside the core. `base.md` addresses it to the
  package rather than to the core repository, and every path in this report is
  an extension's.

## Wrong if

- A session with the obligation in its brief skips the sweep anyway. The
  placement was then not the lever, and what remains is the end gate
  [`D-SKL-049`](../task-skills/skl-049-the-gate-at-the-end-of-a-workflow-waits-for-its-corrections.md)
  waits on.
- A session whose change touches no TYPO3 API reports the line as noise.
  `base.md` exempts that case on the files a change touches, and a brief cannot
  read them. So the obligation would arrive where the step does not apply.
- A session names `omittedHints` as the answer to its own ask before the clause
  exists. The field was then readable as it stood, and the two recorded doubts
  were about something else.

## Since then

Both placements stand. The sweep sits in the checklist of every brief whose
change type produces a change. A brief that changes nothing names it beside what
it already leaves out. Each **Wrong if** is about what a session does with a
line it now holds.

On 2026-08-24 a session skipped the sweep on a core patch and reported it, and
the first did not fire. It never ran the guide again after the ask to rework the
change, so the placement was never in a brief it held. That tests the call
rather than the line, and `D-SKL-072` is where it went.
