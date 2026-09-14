---
id: R-SCO-002
title: 'A scope outside the core changes the payload, entry by entry'
status: held
heldBy:
  - ScopeTest::aBriefInAnExtensionRepositoryHandsBackNoCoreSuite
  - ScopeTest::aBriefOutsideTheCoreKeepsNothingThatOnlyTheCoreHas
  - ScopeTest::aHintKeepsItsAdviceOutsideTheCoreAndLosesItsCoreChecks
  - ScopeTest::aPathNothingPlacesIsStillAnsweredFromTheCore
  - ScopeTest::aRuleSectionOutsideTheCoreSaysWhereAConventionIsBound
  - ScopeTest::aTestFileNothingPlacesIsInTheRepositoryTheCallIsIn
  - ScopeTest::anExtensionChangelogTaskIsRoutedAwayFromTheCoresOwnProcedure
  - ScopeTest::anExtensionDeprecationIsCommittedUnderItsOwnRepositorysConvention
  - ScopeTest::anExtensionTestBriefRoutesTheHarnessTheExtensionHas
  - ScopeTest::noRunTestsCommandIsHandedToARepositoryThatHasNoRunTests
  - ScopeTest::twoPathsOfDifferentAudienceInOneCallStayApart
---

# R-SCO-002 — A scope outside the core changes the payload, entry by entry

**A scope of `project` or `extension` changes the payload.**

The answer drops core-only commands, checklist items and checkout discovery.
Conventions that transfer stay, with a mark that says so. The line runs per
entry, not per section, because a checklist mixes both. In a call whose paths
have different scopes it runs per path as well. The suites and the checks come
back for the paths that can run them, and the answer names the ones that cannot
beside them.

## From

An answer that reported `outsideCore: true` and then returned four `runTests.sh`
suites for a repository that has no `Build/Scripts/` (2026-07-29).
