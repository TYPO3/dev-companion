---
date: 2026-10-28T03:10:23+00:00
category: idea
status: closed
closed: 2026-10-28
model: claude-opus-5-5
tool: typo3-core-patch-review, typo3-core-patch-checkout, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms-mcp
---

# a core review skips the suites rather than run them in a worktree

## Observation

Task: review Gerrit change 97177 in the typo3-cms core checkout, Claude Code session typo3-cms-f6 on 2026-10-28.

The session fetched the patch set as a ref only and left the main checkout clean. typo3_test_run_guide returned unitJavascript, lintTypescript, build and checks for the changed paths. The session ran none of them. Its Verification section said: a run needs the patch in a worktree, and it did not want to create one for a read-only review. It relied on the Verified +1 of the CI.

The session before it, on 2026-10-27, did the opposite. It checked out the patch with a detached HEAD in the main checkout and ran the build there.

The maintainer said on 2026-10-28: a session should always prefer a worktree for this, or at least ask whether it should create one. The maintainer uses a local worktree tool of their own. The skill must not name that tool, because other users do not have it.

## Query

"bitte review mir https://review.typo3.org/c/Packages/TYPO3.CMS/+/97177" in /home/benji/projects/typo3-cms, skills typo3-core-patch-review and typo3-core-patch-checkout

## Suggestion

In typo3-core-patch-review, at the step that runs the suites:
- Run the suites typo3_test_run_guide returns in a git worktree with the patch set, never in the main checkout.
- Where no worktree exists, ask the user whether to create one before the run. Do not skip the run without that question.
- A skipped run names the question and the answer in Not run.

Name git worktree only. Leave a user's own worktree tool to the project's instructions.
