---
date: 2026-09-09T19:02:27+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup, typo3_test_run_guide, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# what carried this session: the chain and mergeable fields, the worktree warnings, the gerrit page...

## Observation

Task: rebase the Gerrit chain around 93628 onto current main in a ddev worktree. Recording what worked, so it is not broken later.

1. typo3_gerrit_lookup's chain array is what made the session possible at all. The request said "the chain" and named one number; the chain array turned that into three concrete links with their statuses (93628 NEW PS51, 93760 NEW PS5, 93666 MERGED), which is exactly the set I had to act on. Two fields deserve naming because they did work no other source could:
   - mergeable. false on 93628, true on 93760. That predicted the outcome precisely: 93760 cherry-picked clean, 93628 conflicted in five files. Reading it first meant I never wondered whether a conflict was my mistake.
   - chainedAt against patchSet on the merged link. 93666 shows patchSet 23, chainedAt 18. That one-line difference is why the chain's base was not an ancestor of main and why several of my conflicts existed at all. Nothing in a checkout says it.
   Neither is advertised in the tool description, which mentions "relation chain" in passing. They earned top billing.

2. typo3_test_run_guide's worktree warnings saved me from a false green, twice over. It states that cglGit and cglHeaderGit take their file list from git inside the container, that a worktree's gitdir sits outside the mount, and that the suite therefore reports SUCCESS having read nothing - and names cgl -n as the substitute that asks git nothing. I was in a worktree and would otherwise have run cglGit and reported a green that inspected zero files. The same section's note that checkGruntClean runs git add over the whole working tree kept me from running it on a tree holding a half-finished rebase. Concretely: two commands not run, one false green not reported.

3. typo3_rule_lookup with documentId="core/contribution/gerrit-workflow", read whole rather than searched, carried the procedure end to end and sent me back to nothing. In one answer: the refs/changes/NN/CHANGE/PS form, the asymmetry that the change refs live on the review server while a core clone fetches from GitHub (I would have run git fetch origin refs/changes/... and got a confusing nothing), the review/NNNNN branch convention with the reasoning for it, the note that cherry-pick keeps the author and carries the Change-Id so a carried patch is a patch set on the same change, and the worktree section confirming the hooks live in the shared gitdir. That page is the single highest-value artefact I touched.

4. The guides list arrives inside typo3_project_describe rather than as a client resource list - my client (Claude Code) showed me no resource list at all, so that embedding is the only reason I knew the gerrit-workflow page existed to ask for by id. Keep it there.

5. typo3_project_describe's commands array, each marked check/change/unknown, is what let me pick cgl -n over cgl while the tree was mid-rebase, without reading Build/Scripts/runTests.sh.

One place a right answer stopped one step short. typo3_test_run_guide named checkGruntClean and explained why it cannot work in a worktree, then pointed at core/contribution/committed-build-output for the real procedure. The change touches Build/Sources/TypeScript/install/module/environment/image-processing.ts together with its committed .js, so the question "is the committed JavaScript still consistent after this rebase" was live. I did not read that page. I answered it myself with git log --oneline BASE..origin/main on both the .ts and the .js and found main had touched neither, so the committed pair was still internally consistent and no rebuild was owed. A single sentence in the test run guide - "the committed .js is stale only if main touched the same module since the patch's base; this git call settles it" - would have closed it without a page load.

## Query

typo3_gerrit_lookup change=93628 and change=93760; typo3_rule_lookup documentId="core/contribution/gerrit-workflow"; typo3_test_run_guide with the changed imaging paths. Task: rebase a stale Gerrit chain onto main in a worktree.
