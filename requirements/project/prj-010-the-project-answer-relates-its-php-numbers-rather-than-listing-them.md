---
id: R-PRJ-010
title: 'The project answer relates its PHP numbers rather than listing them'
status: held
restsOn: [D-ANS-082]
heldBy:
  - ProjectTest::aFloorTheCoreRefusesAndAnEnvironmentUnderItAreBothSaid
  - ProjectTest::aFloorTheEnvironmentRunsIsSaidToBeRun
  - ProjectTest::aProjectWithNoReadableFloorIsRelatedToNothing
  - ProjectTest::theThreePhpNumbersAreRelated
  - VersionsTest::aPhpSpellingFromTheCheckoutsAnswersItsLowestVersion
  - VersionsTest::theFloorIsReadOneLevelBelowTheMajorTheRestOfThisAnswers
---

# R-PRJ-010 — The project answer relates its PHP numbers rather than listing them

**Where the project declares a PHP floor, the answer states where that floor
sits against the installed core's floor and against any configured
environment.**

Three numbers have stood in that answer since `R-PRJ-008` put the environment's
beside the two declared ones. Each field's description names the other two to
say which number it is not. What none of them said is how the values stand to
each other, and that is where the defect lives. A floor above what the core
needs is a range the project narrowed itself. A floor no configured environment
runs is a claim every check passes without a test.

The answer reads it from the constraints as the files spell them, so `R-PRJ-001`
still holds and the answer arrives on a fresh clone. Nothing runs to find it
out, which is also the whole of what it may not claim. It says what the project
promises, never that the promise works.

The answer states it even where the three agree, for the reason it already
states the core's floor where it repeats the project's own. A line dropped when
nothing is wrong looks the same as one nothing computed.

A floor is a floor and not a range. The comparison takes only the lowest version
each constraint admits. So an environment above what a constraint's own upper
bound allows reads like one inside it. A form the reader will not claim to read
costs the whole statement rather than buys a wrong one.

## From

`feedback/2026-08-17-211157` (2026-08-17), a v14 demo site in
`/home/benji/projects/site-demo`. `typo3_project_describe` reported
`phpConstraint: "^8.3"`, `corePhpConstraint: "^8.2"` and
`environment.php: "8.4"` in one answer. The session declared a floor two minors
above what its own core required and ran every command on a third version. It
executed no line on the one it claimed to support. It reports that it had
already read the answer that would have shown it.

## Held by

The read is minor-granular, which `Versions::admits()` is not. That one answers
per TYPO3 major, so `^8.3` and `^8.2` are one answer to it. A session measured
what a manifest spells rather than assumed it. It read every `require.php` below
`.checkouts/{12.4,13.4,14.3,main}` and their vendor trees on 2026-08-18, 556
constraints in 36 distinct forms. Each one is a case in the provider above. Each
expectation is composer/semver's own answer for that form, which the session got
with a request for the lowest major.minor it admits any release of. The one
shape left unread is Composer's hyphen range. It occurs in none of them, and a
read of it as the comparators it splits into answers its ceiling.
