---
id: D-SKL-072
title: 'A workflow handover names the calls the next order restarts with'
date: 2026-08-24
status: open
coveredBy:
  - SkillTest::theCrossingOutOfAReviewNamesTheCallsTheOrderRestartsWith
---

# D-SKL-072 — A workflow handover names the calls the next order restarts with

**The paragraph that hands a core review over to the patch workflow names the
calls the order restarts with, rather than naming the skill alone.**

The crossing is the one moment the order has to run a second time. The session
arrives at it with a base in hand it walked once and discharged.

## Evidence

- `feedback/2026-08-24-100534`, `/home/benji/projects/typo3-cms` on 2026-08-24:
  Gerrit change 95375 reviewed under `typo3-core-patch-review`, then reworked
  into a mergeable patch. It reports five prescribed calls not made, and marks
  two of them as instructions that were present, correct and in its context.
- The session crossed. It states that it invoked `typo3-core-patch-development`
  when the user asked for the change, so the crossing paragraph fired as an act.
  That is what `D-SKL-038` and the paragraph's own history exist for. What did
  not follow is the order behind it.
- **The crossing paragraph names a skill and no call.** Read on 2026-08-24.
  *Where the review ends and the rework begins* in
  [`typo3-core-patch-review`](../../skills/typo3-core-patch-review/SKILL.md)
  says to invoke the other skill and work from it. It names
  `typo3-core-patch-development`, the amend and the push. Its own step 1 then
  says to work through `references/base.md`. That is the sentence the review
  already discharged at its start, twenty turns and one exemption earlier.
- **The sweep could not arrive, because the brief was never fetched.**
  `TaskGuide::DEPRECATION_SWEEP` stands in the checklist of every brief whose
  change type produces a change, which is what
  [`D-GUI-013`](../guides/gui-013-the-brief-names-the-sweep-a-change-owes.md)
  wrote on 2026-08-18. The session did not re-run `typo3_task_guide` with
  `changeType="bugfix"`, so the placement was never in front of it.
- **The first report is already answered, and the re-run says so.** This entry
  called `typo3_rule_lookup` on 2026-08-24 in this repository at
  `targetVersion=15.0` with the subjects the session names. `commit message`,
  `release branch`, `target branch` and `gerrit push amend` each answer with
  excerpts and a foot. The foot names every page the excerpts come from and how
  many of its headings are not above. It names what those headings are, and the
  call that reads it. "7 of its 10 headings are not above — Who Reads It,
  Summary Line, Work in Progress, Body, Relationships, Breaking Changes,
  Deprecations". That is the feedback's own suggestion, shipped on 2026-08-11 as
  [`D-ANS-076`](../answers/ans-076-a-search-matching-one-page-answers-with-the-page.md)
  and `Prose::readWhole()`, and it was in both of the session's answers.
- **What the session did instead of `typo3_script_lookup` is a boundary rather
  than a skip.** It read `Build/Scripts/runTests.sh` itself and reports that as
  the better outcome. The file carries that `checkGruntClean` runs `git add *`,
  which would have staged an untracked file of the user's. A note about the
  script does not. That is a strength read as evidence about where a lookup's
  boundary runs —
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md).
- The corpus carries the shape rather than this one report.
  `feedback/2026-08-24-140239` is another directory and another task shape that
  reports a page named to it and never read. `feedback/2026-08-24-133515` is a
  full core patch written with zero calls to this server. Both name the same
  cause the report here does: the moment the task changed is the moment nothing
  re-raised the order.

## Decided

- **Step 2 of the ladder, delivery.** The order exists, it is correct, and it is
  in nothing the caller reads at the moment it has to run a second time. The
  crossing paragraph is that moment and is already written; what it carries is a
  skill name where three call names would fit.
- **What it names is the three the crossing changes the answer to.**
  `typo3_task_guide` with the change type the session is about to write,
  `typo3_hint_lookup` for the paths it is about to edit, and the deprecation
  sweep. Each takes an argument the review has just established and the brief
  behind it does not. That is what makes them a restart rather than a repeat.
- **Trimmed rather than taken on whole.** `D-ANS-076` answers the first report,
  and it comes off the feedback with the re-run above as the record. The fifth
  is a strength and asks for nothing.
- **Queued rather than closed on the spot.** The change is a clause in a
  published skill, which [judging.rst](../../documentation/records/judging.rst)
  reviews rather than improvises, and a skill lands in somebody else's project.
  It needs no lookup about TYPO3.
- **`normal` rather than `low`.** Three sessions from two directories report the
  cause within one day, and the lever is one clause.
- **Refused: the "steps owed but not yet taken" list the feedback asks
  `typo3_task_guide` for.** This server holds nothing between two calls
  ([`D-DIS-011`](../discovery/dis-011-what-was-read-from-the-installation-lives-as-long-as-the-call.md)).
  So it cannot see what the caller has already asked, and a list composed from
  one call's arguments would name steps the session took.
- **Rejected: a restatement of the order in the crossing paragraph.** The order
  is five steps and the installer copies the base into every skill. A second
  copy in one of them is the one that goes stale first. `R-GUI-006` is why the
  base is a reference rather than a body.

## Assumed

- That a paragraph read at the crossing carries what it names. The session
  reports that it invoked the skill out of this paragraph, so it read it at that
  moment. What nothing measures is whether the session then makes a call named
  in it.
- That three named calls stay three calls rather than become a list. A session
  skips a checklist as one, which is
  [`D-SKL-010`](skl-010-the-assessment-before-a-core-patch-reads-the-issue.md)'s
  own **Wrong if**, and the count is the whole of what keeps the two apart.
- That the crossing is where the four other reports had their decision. Two of
  them — the hint lookup on the PHP paths and the deprecation sweep — are steps
  the restart makes, and two are not.

## Wrong if

- A session crosses with the three calls named and makes none of them. The
  crossing was then not the lever, and what remains is the gate
  [`D-SKL-049`](skl-049-the-gate-at-the-end-of-a-workflow-waits-for-its-corrections.md)
  waits on.
- A session reports the clause as noise because the review already made those
  calls. The crossing rests on a changed argument rather than on a new call. A
  case where the argument does not change is one the clause reaches wrongly.
- The clause lands and a session skips `typo3_documentation_lookup` again on a
  diff that changes documented behaviour. That report is inside the review's own
  body at its point of use, which a crossing clause does not reach. It would say
  the pattern is wider than the handover.

## Since then

The crossing this clause hangs off bounds what it reaches. The first sighting of
that bound is a review asked to change the patch that never invoked the
successor. So the session read the paragraph with the three calls as review
prose rather than at a crossing. The session names the deprecation sweep as its
concrete cost, in this clause's own terms, and did not run it. That is not the
first **Wrong if**, which asks for a session that crosses. What it says is that
nothing can reach the **Wrong if** until the trigger holds, and the trigger is
`D-SKL-077`'s.
