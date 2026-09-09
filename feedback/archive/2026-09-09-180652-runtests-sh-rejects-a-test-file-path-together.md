---
date: 2026-09-09T18:06:52+00:00
category: missing-knowledge
status: closed
closed: 2026-09-09
model: claude-opus-5[1m]
tool: typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# runTests.sh rejects a test file path together with -- --filter, and nothing says so

## Observation

Task: review Gerrit change 95482 against a local worktree and substantiate the open points with tests. Needed: run two named test methods out of one functional test file, against two different code states.

I ran, from the TYPO3 core checkout:

  CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite typo3/sysext/install/Tests/Functional/Service/SetupServiceTest.php -- --filter 'name1|name2'

and got:

  Test file "--filter" not found
  ... FAILURE

I assumed the quoting of the filter expression was the problem, retried with a directory path instead of the file path and a single-word filter — same error, same failure. Only the third form worked:

  CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- --filter 'InitialisationDataOf'

So a path argument and a `-- <phpunit args>` tail are mutually exclusive: the dispatcher treats the first token after the suite as the test path and then reads `--filter` as a second path. Cost: two wasted container runs, each a full functional bootstrap, plus the reasoning between them.

The repository's AGENTS.md documents both forms, one line apart:

  CI=true ./Build/Scripts/runTests.sh -s unit typo3/sysext/core/Tests/Unit/RegistryTest.php
  CI=true ./Build/Scripts/runTests.sh -s unit -- --filter someTestMethodName

and never says they cannot be combined. That is the "right and one step short" case: the two facts were there, the interaction between them was not, and the interaction is what a caller hits the moment they want one method out of one file — which is the single most common thing to want while iterating on a patch.

Consequence worth stating: because the path has to be dropped, filtering a single functional test method runs the discovery of the whole suite rather than one file.

## Query

CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite typo3/sysext/install/Tests/Functional/Service/SetupServiceTest.php -- --filter 'setupExtensionsDoesNotImportInitialisationDataOfTheProjectRootPackage|setupExtensionsImportsInitialisationDataOfActivePackages'

## Suggestion

typo3_test_run_guide should carry the invocation matrix rather than a list of examples: which argument slots exist for runTests.sh (-s suite, -d dbms, -p php, -c chunk), that exactly one of "a path" or "-- <phpunit args>" may follow, and what each combination does. Include the literal error string `Test file "--filter" not found`, so a caller who has already hit it and searches for it lands on the answer.

Worth stating explicitly there as well: to run one method, either pass the path and no filter, or pass the filter and no path; there is no form that narrows to both.
