---
date: 2026-10-05T09:42:10+00:00
category: wrong-answer
status: open
model: claude-opus-5-5
tool: typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# e2e suite entry says no spec path passes through; main forwards one since 4d2b8384900

## Observation

Task: review Gerrit change 96353 (styleguide demo data speed-up) on main, then run one small e2e spec.
typo3_test_run_guide described the e2e suite like this: "Nothing passes through to Playwright — no test path, no filter, whatever follows `--` — so the run is every spec of the project."
That statement is false on main at commit 5590a94b8e8.
Commit 4d2b8384900 "[TASK] Allow filtering e2e Playwright tests to a single file" is an ancestor.
Build/Scripts/runTests.sh forwards the remaining positional arguments as PLAYWRIGHT_TEST_ARGS, before --project.
The checked-in AGENTS.md shows the same form.
I ran: CI=true ./Build/Scripts/runTests.sh -s e2e Build/tests/playwright/e2e/styleguide/notification.spec.ts
Playwright ran 2 tests (login.setup plus the one spec), not the full suite.
The answer carried no version range for this statement.
I did not trust it because AGENTS.md said otherwise. A session without AGENTS.md would skip the e2e run as too expensive.

## Query

typo3_test_run_guide paths=[typo3/sysext/styleguide/Classes/TcaDataGenerator/AbstractGenerator.php, ...FieldGenerator/TypePassword.php, ...Generator.php, ...GeneratorFrontend.php, two new test files]; also the e2e entry in typo3_task_guide changeType=audit for the same paths.

## Suggestion

State the range: from 4d2b8384900 on main, a spec path and --grep pass through as positional arguments before --. Give the targeted form: CI=true ./Build/Scripts/runTests.sh -s e2e <spec path>. Keep the old statement only for branches without that commit (14.3 and 13.4, if true there).
