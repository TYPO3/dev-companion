---
id: D-SKL-075
title: A patch narrows the work, not the list of points it closes on
date: 2026-08-25
status: open
coveredBy:
  - SkillTest::aPatchCoversEveryPointItsIssueLists
---

# D-SKL-075 — A patch narrows the work, not the list of points it closes on

**A core patch covers every point its issue lists, or the author splits the
points it leaves into issues of their own before the code.**

`typo3-core-patch-development` says how to keep a patch to one change and says
nothing about the issue's own list of points. So a session that read all three
points shipped two of them and footnoted the third.

## Evidence

Measured on 2026-08-25 in this branch, against `feedback/2026-08-24-162543`.

- The judged feedback is a session on Forge #106584. Its subject names two
  ViewHelpers and its comments name three. The session implemented `href` and
  `src` and reported `f:image` `alt` as a follow-up "needing its own issue". The
  user corrected it: cover every point, or split the issue up front. The
  correction came from the user rather than from anything this server said.
- The feedback's own query re-run today —
  `typo3_forge_lookup issue=106584 notes=people` — answers `answered`, status
  `Under Review`, six notes of eight with two bot notes dropped. Note 3 (Daniel
  Kruse, 2025-04-17) is the list: `f:asset.css -> href`,
  `f:asset.script -> src`, `f:image -> alt`. Note 5 (Simon Praetorius,
  2025-06-13) confirms all three. The third point reaches the caller in one
  call, and did.
- `bin/cli hints:probe "cover every point a Forge issue lists or split it into separate issues"`
  reaches nothing; 105 hints come back as the index. Nothing in `knowledge/` or
  `skills/` says what a patch owes the points its issue lists.
- The two places a split part needs its own number are both here.
  `knowledge/documents/core/contribution/commit-messages.md` requires
  `Resolves:` and one line per resolved issue;
  `knowledge/documents/core/contribution/changelog.md` names the entry
  `<Type>-<forgeIssueNumber>-<UpperCamelCaseDescription>.rst`. Neither says what
  becomes of an issue a patch covered in part.
- The skill's nearest sentence is "Keep the patch one change. What else you
  noticed is another issue and another patch." It is about work that arrives
  from outside the issue. Read at the moment of the decision, it argues for the
  narrow patch rather than against it.
- `feedback/2026-08-24-225243` names that same sentence from the other side. It
  "fired — but only for the first split", where the request widened after the
  patch was under way. One paragraph, two sessions, two directions.
- [`D-SKL-002`](skl-002-a-focused-audit-narrows-what-is-assessed-not-the-list-it-closes-on.md)
  settled this principle for the sibling skill. A focused audit narrows what it
  assesses, never the surface list its report closes on. The failure here is the
  same one in the other workflow. The session had the right to cut the work and
  cut the list instead.
- [`D-SKL-010`](skl-010-the-assessment-before-a-core-patch-reads-the-issue.md)
  put the Forge call into the skill's step 3 and says what a session reads out
  of it. That is the status and target version, the relations, and the notes
  "where a maintainer said why". The notes carry a fourth thing, and no entry
  names it.

## Decided

- Step 4 of the ladder, wording. The skill fired, the session followed its step
  3 and the answer carried the third point. What did not take is what the
  session then did with it.
- The work goes to the queue rather than into this run. It rewrites a published
  `SKILL.md`, which `judging.rst` puts on the far side of what a judgement may
  change on the spot.
- The priority is `normal` and two sessions set it: one reporting the narrowing,
  one reporting the same paragraph as insufficient in the opposite direction.
- **The venue for the feedback's second half is the skill, not the tool.** It
  asks `typo3_forge_lookup`'s description to flag that an issue's scope is
  frequently set in its comments. The re-run shows the comments reach the caller
  and the session read all three points out of them. So nothing measured has the
  description fall short, and the place where a session reads the enumeration is
  already a step of the skill.
- The card of `feedback/2026-08-24-225243` stays as it is rather than folds in.
  It asks for a different sentence and carries a second ask about a backend Lit
  step's error branch, and no judgement has read it.
- `coveredBy: []` because nothing exists yet. What could hold this is a
  `SkillTest` sentence over the skill body, and it belongs to the commit that
  writes the wording.

## Assumed

- That the points of an issue are enumerable during the assessment. The one case
  says so, because the list is a comment naming three ViewHelpers. An issue
  whose scope is prose makes the enumeration a judgement the skill would have to
  describe.
- That the split decision has to come before the code because each part needs a
  number of its own. The `Resolves:` trailer and the changelog file name both
  take it. That is the feedback's reason, and `knowledge/` carries both halves
  of it.

## Wrong if

- A reviewer asks for a squash of two such patches. Then every point in one
  change is what the project wanted and the rule points the wrong way.
- Two sessions enumerate one issue's points differently off the same comments.
  Then the split rests on the read rather than on the issue. What the skill owes
  is how to make the list rather than what to do with it.
- A session narrows a patch to a convenient subset with the wording in place.
  Then it is not the wording, and the gap is a gate. That is what
  `feedback/2026-08-17-212218` reports for another skill, and the answer is a
  shared one rather than this one.

## Since then

The work settled the placement: both, one point each. The enumeration is a third
act of the step where a session reads the list, because this entry refuses a
fourth rung. The other direction is one paragraph where a session decides to
narrow. That is the shape `D-SKL-002` settled once already: the instruction at
the point of the decision outranks a permission stated earlier. The two reasons
go one to a place rather than twice. `R-SKL-027` is a requirement rather than
the change. The demand holds while somebody rewrites the file that states it.
What stays unmeasured is the first **Wrong if**.
