---
id: D-SKL-076
title: A description names both jobs a skill's body owns
date: 2026-08-25
status: open
coveredBy:
  - SkillTest::aBacklogSearchMatchesTheSkillThatOwnsTheCandidates
  - SkillTest::aTaskEndingInAPatchIsNotSentAwayByTheTriageDescription
---

# D-SKL-076 — A description names both jobs a skill's body owns

**Where a skill's body owns two jobs, its description names both, and
`typo3-core-issue-triage`'s names only the second.**

Three trims took the backlog job out of that description in stages, and each
read it as a summary of the body's sections. The last of them served a total
budget this repository has since given up.

## Evidence

- **The session.**
  [`feedback/2026-08-24-163220`](../../feedback/archive/2026-08-24-163220-both-skills-matching-this-task-stayed-shut-for.md),
  `/home/benji/projects/typo3-cms` on `claude-opus-5[1m]`, opening "bitte suche
  forge issues im asset renderer bereich". Six `typo3_forge_lookup` searches and
  four candidate issues followed, and the session opened no skill at any point.
  That is the job the skill's own first section describes, and it arrived as the
  first sentence of the brief.
- **The body owns it and calls it a job of its own.** "Find the candidates"
  opens the skill and hands the backlog over as "the first deliverable". It
  states that "Triaging a backlog and triaging an issue are two different jobs".
  The five readings a session picks a candidate on are `D-SKL-031`, from
  2026-08-09.
- **The description names none of it.** 192 characters of the 360
  `SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn` allows, measured on
  2026-08-25. Its subject is one issue somebody already holds: whether it still
  happens, somebody fixed it, or it was never a defect.
- **Three commits took it out, and none of them decided to.** `a1b09af9` cut
  "find the candidates in the backlog" as a step clause and left the request
  shapes in place (`D-SKL-024`). `4b186b3d`, the budget trim of the same day,
  cut those shapes. They were "for going through old or untouched issues", "for
  deciding whether a report is worth taking on". It wrote a shorter clause back
  (`D-SKL-026`). `4c1fe8fc` cut that clause again on 2026-08-19, for 162
  characters shared with `typo3-core-patch-development`.
- **What the last cut bought no longer exists.** It freed listing room under a
  total of 3600, and `D-SKL-064` gave that ratchet up. No fixed sum absorbs the
  next skill, so the test caps each description on its own and holds no sum.

## Decided

- The description names both jobs. What it costs is inside the cap this
  description sits 168 characters below.
- The work is queued rather than made in the judging run. A description is the
  skill's contract in somebody else's project, where no release of this server
  corrects it.
- A test over the pair holds it, in the shape
  `SkillTest::aWorktreeTaskMatchesTheSkillThatOwnsTheCheckout` and
  `SkillTest::aDefectInsideTheDeclaredRangeMatchesTheRemovalSkill` already have.
  That is the words a user types, and the section that has to answer once they
  did.
- The rule goes into
  [documentation/contributing/writing-a-skill.rst](../../documentation/contributing/writing-a-skill.rst)
  beside `D-SKL-024`'s, and not into a requirement. Which jobs a body owns is no
  more readable off a file than which clause is a summary. So a requirement
  would be `not guarded` and would repeat the page.
- Against the same session's other suggestion, a trigger on
  `typo3-core-patch-development` for a session about to change a file in the
  core checkout. That is a request shape added to a description that already
  names the activity, which `D-SKL-033` declined on evidence.

## Assumed

- That a description naming the backlog job would have been chosen. Nothing here
  can see the choice, and the same session passed over
  `typo3-core-patch-development`, whose description does name what it went on to
  do. `D-SKL-033` records that limit and this entry does not escape it.
- That the rest of the published skills carry one job each. This entry read the
  five others `D-SKL-024` trimmed for it on 2026-08-25, and each runs one task
  to one deliverable. It did not read the eight that never carried a cut clause.

## Wrong if

- The description names both jobs and a backlog brief opens nothing. Then the
  wording was not the obstacle and this is `D-SKL-033`'s count again.
- A session opens the skill on a backlog brief and reports the sections after
  the first as somebody else's work. Then the two jobs are two skills, and one
  description cannot carry both.
- Reading the eight untouched descriptions against their bodies turns up no
  second case. Then this is a property of one skill rather than a rule worth a
  page.

## Since then

A read of the eight on 2026-08-25 did not fire the third **Wrong if**. The first
fired two days later, and the session names a clause no read here had weighed,
the last sentence of the description. It is a handover, so the description names
where it goes rather than drops it.
