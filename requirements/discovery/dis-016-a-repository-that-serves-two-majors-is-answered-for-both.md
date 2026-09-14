---
id: R-DIS-016
title: 'A repository that serves two majors is answered for both'
status: held
heldBy:
  - VersionsTest::aConstraintIsReadByAskingItAboutEachCoveredMajor
  - VersionsTest::aStatedMajorSaysWhichOtherOneItLeftOut
  - VersionsTest::anExtensionThatServesTwoMajorsIsAnsweredForBoth
  - VersionsTest::theAnswerSaysWhichMajorsItWasComposedFor
---

# R-DIS-016 — A repository that serves two majors is answered for both

**A repository that declares `typo3/cms-core` for more than one covered major
gets an answer for all of them at once.**

`typo3_task_guide` and `typo3_hint_lookup` keep a statement that holds on any of
them. They name the majors and the declaration in the answer, and report them as
`targetVersions`.

A version the caller states still narrows to that one. Where that is below what
the repository declares, the answer says so. It names the major it is for and
the ones declared beside it, and says that it lacks the statements that hold
only there. The two input descriptions say the same, so a caller states a
version to narrow rather than to restate what sits installed. A constraint the
server cannot read falls back to the installed version. The catalogs keep their
withhold per single version, because markup that does not exist fails in a
browser either way.

## From

`REVIEW-02`. The extension declares `^13.4 || ^14.3`, and the installed 14
filtered the lookup. So
`ext_emconf.php is what makes a directory an extension outside Composer`, bound
`until: 13`, never reached the session. It reported the file it is about as
accumulated drift (2026-07-31). The run of that afternoon then never reached the
widened answer. The session read `14.3.0` out of `typo3_project_describe` and
stated it. The input description invited that with its promise of the installed
version as the default, and the narrowed answer said nothing about itself
(2026-07-31).
