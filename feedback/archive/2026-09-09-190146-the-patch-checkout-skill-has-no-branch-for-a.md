---
date: 2026-09-09T19:01:46+00:00
category: missing-knowledge
status: closed
closed: 2026-09-09
model: claude-opus-5[1m]
tool: typo3-core-patch-checkout
directory: /home/benji/projects/typo3-cms
---

# the patch-checkout skill has no branch for a relation chain of more than one open change

## Observation

Task: rebase the Gerrit relation chain around change 93628 onto current main, in a worktree.

typo3-core-patch-checkout activated and fitted the shape of the work well - the four ways in, the "before the checkout is changed" preconditions, the conflict checklist and the put-it-back section all earned their place, and I followed them. One thing it does not carry, and it is what the request actually was.

The skill is built throughout on one change being one commit. "Every patch is exactly one commit." "A rebase of the fetched commit and a cherry-pick onto current code are one move under two names, because a core patch is exactly one commit." "One change, because the work needs it on disk", with a warning about fetching several refs into a working checkout. All of that is true per change and none of it covers a relation chain, which is the normal shape for a large refactoring and was the shape here.

What I had to work out unaided, in this order:

1. Read the chain array out of typo3_gerrit_lookup's answer and see which links are still open. Here: 93628 PS51 (NEW), on 93760 PS5 (NEW), on 93666 (MERGED). So the thing to rebase is two commits, not one.
2. Notice that the merged link is not an ancestor of main. git merge-base --is-ancestor said NO, because 93666 merged as PS23 while the chain still sits on its PS18 - the chain array's chainedAt field (18) against its patchSet field (23) says exactly this, and that difference predicted a good share of my later conflicts.
3. Choose the move. The skill's form is `git switch -c review/N origin/main` then `git cherry-pick FETCH_HEAD` for one commit. For a chain it is `git cherry-pick BASE..TIP` over the range, or equivalently `git rebase --onto origin/main BASE TIP`, with the branch still named for the topmost change.
4. Decide what the branch is named when it carries two changes. I kept the skill's review/NNNNN convention and used the tip change's number, review/93628, which matched the naming already in this checkout.
5. Decide what to do when a conflict resolution belongs to the lower commit. I hit this at the end: php-cs-fixer findings split across both commits, so I had to detach to HEAD~1, amend the lower one, re-cherry-pick the upper, amend that, and move the branch. Nothing warned me that a chain makes every later amend a two-step.

The moment a chain section would have had to open is the first minute of the session, on the word "chain" in the request and on the chain array in the first gerrit answer.

I would work all five out again next session, the same way, because none of it is written down.

One further thing the skill needs a sentence for. Its stopping rules - "more than a handful of hunks conflict, the patch is stale enough that its author rebasing it is the honest outcome", and the whole "resolving would produce a patch nobody wrote" frame - are written for somebody else's patch. Here the author WAS the person asking, verified from the change owner. I had 6 conflicting hunks in 5 files, over the "handful" line, and I resolved rather than stopped, on the reasoning that the rule exists to stop me authoring on an absent author's behalf and that reason is gone when the author is the requester. That reasoning I had to construct; the skill does not say whether it agrees.

## Query

User request, verbatim: "bitte rebase mir die chain auf main in einem ddev branchery worktree 93628: [WIP][TASK] ALLOW GD ONLY". Activated typo3-core-patch-checkout with that as args.
