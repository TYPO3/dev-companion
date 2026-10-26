---
date: 2026-10-22T10:57:11+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5
tool: typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# Briefs for a fluid ViewHelper fix carry e2e suites, a content-caching item and no 13.4 question

## Observation

Task: bugfix in SelectViewHelper.php, its functional test and an Important RST.
typo3_test_run_guide returned 24 suites, e2e, e2e-prepare and e2e-browser among them. The useful ones were functional, phpstan, checkIntegrityPhp, cglGit and checkRst.

## Query

typo3_test_run_guide paths=[typo3/sysext/fluid/Classes/ViewHelpers/Form/SelectViewHelper.php, typo3/sysext/fluid/Tests/Functional/ViewHelpers/Form/SelectViewHelperTest.php, typo3/sysext/core/Documentation/Changelog/14.3.x/Important-79571-SelectFieldsBoundToACollectionSubmitAList.rst]

## Suggestion

Rank the suites for the paths and mark the narrow set first.
