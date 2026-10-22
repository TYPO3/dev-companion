---
date: 2026-10-22T10:57:11+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3_task_guide, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# Briefs for a fluid ViewHelper fix carry e2e suites, a content-caching item and no 13.4 question

## Observation

Task: bugfix in SelectViewHelper.php, its functional test and an Important RST.
typo3_test_run_guide returned 24 suites, e2e, e2e-prepare and e2e-browser among them. The useful ones were functional, phpstan, checkIntegrityPhp, cglGit and checkRst.
typo3_task_guide put "Content changes, so what is delivered has to be the version that is current after the change" first in the checklist. It does not apply to a form field name.

## Query

typo3_test_run_guide and typo3_task_guide paths=[typo3/sysext/fluid/Classes/ViewHelpers/Form/SelectViewHelper.php, typo3/sysext/fluid/Tests/Functional/ViewHelpers/Form/SelectViewHelperTest.php, typo3/sysext/core/Documentation/Changelog/14.3.x/Important-79571-SelectFieldsBoundToACollectionSubmitAList.rst]

## Suggestion

Rank the suites for the paths and mark the narrow set first. Drop the content-delivery item where no path is about caching or rendering of records.
