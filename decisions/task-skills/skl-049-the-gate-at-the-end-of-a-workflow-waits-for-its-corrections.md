---
id: D-SKL-049
title: 'The gate at the end of a workflow waits for its corrections'
date: 2026-08-18
status: open
---

# D-SKL-049 — The gate at the end of a workflow waits for its corrections

**A closing gate that names what a build workflow still owes waits on the three
corrections its own sightings produced. Whether to write it is the maintainer's
answer.**

One session reported three prescriptions partly executed and named the pattern
as the finding. Each of the three got its correction at the point of use the
next day, and no session has met any of the corrections yet.

## Evidence

- `feedback/2026-08-17-212218`, a v14 demo site built as a sitepackage plus a
  distribution extension on 14.3.6. It reports the five bullets of *Verify at
  the right layers* in `typo3-content-element-development` with one executed. It
  names two earlier instances of the same shape in the same session. It states
  the repetition rather than any one bullet as what it found.
- It is a summary, so this entry maps it onto its siblings before anything else
  ([`D-FBK-021`](../feedback/fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md)).
  Its first instance is `feedback/archive/2026-08-17-211118`, judged in
  [`D-SKL-044`](skl-044-a-step-that-names-two-hint-ids-says-what-each-answers.md)
  and corrected by `7dab8ef8`. Its second is
  `feedback/archive/2026-08-17-211306`, judged in
  [`D-KNW-087`](../knowledge/knw-087-a-listed-neighbour-says-what-it-prevents.md)
  and corrected by `9111d6a8` and `1f189b5f`. Its third is the browser bullet of
  the section it is about, judged in
  [`D-SKL-045`](skl-045-a-build-workflow-names-the-guide-at-the-step-that-needs-it.md),
  held by `R-SKL-024` and corrected by `4ec22687`.
- What the three corrections have in common is the mechanism the summary names.
  A clause per hint id, a neighbour that says what it prevents, a guide named by
  `documentId` at the step that needs it. Each one changes what a caller reads
  at the moment it decides, which is where the summary says the decision
  happens.
- The query cannot be re-run here: it is a six-element sitepackage build, and
  what this repository runs and records is a repository review
  ([AGENTS.md](../../AGENTS.md)). What was re-read on 2026-08-18 is the section
  itself. Its other four bullets are present, correct and undisputed by the
  feedback, and the browser bullet now carries both guide ids.
- The corpus is one session. `bin/cli feedback:list` on 2026-08-18 reports 13
  open feedback, all of them from `/home/benji/projects/site-demo` and all from
  one build.
- The same debrief reports the other side. `feedback/2026-08-17-212600` measures
  the two skills at 2,945 and 1,290 tokens and defends the size discipline as
  what made them read in full. It names these same two failures as legible
  *because* of it. Its own suggestion names "the order, the obligations, the
  terminal check". So the session that asks for a gate is also the one that says
  what a gate may not cost.
- The repository already carries both halves of the question.
  [writing-a-skill.rst](../../documentation/contributing/writing-a-skill.rst)
  says judgment keeps a checklist and construction does not.
  [`D-SKL-010`](skl-010-the-assessment-before-a-core-patch-reads-the-issue.md)
  carries *the rungs read as a checklist and are skipped as one* as its own
  **Wrong if**. `R-SKL-020` is a terminal stop already in a workflow, for the
  step that publishes. The skill in question also ships
  `references/checklist.md`, read before writing code, which
  `feedback/2026-08-17-212600` calls the most useful document of the build.

## Decided

- **Step 5 of the ladder for the half nothing else reports.** The prescription
  was present, correct, delivered into an active skill and read. What the
  feedback names as the gap is the form a workflow has. That is the design as
  the price rather than a gap, a delivery, a routing entry or a wording.
- **Proposed, so it waits for an answer.** The card carries the question and
  stays with the feedback, which keeps `D-FBK-017`'s invariant.
- **The recommendation is to wait, and this entry names the trigger.**
  `D-SKL-044`'s first **Wrong if** is a session that fetches one id and stops
  with the corrected step in front of it. `D-SKL-045`'s is a session that ships
  a view unverified with the guide named at the step. Either one fired is the
  gate's case made on a corrected file.
- **Rejected: writing the gate now.** It would be the fourth intervention
  against three sightings, three of which landed on 2026-08-18 and none of which
  a session has met. Afterwards nothing could say which of the four carried a
  session past the step. The credit for the cheap corrections would go to the
  expensive one or the other way round.
- **Rejected: an archive on the three corrections.** The pattern claim is the
  half only this file reports. An archive would tell the session that its
  finding has its answer when what landed answers its three examples.
- **What the work would open with is a placement, not a text.** The skill
  already gates before the code. So the question is whether the closing step
  re-reads that reference or states its own list. That is the todo's first
  question rather than this entry's answer.

## Assumed

- That the three corrections address the mechanism the summary names. Each one
  landed where the caller decides, which is where the feedback puts the failure,
  and no one of them serves the pattern.
- That one session reporting a shape three times is one pattern rather than
  three coincidences. Nothing separates them, and nothing else in the corpus
  reports it.
- That a session that meets the corrected files will file again where they do
  not take. This session filed thirteen times, which is evidence about it and
  not about the next one.

## Wrong if

- A session with all three corrections in front of it reports a prescription
  partly executed a fourth time. The corrections were then not the lever, the
  gate is what remains, and the wait cost that session its step.
- A second session, from another task shape, reports the same pattern. The
  corpus is then more than one build and weight rather than an ask answers the
  question.
- The answer comes back that the maintainer wants the gate. It lands as one
  closing sentence that points at the reference the skill already ships. What
  waited was then cheaper than the weight this entry gives it.
- A gate lands later and a session reports that it skipped it as a list. That is
  `D-SKL-010`'s **Wrong if** by way of this entry, and it would make the
  deferral right for a reason this entry does not give.

## Since then

The second **Wrong if** has fired four times, each from a task shape this entry
did not have in view. The fourth is the first where the skipped prescription is
`skills/base.md`'s deprecation sweep rather than a build step. Each got its
correction at its own point of use, as the three corrections before them did,
and `D-SKL-085` is the latest. So the corpus is more than one build. The
question the entry defers is still the closing gate itself, which the maintainer
answered *wait* to on 2026-08-19 and again on 2026-08-27.
