---
id: D-SKL-061
title: 'The upgrade description is reachable from a defect'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::aDefectInsideTheDeclaredRangeMatchesTheRemovalSkill
  - SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn
---

# D-SKL-061 — The upgrade description is reachable from a defect

**`typo3-extension-upgrade` names the removed and deprecated surface of a major
as a case of its own. So a defect inside a range the package already declares
reaches it.**

Its description opens on the carry of a package from the versions it supports
today to another set. A reader reads the four shapes after the colon under that
clause. A bug report whose cause is a removal changes no range. So the clause
that carries the activation states a premise the task does not meet.

## Evidence

- **The session.** `/home/benji/projects/blog` on 2026-08-18,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-18-080630`](../../feedback/archive/2026-08-18-080630-typo3-extension-upgrade-describes-this-task.md).
  The request was a German symptom report, these TypoScript conditions seem to
  be broken in v14, they must still work in v13. One file stood selected in the
  editor. No skill activated at any point of the session, investigation through
  commit.
- **The words were there.** The session read the description back and reports
  two of its four clauses as the task exactly.
  `replacing what a major deprecated or removed` is the removal it chased.
  `proving every version it claims` is the v13 half it could not prove. What it
  names as the obstacle is the first clause, under which both read as steps of a
  change to a declared range.
- **The body says the same thing.** `SKILL.md` opens "Cross a package from the
  range it declares to the range it is meant to declare". Its third step
  resolves the range the package may declare. A description rewritten alone
  would leave the file at odds with itself in somebody else's project, which is
  what `R-SKL-010` is about.
- **The shape is real.** `.checkouts/14.3` carries
  `Breaking-107473-TypoScriptConditionFunctionGetTSFERemoved.rst` and
  `Breaking-107831-RemovedTypoScriptFrontendController.rst` under
  `Changelog/14.0/`. The package declares `^13.4.15 || ^14.3` and a removal
  inside that range breaks it. So the request arrives as a defect rather than as
  an upgrade.
- **The other route is shut too.** The two intents that name this skill in
  `knowledge/task-intents.json` match on `deprecat`, `breaking`,
  `remove public`, `drop support`, `@internal` and `public api`. A symptom
  report carries none of those. Both checklists are the core author's, a
  changelog file, an extension scanner matcher, rather than the consumer's. The
  `routing` entry of the same shape in `knowledge/server-scope.json` fires on
  starting work on a major or planning an upgrade, and routes to
  `typo3_changelog_lookup`.
- **Not step 1a.**
  `bin/cli hints:probe "extension broken on a TYPO3 major it already supports, removed global"`
  matched nothing and returned its 96 candidates as the index. The gap is not a
  statement.
- **What the listing has left.** 3572 characters of the 3600 ceiling on
  2026-08-18, measured over `Installer::skills()` the way
  `SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn` measures it, with the
  upgrade description at 267 of them.

## Decided

- The ladder's step 3, on the skill's own trigger. It is the second sighting of
  the mechanism that confirmed `D-SKL-024` on 2026-08-09. A clause that reads as
  one way to do the job **narrows** what the description names. The task that
  does it another way reads as actively not its case. There the clause was a
  list of steps and the excluded shape was a worktree. Here it is the premise
  the shapes stand under.
- Queued rather than rewritten in this run. A description lands in somebody
  else's project, which is why `D-AUD-003` queued the backend preview rewrite
  rather than made it. The body moves in the same commit.
- The rewrite trades words and does not buy a shape. `D-SKL-033` decided against
  listing characters for more request shapes, and this is not that request. The
  shape already stands there and the first clause is what excludes it. 28
  characters is what the ceiling has left. So a rewrite that only adds is the
  other trade and `D-SKL-026`'s arithmetic decides it.
- At `normal`, because two sessions on two skills have now reported the same
  mechanism. The words that would have matched were already in the file.
- Against a read of this as an absent owner. What the session did, find the
  removal, find what replaces it, prove both majors, is this skill's workflow
  with the range decision taken out. So the skill owns the case and what failed
  is the way in.
- The second half of the feedback goes to the card that already carries it.
  [`feedback/2026-08-18-081129`](../../feedback/archive/2026-08-18-081129-nothing-says-how-to-execute-the-other-typo3.md)
  reports the same gap, that nothing says how to execute the other major a
  package claims. It comes from the same session with the procedure it would
  have needed. So this feedback shrinks to the description and that card stays
  the owner.

## Assumed

- That the session read the description and chose against it, rather than the
  listing dropped it or the session never consulted it. Nothing here can see the
  choice, which is the same limit `D-SKL-033` stops at.
- That the first clause is what governed the read. The session says so and its
  report is the only account of it. The sentence also parses the other way, as
  four independent shapes after a colon.

## Wrong if

- A run against the rewritten description leaves the skill shut on a symptom
  report of this shape. Then the wording was not the obstacle for this skill
  either, and `D-SKL-033`'s position covers it as well.
- The skill carries a session that activates on such a report through the range
  steps, and the session reports them as noise. Then the case belongs to an
  owner that does not exist yet, and the trigger was the smaller half of the
  gap.
- Only added characters can widen the premise. Then this is a budget question
  rather than a wording one, and which of the twelve descriptions pays is what
  somebody decides first.

## Since then

The rewrite landed. The description opens on a package kept at work on the
versions it declares **or** carried to another set. So a reader reads the shapes
after the colon under a premise a defect meets, and the body opens on the same
two cases. What the trade had to fit is smaller than this entry says. Measured
the way the test measures, the room was three characters and not 28. The next
rewrite of any of the twelve reads the number off that test rather than off
here.
