---
date: 2026-10-22T10:57:11+00:00
category: idea
status: closed
closed: 2026-10-22
model: claude-opus-5-5
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# The trusted-properties hint stopped a mapping test that could not fail

## Observation

Task: finish core change 96975. A reviewer asked for a test that "x[]=5" maps to a populated ObjectStorage.
typo3_task_guide returned the hint extbase-arguments. It says a name with [] registers a list in the trusted properties. It says a test with its own PropertyMappingConfiguration passes anyway, because it never reads the trusted properties.
That stopped me from adding a test like ObjectStorageConverterTest. I searched for a test that maps through real trusted properties. I found BlogPostEditingControllerTest, which maps "categories":[1,1,1,1] and notes that the scalar form fails. I answered the reviewer with that test. Keep this hint in briefs for fluid form ViewHelper paths.

## Query

typo3_task_guide changeType=bugfix paths=[typo3/sysext/fluid/Classes/ViewHelpers/Form/SelectViewHelper.php, typo3/sysext/fluid/Tests/Functional/ViewHelpers/Form/SelectViewHelperTest.php, typo3/sysext/core/Documentation/Changelog/14.3.x/Important-79571-SelectFieldsBoundToACollectionSubmitAList.rst]
