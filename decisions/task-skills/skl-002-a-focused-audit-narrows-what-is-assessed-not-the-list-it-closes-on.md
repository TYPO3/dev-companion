---
id: D-SKL-002
title: A focused audit narrows what is assessed, not the list it closes on
date: 2026-08-02
status: open
coveredBy:
  - SkillTest::aFocusedRequestNarrowsTheReadingAndNeverTheSurfaceList
---

# D-SKL-002 — A focused audit narrows what is assessed, not the list it closes on

**A focused audit narrows what a review assesses, never the surface list its
report closes on.**

The report marks a surface nobody asked about as out of scope. To drop it from
the list is the failure `R-SKL-004` stands against, and a cheaper review is not
a reason to reopen it.

## Evidence

- The feedback of 2026-07-31 18:36 asks for "a quick-start mode that skips the
  full surface list for focused reviews (e.g. security-only or
  configuration-only audits)". It came from a session that had just used the
  skill for a full audit. That session reported the order, the severity rubric
  and the finding gate as what made the review work. It is the cost of a design
  reported by a session that also reported its benefit.
- The permission it asks for already exists and is one clause deep in a
  reference. `references/checklist.md` line 3 says "Read the relevant sections
  for a scoped review; read all sections for a full extension audit." No other
  place in `skills/` or `scenarios/` names a scoped review at all.
- The two operative steps in `SKILL.md` never mention the request. Line 20
  builds the work list from "the checklist's surfaces narrowed to the ones this
  kind of checkout can have". The kind of checkout narrows it and the request
  never does. Line 98 closes on "the surface list written in step 5, every entry
  marked assessed or unassessed". A session on a security-only review reads the
  clause, then the skill tells it to write the whole list and answer every entry
  on it. So the step that builds the work list outranks the clause.
- `bin/cli hints:probe "quick start audit skip full surface list"` reaches
  nothing, and "security-only focused conformance review scope" reaches only
  `security-sinks`, which is about sinks. Nothing in `knowledge/` was ever meant
  to carry this: how much of a task to do is a skill's job.
- Against it, `R-SKL-004` rests on runs that went narrow. A run that read three
  XLF files and never asked what governs them. A run that filed translations as
  "assessed and clean" with `source-language="de"` on screen. Two runs produced
  no finding about static analysis in a repository with no analyser. One had an
  absent `Documentation/` that appeared neither as a finding nor as unassessed.
  In every one of them the cheap review and the thorough one produced the
  same-looking report.

## Decided

- Step 4 of the ladder, wording. The rule is here and the skill delivered it,
  since `SKILL.md` orders the checklist read. It did not take. The sentence that
  permits a scope sits in a reference, while the sentences that build and close
  the work list do not know about it.
- The work goes to the queue rather than into the judgement run. It rewrites the
  two operative steps of a published `SKILL.md`, and
  `SkillTest::theBaseIsEstablishedBeforeTheCheckoutIsOpened` asserts one of them
  as a literal string in an ordered block. That is a skill's contract, and
  `judging.md` puts it on the far side of what a judgement may change on the
  spot.
- Rejected: the feedback's own suggestion taken literally. A mode that skips the
  full surface list removes exactly what makes an absent surface visible. The
  report then cannot separate "you did not ask about this" from "there is
  nothing here". That is the distinction the surface list exists for and the one
  every run in `R-SKL-004` failed.
- Rejected: closing the feedback on the clause that already exists. The
  reference answers it and the instruction contradicts it, and a session that
  follows the instruction follows the right file.

- `SKILL-11` is what measures the behaviour, and the test above is a sentence.
  `SkillTest::theBaseIsEstablishedBeforeTheCheckoutIsOpened` and
  `SkillTest::anAssessmentAsksBeforeItJudgesAndSaysWhatItDidNotAsk` hold that
  the list exists and has its answers. None of the three can see whether a
  session narrowed what it read.
## Assumed

- That the two halves come apart. That a review can cut what it *reads* to the
  requested surfaces while what it *lists* stays whole. The cost is one line per
  surface in the report. Nothing has measured that. If the read is what the list
  drags along, then a focused mode is not a rewording. The honest answer is then
  that this skill has one mode.
- That a focused request is legible to the session at all. "Security-only" was
  the feedback's example, and a request that names no surface leaves the cut to
  a judgement the skill would then have to describe.

## Wrong if

- A review with a focused prompt writes a focused surface list and reports it
  clean. So a reader cannot tell an unrequested surface from an unexamined one.
  Then the cut reached the list after all and the clause has to go rather than
  grow.
- Or the reverse: runs given a focused prompt keep on with the full list
  unprompted. In which case nothing outranked anything, the cost the feedback
  reports is the read and not the list. A paragraph then went into a file that
  carries the load because it is short.

## Since then

The wording landed on 2026-08-02, and what settled the assumption first is two
runs the decision had not read for it. Both halves come apart in them. One
report already keeps four kinds of "no finding here" apart, so a fifth is the
cheaper one to write. The other closes three surfaces off the scope call without
a look into the checkout. So a list without a read is what the runs already do.

What neither measures is the cut this is about. Both had an open prompt, so
every cheap closure was an absent surface rather than a request that left it
out. Whether a session told "security only" writes the whole list has no
measure. One thing the wording decides that the entry did not. The state is
**not requested** rather than "out of scope", because two phrases next to it
already mean other things.
