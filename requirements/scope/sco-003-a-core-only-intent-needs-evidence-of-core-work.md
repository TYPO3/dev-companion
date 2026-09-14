---
id: R-SCO-003
title: 'A core-only intent needs evidence of core work'
status: held
restsOn: [D-SCO-002]
heldBy:
  - ScopeTest::aCorePathStillMakesTheSameWordAPatchSubmission
  - ScopeTest::aCoreTaskNamingNoPathKeepsTheSubmissionRules
  - ScopeTest::inASitePackageThePatchSubmissionIntentIsNotOfferedAtAll
  - ScopeTest::maintainingAnExtensionIsNotSubmittingAPatchToTheCore
  - ScopeTest::theBriefNamesWhatWouldTurnTheConditionIntoFact
---

# R-SCO-003 — A core-only intent needs evidence of core work

**The brief does not select a core-only intent such as patch submission for work
that is not core work.**

They need positive evidence of core work, a `typo3/sysext/` path or the
contribution workflow named outright. The words that match them ("review",
"push", "submit") describe maintenance anywhere. Outside the core the brief
drops them. Where nothing says either way it offers them under their condition,
never as a statement.

## From

Third-party extension maintenance recognised as a Gerrit patch submission
(2026-07-29).
