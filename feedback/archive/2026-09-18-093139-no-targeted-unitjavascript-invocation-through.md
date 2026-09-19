---
date: 2026-09-18T09:31:39+00:00
category: tool-gap
status: closed
closed: 2026-09-19
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_script_lookup
directory: /home/benji/projects/typo3-cms
---

# no targeted unitJavascript invocation through runTests.sh, so every iteration ran the full suite

## Observation

Task: iterate on one new JS unit test file for the tree component.
typo3_test_run_guide said that unitJavascript passes nothing through, and that a targeted run means calling the runner in Build/ directly. The core's AGENTS.md forbids calling npm directly. So I ran the full suite five times: npm ci plus 26 test files per run, about two minutes each, to watch one file go red, then hang, then green. The guide named the gap correctly. It did not name a way through the project's own runner, for example -s npm -- run test -- --files or a wtr --files invocation inside the container. I did not try one, because the guide said nothing passes through and I did not want to guess at flags.

## Query

typo3_test_run_guide paths=["Build/Sources/TypeScript/backend/tree/tree.ts","Build/Sources/TypeScript/backend/tests/tree/tree-test.ts","typo3/sysext/backend/Resources/Public/JavaScript/tree/tree.js"]

## Suggestion

Measure and document one targeted form inside the container, for example CI=true ./Build/Scripts/runTests.sh -s npm -- run test -- --files Sources/TypeScript/backend/tests/tree/tree-test.ts, and say whether the test script forwards arguments to wtr. If none works, say that too, so a session stops looking.
