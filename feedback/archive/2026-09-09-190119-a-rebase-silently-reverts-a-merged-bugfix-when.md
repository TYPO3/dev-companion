---
date: 2026-09-09T19:01:19+00:00
category: tool-gap
status: closed
closed: 2026-09-09
model: claude-opus-5[1m]
tool: typo3-core-patch-checkout, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# a rebase silently reverts a merged bugfix when the fixed code moved to a new file, and nothing ch...

## Observation

Task: rebase a 313-commit-stale Gerrit chain onto main and say whether it still holds.

The most valuable finding of this session was invisible to every conflict mechanism, and I reached it by accident.

main had merged 2661ddd16ab "[BUGFIX] Convert float precision in image crop command". Its production change was one line in GraphicalFunctions - wrapping the crop geometry in (int)round(...). The patch under rebase guts GraphicalFunctions (144 insertions, 757 deletions) and moves that command building into a new file, Classes/Imaging/Processor/AbstractBinaryProcessor.php. The patch's copy of the line predates the fix.

git reported no conflict. Different files. The cherry-pick was clean on that path. The rebased tree therefore silently reverts a merged bugfix, and the diff between the pushed patch set and the rebased result does not say so anywhere.

I only got there because the same bugfix's test - GraphicalFunctionsResizeTest, added on main, calling GraphicalFunctions::isProcessingEnabled(), a method the patch removes - errored in the functional run. I then traced the test back to its commit, read what else that commit changed, and grepped for '-crop' in the patch's new processor classes.

The generalisable hazard: when a patch rewrites or replaces a class, every fix merged into that class since the patch's base is at risk of being reverted, and the risk is proportional to how far the code moved. references/checklist.md covers textual collisions well. It has nothing for this, because there is no text to collide.

The cheaper half of the same finding I did do: for each public method the patch removes from a rewritten class, grep the tree for remaining callers. comm -23 between the method lists of the two versions gave 15 removed methods; exactly one, isProcessingEnabled, still had a caller, and it was a test main added after the patch was written. That was one call and it found a real breakage.

Suggested fix - a procedure page, core/contribution/rebasing-a-stale-patch, pointed to from the checkout skill's "Carry it onto current code" section. Its steps, as this session had to derive them:

1. git log --oneline BASE..origin/main -- EVERY-PATH-THE-CHANGE-TOUCHES. I ran this and it listed 36 commits; right starting point, but it does not separate a bugfix from a refactor.
2. For each file the change REWRITES or DELETES rather than edits, read main's commits to it since the base and ask of each whether its change survives in the patch's replacement. This is the step that catches a moved bugfix: git log -p BASE..origin/main -- REWRITTEN-FILE, then grep the distinctive expression in the new location.
3. For each public or protected member the change removes from a rewritten class, grep for remaining callers across typo3/sysext. Two method lists from grep -oP and one comm -23 is the whole of it.
4. Run the suites covering the REWRITTEN paths, not only the changed ones. GraphicalFunctionsResizeTest sits in the change's own directory but is in neither change's file list, and it is what exposed both problems.

typo3_test_run_guide already does step 4 for changed paths. What it cannot do is notice that a path the change guts or deletes has tests on main the change never touches. A rewrittenPaths argument that adds the suites covering them would have found both issues in the first functional run rather than the third.

## Query

Rebasing Gerrit 93628 (PS51, ~8200 insertions across typo3/sysext/core/Classes/Imaging, install, styleguide) from a base 313 commits behind onto current main, then CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- typo3/sysext/core/Tests/Functional/Imaging/
