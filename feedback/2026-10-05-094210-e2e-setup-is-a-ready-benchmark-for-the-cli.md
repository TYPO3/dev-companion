---
date: 2026-10-05T09:42:10+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5
tool: typo3_test_run_guide, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# e2e setup is a ready benchmark for the CLI steps it runs, but nothing says so

## Observation

Task: measure the styleguide generator on SQLite in the e2e setup, as the user asked.
Build/Scripts/setupAcceptanceComposer.sh runs typo3 setup, dataset:import and styleguide:generate -c tca and -c frontend on SQLite with WAL.
I ran one spec (styleguide/notification.spec.ts) and put a timestamp on each output line with perl Time::HiRes.
The gap between "TYPO3 Setup is done." and "TCA page tree created!" was 10.7 s on the parent and 4.4 s on the patch.
One run took about one minute. It was the most realistic number of the session.
I found this route myself from the setup script. No answer named it.
The timing guide only offers a functional-test probe. The functional probe also needed configurationToUseInTestInstance to force Argon2i. I did not know which hasher the testing framework uses by default.

## Query

CI=true ./Build/Scripts/runTests.sh -s e2e Build/tests/playwright/e2e/styleguide/notification.spec.ts | perl -MTime::HiRes=time -ne 'printf "%.2f %s", time, $_'

## Suggestion

In core/testing/timing-a-code-path, add the e2e setup as a second harness for CLI commands it runs (setup, dataset:import, styleguide:generate), with the one-spec invocation and the timestamp pipe. State which password hasher a functional test instance uses by default, and how to force the production one.
