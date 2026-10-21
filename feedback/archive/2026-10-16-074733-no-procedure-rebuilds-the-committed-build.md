---
date: 2026-10-16T07:47:33+00:00
category: missing-knowledge
status: closed
closed: 2026-10-21
model: claude-opus-5-5
tool: typo3-core-patch-checkout, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# no procedure rebuilds the committed build output in every commit of a rebased chain

## Observation

Task: rebase the 13-change chain 95425..95437 onto main and rebuild the build output in each commit.
The skill says a chain is a range, and that each later amend of a lower commit is two steps.
The page committed-build-output tells how to rebuild for one change in a worktree.
No page or skill tells how to rebuild each commit of a chain. I wrote that step myself.
I used git rebase --exec with a script. The script ran runTests.sh -s build, and restored Build/package-lock.json. It stopped on any change outside Resources/Public/JavaScript or Css. It amended HEAD only where the output changed.
Before that, I built origin/main once to prove that its output was clean. The page gave that idea for one change.
All 13 builds took about 40 seconds each. All 13 reproduced the committed output. git range-diff showed "=" for all 13.
The page rebasing-a-stale-patch says no rebuild is owed when main touched neither source nor output. That was true here. The user asked for the rebuild anyway.
The skill phrase "every later amend is two steps" points to manual detach and replay. rebase --exec removes that work for a chain.

## Query

Request: "bitte rebase mir die chain mal wieder und baue alle teile neu" for Gerrit 95437 (13 open links). Pages read: core/contribution/committed-build-output, core/contribution/rebasing-a-stale-patch.

## Suggestion

Add a section "Rebuilding every commit of a chain" to core/contribution/committed-build-output, or a page of its own. Give: build the target branch first and require a clean status; then git rebase --exec SCRIPT origin/main; what the script restores (package-lock.json), what it refuses (changes outside Resources/Public), and when it amends. Give the check afterwards: git range-diff OLD_BASE..OLD_TIP origin/main..HEAD. Name it in the chain section of typo3-core-patch-checkout.
