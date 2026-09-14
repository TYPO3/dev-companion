---
id: D-ANS-082
title: The project answer states how its three PHP numbers relate
date: 2026-08-18
status: open
coveredBy:
  - ProjectTest::aFloorTheEnvironmentRunsIsSaidToBeRun
  - ProjectTest::theThreePhpNumbersAreRelated
---

# D-ANS-082 — The project answer states how its three PHP numbers relate

**The project answer states how its three PHP numbers stand to each other. That
is whether the declared floor clears what the core requires, and whether any
configured environment runs it.**

The three numbers have been in one answer since `D-KNW-055`, each described
against the other two, and the comparison between them has been the caller's. A
session that had all three declared a floor no environment it configured ever
executed, which is a claim nothing tests and every check passes.

## Evidence

- **The three numbers are there and the relation is not.**
  `typo3_project_describe` re-run on 2026-08-18 through
  `bin/typo3-dev-companion` against `.environments/e-site-14.3`. The first line
  reads "PHP unconstrained declared and 8.4 in DDEV, and the installed core
  requires ^8.2 — the lowest a package here may declare". `structuredContent`
  carries `phpConstraint: null`, `corePhpConstraint: "^8.2"` and
  `environment.php: "8.4"`. Each field's description names the other two to say
  which number it is *not*; none of them says how the values stand to each
  other.
- **The session that reported it read that answer and narrowed anyway.**
  `feedback/2026-08-17-211157` declared `^8.3` in a project whose core requires
  `^8.2` and whose container runs 8.4. That is above the floor it could have
  declared and below the interpreter every command ran on. So no line of that
  package ever ran on the version it claims to support. It says it chose the
  number out of habit and that nothing prompted a reconciliation.
- **That is the third Wrong if of `D-KNW-055`, in a variant.** The entry named
  the failure as the container's PHP declared as the supported minimum; what
  happened is a third number that belongs to neither source. Both are the same
  event: the number landed, and what the manifest declares did not change.
- **The comparison is machinery this repository does not have.**
  `Versions::admits()` reasons in TYPO3 majors,
  `'^', '~' => $major === $stated`. So `^8.3` against `^8.2` is a question it
  answers "no" for the wrong reason. Reading a PHP constraint at minor
  granularity is new code, which is what puts this on the queue rather than in
  this commit.

## Decided

- **Taken on, queued.** It touches `src/`, the derived text and the tool's
  declared `outputSchema`, which is the line `documentation/records/judging.rst`
  draws around a close on the spot.
- **What the statement may claim.** What the manifest declares, against what the
  installed core requires, against what the configured environment runs. And,
  where the manifest declares a floor no configured environment executes, that
  this is so. Nothing here ran anything, so it is not evidence the package works
  on its floor, and it is not a check that ran.
- **On the tool rather than in a skill.** `D-KNW-055`'s Wrong if reads the
  failure as a workflow that does not say where to look. A workflow step can say
  "reconcile the three", but the reconciliation is the work. The one place that
  holds all three values is the answer that already prints them. A caller
  charged one context per call (`D-FBK-020`) should not pay a round trip to
  compare numbers it just got.
- **Priority `normal`, set here.** One session reported it, so not `high`. The
  cost already has a count, a supported range narrowed by two minors with every
  check green, so not the `low` a card arrives at.

## Assumed

- **That a read of a PHP constraint at minor granularity covers the spellings a
  manifest writes.** `D-VER-004` made that assumption one level up and one
  spelling in the wild broke it, `>= 8.1 < 8.5` with spaces. The same corpus is
  what the minor-granular read owes its check against.
- **That the relation is worth a sentence every project pays for.** The answer
  states it even where the three agree, for the reason
  `ProjectDescribe::floor()` already states the core's number where it repeats
  the project's own. Nobody can tell a line the answer drops when nothing is
  wrong from one it never computed.

## Wrong if

- The line lands and a session declares a floor it never runs anyway, now with a
  sentence in the answer that says so. Then the answer bought a claim rather
  than a reconciliation, and the gap was a step in the workflow that writes the
  manifest.
- The comparison answers wrongly on a spelling in the wild, a bare `8.2.*`, an
  operator with a space, a hyphen range. It states the wrong relation with the
  answer's authority. That is worse than the absent line, because a caller
  cannot tell it from a correct one.
- Every project the corpus reaches turns out to declare a floor its environment
  runs, and the derived line is always the consistent case. Then it says nothing
  and costs a sentence on the first call of every task.

## Since then

Built on 2026-08-18, and the first **Assumed** holds, measured rather than
carried over. 556 constraints in 36 distinct spellings, each asked of this side
and of composer/semver, and all 36 agree. The spelling that broke the entry one
level up is among them, which is why one function collapses the spaces rather
than two. Two shapes answer "no floor" instead of a number, and both avoid the
second **Wrong if** rather than meet it.
