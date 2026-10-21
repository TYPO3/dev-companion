---
date: 2026-10-21T15:31:39+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# A functional run on a sysext without Tests/Functional reports FAILURE that reads like a regression

## Observation

Task: run functional suites for the extensions that render f:form.select. I ran runTests.sh -s functional on typo3/sysext/belog/Tests/Functional. belog has only Tests/Unit. PHPUnit printed "Test file not found" and the summary said FAILURE. My filtered output showed only FAILURE, and it looked like a test regression. I had to run it again unfiltered to see the cause. I did not call typo3_test_run_guide with these paths before the run.

## Query

CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite typo3/sysext/belog/Tests/Functional

## Suggestion

When typo3_test_run_guide gets a path, say which test layers that system extension has, for example "belog has no functional tests". Note that a path that does not exist gives FAILURE with "Test file not found", which is not a test failure.
