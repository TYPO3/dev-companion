---
date: 2026-09-09T18:42:32+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms/.worktrees/toplevel-access
---

# reading a change gives its file list and line counts but never a line of the patch

## Observation

Task: finish a WIP TypeScript refactoring in a TYPO3 core checkout; the specific step was the user asking "können wir noch was von franzke übernehmen was er besser macht?" — decide what to salvage from abandoned change 80556 for the live patch.

typo3_gerrit_lookup change='80556' messages='people' answered well and stopped one step short. It gave the full commit message, all 100 files with per-file insertions and deletions, both inline comments with thread state, the relation chain, the issues[] trailer, and every human review message including the abandon reason. From that alone I could see that the patch added Build/Sources/TypeScript/backend/utility/top-frame.ts at 83 insertions, and that severity.ts lost 31 lines, modal.ts 25, notification.ts 27, login-refresh.ts 33, multi-step-wizard.ts 41, wizard.ts 37.

Those deletion counts were the finding: four of those files in the current tree still carry a hand-rolled copy of the same top-detection-plus-singleton block, and the abandoned patch collapsed each of them to one line. But to answer "what does he do better" I needed the code, and the answer carries none of it. I ran git fetch against the review remote for refs/changes/56/80556/11, then five git show calls for the added module and its consumers. Five round trips outside the server to read a patch the server had just described in detail.

The two things I ended up recommending for adoption — a defineTopLevelInstance() helper with its memory-leak rule (write the instance back only into window.TYPO3, never into the top TYPO3, or a frame's objects can never be collected) and the frameInitializer callback that re-runs modal.initializeMarkupTrigger(document) per frame — are both invisible at the granularity of a file list. So is the mistake I found in the same patch and warned the user off: it rewrote login.ts's framebusting from top.location.href to topWindow.location.href, which silently disables the framebust in the cross-origin case it exists for.

This is not a blocker: the fetch was cheap and correct, and the answer's fetch.ref ("refs/changes/56/80556/11") and fetch.remote are exactly what git fetch needs — I used them rather than constructing the ref myself, which was a real convenience. The gap is only that there is no way to ask for content.

## Query

typo3_gerrit_lookup change='80556' messages='people' — then, outside the server: git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/56/80556/11 followed by five git show FETCH_HEAD:&lt;path&gt; / git show FETCH_HEAD -- &lt;path&gt; calls to read the added module and its consumers.

## Suggestion

Add a way to get content out of a change read. The cheap, common case is the added files: when the question is "what did this abandoned attempt build that we should reuse", it is nearly always the new module you want, and its size is already in the answer, so the cost is predictable. Something like content='added' returning the diff for files with action='added', or files='&lt;path&gt;[,&lt;path&gt;]' returning those blobs at that patch set.

If that stays out of scope, say in the description that fetch.ref is there to be handed to git fetch and that reading content is expected to happen in the checkout. That would make the two-step the documented route rather than something each session re-derives — and it is a good route, it just is not written down.
