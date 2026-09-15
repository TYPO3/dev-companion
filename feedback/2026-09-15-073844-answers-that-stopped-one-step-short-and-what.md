---
date: 2026-09-15T07:38:44+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_task_guide, typo3_hint_lookup, typo3_test_run_guide, typo3_project_describe
directory: /home/benji/projects/typo3-cms
---

# answers that stopped one step short, and what the client did not show

## Observation

Task: review core change 95814 in a worktree.

Answers that were right and stopped one step short:
1. The hint fluid-resource-uris says that the core covers EXT: paths in f:image "in SvgImageViewHelperTest, with width, height, crop and fileExtension". It does not give the path. I found it with ls in typo3/sysext/fluid/Tests/Functional/ViewHelpers/. One path in the hint saves that step, and the suite is the one that turned the review.
2. typo3_task_guide lists three checks: unit, functional, checkIntegrityPhp. typo3_test_run_guide then lists eighteen suites with the targeted invocation. The guide's three are the ones every patch needs; the test guide is where the answer is. The task guide says so in nextTools, and I read it there. A reader who stops at "checks" runs three suites.
3. typo3_test_run_guide names the cglGit trap in a worktree and the composerInstall precondition. It does not say that a Branchery worktree already carries vendor/ from the source checkout, and that the lock file can still differ. I compared the lock files myself: phpstan 2.2.10 against 2.2.14, then ran composerInstall. That step is the project's, not the server's, but the guide could say "compare composer.lock of the checkout against vendor/composer/installed.json first".
4. typo3_project_describe returned no commands marked check for a core checkout. Everything is unknown or change. So "run the declared checks" had nothing to run, and the test guide filled the gap. That is correct and worth a sentence in the answer: for a core checkout the suites are in typo3_test_run_guide.

What the client did not show: no resource list. I learned the document ids from the guides array of typo3_project_describe and from the skill text. The client loaded tool schemas only on a ToolSearch call; ten schemas in one call. No tool name misled me. The one term I searched in the changelog and the server spells otherwise: none. "fallback storage" returned nothing because no entry carries the words, and the answer said which of the two words matched how often. That was enough to stop.

Nothing went wrong: no error, no schema I had to guess, no call I could not complete. The changelog answer of zero features for 14.3 looked wrong to me and was right.

What the server saved me from, concretely: the fetch over ssh (the page said https serves the refs to anyone); running cglGit and reading its SUCCESS; a missing composerInstall; a Releases line I would have taken as the author's call; a ViewHelper test I would not have run before the diff.

## Query

typo3_task_guide task="Review a core patch that processes extension resource images ..." changeType=audit targetVersion=15 with 13 paths; typo3_hint_lookup id=fluid-resource-uris; typo3_test_run_guide with 9 paths targetVersion=15; typo3_project_describe.

## Suggestion

Put the path of a test the hint names into the hint. In typo3_test_run_guide, add one sentence for a provisioned worktree: compare the lock file against the copied vendor/ before the first suite. In typo3_project_describe, for a core checkout, say in the commands answer that the suites live in typo3_test_run_guide.
